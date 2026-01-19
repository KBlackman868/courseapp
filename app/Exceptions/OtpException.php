<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class OtpException extends Exception
{
    public const INVALID_OTP = 'invalid_otp';
    public const EXPIRED_OTP = 'expired_otp';
    public const MAX_ATTEMPTS_EXCEEDED = 'max_attempts_exceeded';
    public const SEND_FAILED = 'send_failed';
    public const RATE_LIMITED = 'rate_limited';

    public function __construct(
        string $message = "OTP verification failed",
        private string $errorCode = 'otp_error',
        private ?int $userId = null,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function context(): array
    {
        return [
            'error_code' => $this->errorCode,
            'user_id' => $this->userId,
        ];
    }

    public static function invalid(int $userId): self
    {
        return new self(
            "The verification code is invalid",
            self::INVALID_OTP,
            $userId
        );
    }

    public static function expired(int $userId): self
    {
        return new self(
            "The verification code has expired",
            self::EXPIRED_OTP,
            $userId
        );
    }

    public static function maxAttemptsExceeded(int $userId): self
    {
        return new self(
            "Maximum verification attempts exceeded",
            self::MAX_ATTEMPTS_EXCEEDED,
            $userId
        );
    }

    public static function rateLimited(int $userId): self
    {
        return new self(
            "Please wait before requesting a new code",
            self::RATE_LIMITED,
            $userId
        );
    }
}
