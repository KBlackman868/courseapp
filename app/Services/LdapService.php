<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LdapService
{
    private $connection = null;
    private bool $enabled = false;
    private array $internalDomains = ['health.gov.tt', 'moh.gov.tt'];
    private array $config = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Safely load configuration
     */
    private function loadConfig(): void
    {
        try {
            $ldapConfig = config('ldap');
            
            // Check if config is actually an array
            if (is_array($ldapConfig)) {
                $this->config = $ldapConfig;
                $this->enabled = !empty($ldapConfig['enabled']) && $ldapConfig['enabled'] === true;
                
                if (isset($ldapConfig['internal_domains']) && is_array($ldapConfig['internal_domains'])) {
                    $this->internalDomains = $ldapConfig['internal_domains'];
                }
            } else {
                // Config doesn't exist or isn't an array - use defaults
                $this->enabled = false;
                $this->config = $this->getDefaultConfig();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to load LDAP config, using defaults', ['error' => $e->getMessage()]);
            $this->enabled = false;
            $this->config = $this->getDefaultConfig();
        }
    }

    /**
     * Get default configuration
     */
    private function getDefaultConfig(): array
    {
        return [
            'enabled' => false,
            'host' => '',
            'port' => 389,
            'ssl' => false,
            'tls' => false,
            'timeout' => 5,
            'version' => 3,
            'admin_dn' => '',
            'admin_password' => '',
            'base_dn' => '',
            'user_dn' => '',
            'attributes' => [
                'username' => 'sAMAccountName',
                'email' => 'mail',
                'first_name' => 'givenName',
                'last_name' => 'sn',
                'department' => 'department',
                'guid' => 'objectGUID',
            ],
            'groups' => [
                'course_creators' => '',
                'admins' => '',
            ],
            'internal_domains' => ['health.gov.tt', 'moh.gov.tt','test-dc.gov.tt'],
        ];
    }

    /**
     * Check if LDAP is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) config('ldap.enabled', false);
    }

    /**
     * Check if email domain is internal (MOH)
     */
    public function isInternalDomain(string $email): bool
    {
        if (empty($email) || strpos($email, '@') === false) {
            return false;
        }
        
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }
        
        $domain = strtolower(trim($parts[1]));
        
        foreach ($this->internalDomains as $internalDomain) {
            if (strtolower(trim($internalDomain)) === $domain) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get config value safely
     */
    private function getConfigValue(string $key, $default = null)
    {
        if (!is_array($this->config)) {
            return $default;
        }
        
        // Handle nested keys like 'attributes.username'
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

/**
 * Connect to LDAP server
 */
public function connect(): bool
{
    if (!$this->isEnabled()) {
        Log::info('LDAP is disabled');
        return false;
    }

    if (!function_exists('ldap_connect')) {
        Log::error('PHP LDAP extension is not installed');
        return false;
    }

    $host = config('ldap.host', '');
    $port = config('ldap.port', 389);
    
    Log::info('LDAP connecting', ['host' => $host, 'port' => $port]);
    
    if (empty($host)) {
        Log::error('LDAP host is not configured');
        return false;
    }

    $this->connection = @ldap_connect("ldap://{$host}", $port);

    if (!$this->connection) {
        Log::error('Failed to connect to LDAP server');
        return false;
    }

    // Set LDAP options
    ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, config('ldap.timeout', 5));

    Log::info('Successfully connected to LDAP server');
    return true;
}

    /**
     * Bind with admin credentials
     */
    public function bindAdmin(): bool
    {
        if (!$this->connection) {
            if (!$this->connect()) {
                return false;
            }
        }

        $adminDn = $this->getConfigValue('admin_dn', '');
        $adminPassword = $this->getConfigValue('admin_password', '');

        if (empty($adminDn) || empty($adminPassword)) {
            Log::error('LDAP admin credentials not configured');
            return false;
        }

        $bind = @ldap_bind($this->connection, $adminDn, $adminPassword);

        if (!$bind) {
            Log::error('Failed to bind as admin', [
                'admin_dn' => $adminDn,
                'error' => ldap_error($this->connection)
            ]);
            return false;
        }

        return true;
    }

    /**
     * Authenticate user against LDAP
     */
/**
 * Authenticate user against LDAP using direct bind
 */
public function authenticate(string $username, string $password): ?array
{
    if (!$this->isEnabled()) {
        Log::warning('LDAP is disabled');
        return null;
    }

    if (!$this->connect()) {
        Log::warning('LDAP connection failed');
        return null;
    }

    // Extract username if full email provided
    $usernameOnly = $username;
    if (strpos($username, '@') !== false) {
        $parts = explode('@', $username);
        $usernameOnly = $parts[0];
    }

    // Direct bind with UPN format
    $bindDn = "{$usernameOnly}@test-dc.gov.tt";
    
    $bound = @ldap_bind($this->connection, $bindDn, $password);

    if (!$bound) {
        Log::warning('LDAP authentication failed', [
            'email' => $username,
            'error' => ldap_error($this->connection)
        ]);
        return null;
    }

    Log::info('LDAP authentication successful', ['username' => $usernameOnly]);

    // Try to get user details
    $baseDn = config('ldap.base_dn', 'DC=test-DC,DC=gov,DC=tt');
    $filter = "(sAMAccountName={$usernameOnly})";
    
    $search = @ldap_search($this->connection, $baseDn, $filter);
    
    if ($search) {
        $entries = @ldap_get_entries($this->connection, $search);
        
        if (is_array($entries) && $entries['count'] > 0) {
            $entry = $entries[0];
            
            return [
                'dn' => $entry['dn'] ?? null,
                'username' => $entry['samaccountname'][0] ?? $usernameOnly,
                'email' => $entry['mail'][0] ?? $entry['userprincipalname'][0] ?? "{$usernameOnly}@test-dc.gov.tt",
                'first_name' => $entry['givenname'][0] ?? '',
                'last_name' => $entry['sn'][0] ?? '',
                'department' => $entry['department'][0] ?? 'Ministry of Health',
                'guid' => isset($entry['objectguid'][0]) ? $this->convertGuidToString($entry['objectguid'][0]) : null,
                'is_course_creator' => false,
            ];
        }
    }

    // Fallback - return basic info if search fails
    return [
        'username' => $usernameOnly,
        'email' => "{$usernameOnly}@test-dc.gov.tt",
        'first_name' => '',
        'last_name' => '',
        'department' => 'Ministry of Health',
        'guid' => null,
        'is_course_creator' => false,
    ];
}

    /**
     * Find user DN by username or email
     */
    private function findUserDn(string $identifier): ?string
    {
        $baseDn = $this->getConfigValue('user_dn', '');
        $usernameAttr = $this->getConfigValue('attributes.username', 'sAMAccountName');
        $emailAttr = $this->getConfigValue('attributes.email', 'mail');

        if (empty($baseDn)) {
            Log::error('LDAP user DN not configured');
            return null;
        }

        $filter = "(|({$usernameAttr}={$identifier})({$emailAttr}={$identifier}))";

        $search = @ldap_search($this->connection, $baseDn, $filter, ['dn']);

        if (!$search) {
            Log::error('LDAP search failed', [
                'filter' => $filter,
                'error' => ldap_error($this->connection)
            ]);
            return null;
        }

        $entries = ldap_get_entries($this->connection, $search);

        if (!is_array($entries) || $entries['count'] === 0) {
            return null;
        }

        return $entries[0]['dn'] ?? null;
    }

    /**
     * Get user attributes from LDAP
     */
    private function getUserAttributes(string $userDn): array
    {
        $attributes = $this->getConfigValue('attributes', []);
        if (!is_array($attributes)) {
            $attributes = [];
        }
        
        $attrs = array_values($attributes);
        $attrs[] = 'memberOf';

        $search = @ldap_read($this->connection, $userDn, '(objectClass=*)', $attrs);

        if (!$search) {
            return [];
        }

        $entries = ldap_get_entries($this->connection, $search);

        if (!is_array($entries) || $entries['count'] === 0) {
            return [];
        }

        $entry = $entries[0];

        $result = [
            'dn' => $userDn,
            'username' => $entry[strtolower($attributes['username'] ?? 'samaccountname')][0] ?? null,
            'email' => $entry[strtolower($attributes['email'] ?? 'mail')][0] ?? null,
            'first_name' => $entry[strtolower($attributes['first_name'] ?? 'givenname')][0] ?? null,
            'last_name' => $entry[strtolower($attributes['last_name'] ?? 'sn')][0] ?? null,
            'department' => $entry[strtolower($attributes['department'] ?? 'department')][0] ?? null,
            'guid' => $this->convertGuidToString($entry[strtolower($attributes['guid'] ?? 'objectguid')][0] ?? null),
            'groups' => $entry['memberof'] ?? [],
        ];

        $courseCreatorsGroup = $this->getConfigValue('groups.course_creators', '');
        $result['is_course_creator'] = $this->isGroupMember($result['groups'], $courseCreatorsGroup);

        return $result;
    }

    /**
     * Check if user is member of a specific group
     */
    private function isGroupMember($groups, string $groupDn): bool
    {
        if (!is_array($groups) || empty($groupDn)) {
            return false;
        }

        $groupDnLower = strtolower($groupDn);

        foreach ($groups as $key => $group) {
            if ($key === 'count' || !is_string($group)) continue;
            if (strtolower($group) === $groupDnLower) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert binary GUID to string format
     */
    private function convertGuidToString(?string $binaryGuid): ?string
    {
        if (!$binaryGuid) {
            return null;
        }

        $hex = bin2hex($binaryGuid);
        
        if (strlen($hex) < 32) {
            return null;
        }
        
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 6, 2) . substr($hex, 4, 2) . substr($hex, 2, 2) . substr($hex, 0, 2),
            substr($hex, 10, 2) . substr($hex, 8, 2),
            substr($hex, 14, 2) . substr($hex, 12, 2),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    /**
     * Find or create user from LDAP data
     */
    public function findOrCreateUser(array $ldapData): ?User
    {
        if (empty($ldapData['email'])) {
            Log::error('Cannot create user: no email in LDAP data');
            return null;
        }

        $user = User::where('email', $ldapData['email'])->first();

        if ($user) {
            $user->update([
                'first_name' => $ldapData['first_name'] ?? $user->first_name,
                'last_name' => $ldapData['last_name'] ?? $user->last_name,
                'department' => $ldapData['department'] ?? $user->department,
                'ldap_guid' => $ldapData['guid'] ?? null,
                'ldap_username' => $ldapData['username'] ?? null,
                'ldap_synced_at' => now(),
                'user_type' => 'internal',
                'auth_method' => 'ldap',
                'is_course_creator' => $ldapData['is_course_creator'] ?? $user->is_course_creator ?? false,
            ]);

            Log::info('Updated existing user from LDAP', ['user_id' => $user->id]);
        } else {
            $user = User::create([
                'email' => $ldapData['email'],
                'first_name' => $ldapData['first_name'] ?? '',
                'last_name' => $ldapData['last_name'] ?? '',
                'department' => $ldapData['department'] ?? 'Ministry of Health',
                'password' => Hash::make(Str::random(32)),
                'ldap_guid' => $ldapData['guid'] ?? null,
                'ldap_username' => $ldapData['username'] ?? null,
                'ldap_synced_at' => now(),
                'user_type' => 'internal',
                'auth_method' => 'ldap',
                'is_course_creator' => $ldapData['is_course_creator'] ?? false,
                'email_verified_at' => now(),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('user');
            }

            Log::info('Created new user from LDAP', ['user_id' => $user->id]);
        }

        return $user;
    }

    /**
     * Close LDAP connection
     */
    public function disconnect(): void
    {
        if ($this->connection) {
            @ldap_unbind($this->connection);
            $this->connection = null;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}