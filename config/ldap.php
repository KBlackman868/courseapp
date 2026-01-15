<?php

return [
    'enabled' => env('LDAP_ENABLED', false),

    'host' => env('LDAP_HOST', 'test-DC.gov.tt'),
    'port' => (int) env('LDAP_PORT', 389),
    'ssl' => env('LDAP_SSL', false),
    'tls' => env('LDAP_TLS', false),
    'timeout' => (int) env('LDAP_TIMEOUT', 5),
    'version' => (int) env('LDAP_VERSION', 3),

    'admin_dn' => env('LDAP_ADMIN_DN', ''),
    'admin_password' => env('LDAP_ADMIN_PASSWORD', ''),

    'base_dn' => env('LDAP_BASE_DN', 'DC=test-DC,DC=gov,DC=tt'),
    'user_dn' => env('LDAP_USER_DN', 'DC=test-DC,DC=gov,DC=tt'),

    'attributes' => [
        'username' => env('LDAP_ATTR_USERNAME', 'sAMAccountName'),
        'email' => env('LDAP_ATTR_EMAIL', 'mail'),
        'first_name' => env('LDAP_ATTR_FIRST_NAME', 'givenName'),
        'last_name' => env('LDAP_ATTR_LAST_NAME', 'sn'),
        'department' => env('LDAP_ATTR_DEPARTMENT', 'department'),
        'guid' => env('LDAP_ATTR_GUID', 'objectGUID'),
    ],

    'groups' => [
        'course_creators' => env('LDAP_GROUP_COURSE_CREATORS', ''),
        'admins' => env('LDAP_GROUP_ADMINS', ''),
    ],

    'internal_domains' => [
        'test-dc.gov.tt',
        'health.gov.tt',
        'moh.gov.tt',
    ],
];