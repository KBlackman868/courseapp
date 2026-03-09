<?php

namespace App\Rules;

use Illuminate\Http\Request;

/**
 * Centralized password validation rules for the entire application.
 * Used in: Registration, Change Password, Reset Password, Admin Add User.
 */
class PasswordRules
{
    /**
     * Get the standard password validation rules array.
     *
     * @param Request|null $request  Used for the name/email check closure
     * @param bool         $requireConfirmation  Whether to include 'confirmed'
     * @return array
     */
    public static function rules(?Request $request = null, bool $requireConfirmation = true): array
    {
        $rules = [
            'required',
            'string',
            'min:12',
            'regex:/[A-Z]/',          // uppercase
            'regex:/[a-z]/',          // lowercase
            'regex:/[0-9]/',          // digit
            'regex:/[!@#$%^&*()\-_=+\[\]{}|;:\'",.?\/]/',  // special char
            'not_regex:/[\\\\~<>]/',  // forbidden chars
        ];

        if ($requireConfirmation) {
            $rules[] = 'confirmed';
        }

        // Name/email check closure
        if ($request) {
            $rules[] = function ($attribute, $value, $fail) use ($request) {
                $firstName = $request->input('first_name', '');
                $lastName = $request->input('last_name', '');
                $email = $request->input('email', '');

                $lowerPassword = strtolower($value);

                // Check name substrings (3-char windows)
                foreach ([$firstName, $lastName] as $name) {
                    if (!$name) continue;
                    $lowerName = strtolower($name);
                    for ($i = 0; $i <= strlen($lowerName) - 3; $i++) {
                        $part = substr($lowerName, $i, 3);
                        if (str_contains($lowerPassword, $part)) {
                            $fail('Password cannot contain parts of your name.');
                            return;
                        }
                    }
                }

                // Check email username
                if ($email) {
                    $emailUser = strtolower(explode('@', $email)[0]);
                    if (strlen($emailUser) >= 3 && str_contains($lowerPassword, $emailUser)) {
                        $fail('Password cannot contain your email username.');
                    }
                }
            };
        }

        return $rules;
    }

    /**
     * Custom error messages for password validation.
     */
    public static function messages(): array
    {
        return [
            'password.min' => 'Password must be at least 12 characters.',
            'password.regex' => 'Password does not meet complexity requirements.',
            'password.not_regex' => 'Password cannot contain the characters: \\ ~ < >',
        ];
    }
}
