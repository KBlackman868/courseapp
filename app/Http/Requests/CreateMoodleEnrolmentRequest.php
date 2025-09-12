<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateMoodleEnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'moodle_course_id' => 'required_without:course_id|integer|min:1',
            'course_id' => 'required_without:moodle_course_id|integer|exists:courses,id',
            'role_id' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User not found',
            'moodle_course_id.required_without' => 'Either moodle_course_id or course_id is required',
            'course_id.required_without' => 'Either course_id or moodle_course_id is required',
            'course_id.exists' => 'Course not found',
            'role_id.integer' => 'Role ID must be an integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}