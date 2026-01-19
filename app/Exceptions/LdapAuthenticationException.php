<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class LdapAuthenticationException extends Exception
{
    public function __construct(
        string $message = "LDAP authentication failed",
        private string $errorCode = 'ldap_auth_error',
        private ?string $username = null,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function context(): array
    {
        return [
            'error_code' => $this->errorCode,
            'username' => $this->username,
        ];
    }
}
