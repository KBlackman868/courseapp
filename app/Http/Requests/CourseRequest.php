<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['admin', 'superadmin', 'instructor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'title')->ignore($courseId),
            ],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['sometimes', 'in:active,inactive,draft'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'moodle_course_id' => ['nullable', 'integer'],
            'moodle_course_shortname' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a course title.',
            'title.unique' => 'A course with this title already exists.',
            'title.max' => 'Course title cannot exceed 255 characters.',
            'description.required' => 'Please enter a course description.',
            'description.max' => 'Course description cannot exceed 5000 characters.',
            'category_id.exists' => 'The selected category does not exist.',
            'status.in' => 'Invalid course status.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'image.max' => 'The image size cannot exceed 2MB.',
        ];
    }
}
