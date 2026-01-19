<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class EnrollmentException extends Exception
{
    public const ALREADY_ENROLLED = 'already_enrolled';
    public const COURSE_NOT_FOUND = 'course_not_found';
    public const USER_NOT_FOUND = 'user_not_found';
    public const INVALID_STATUS = 'invalid_status';
    public const MOODLE_SYNC_FAILED = 'moodle_sync_failed';
    public const UNAUTHORIZED = 'unauthorized';

    public function __construct(
        string $message = "Enrollment operation failed",
        private string $errorCode = 'enrollment_error',
        private ?int $userId = null,
        private ?int $courseId = null,
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

    public function getCourseId(): ?int
    {
        return $this->courseId;
    }

    public function context(): array
    {
        return [
            'error_code' => $this->errorCode,
            'user_id' => $this->userId,
            'course_id' => $this->courseId,
        ];
    }

    public static function alreadyEnrolled(int $userId, int $courseId): self
    {
        return new self(
            "User is already enrolled in this course",
            self::ALREADY_ENROLLED,
            $userId,
            $courseId
        );
    }

    public static function courseNotFound(int $courseId): self
    {
        return new self(
            "Course not found",
            self::COURSE_NOT_FOUND,
            courseId: $courseId
        );
    }

    public static function moodleSyncFailed(int $userId, int $courseId, string $reason): self
    {
        return new self(
            "Failed to sync enrollment to Moodle: {$reason}",
            self::MOODLE_SYNC_FAILED,
            $userId,
            $courseId
        );
    }
}
