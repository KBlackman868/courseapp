<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Moodle Base Configuration
    |--------------------------------------------------------------------------
    */
    'base_url' => env('MOODLE_BASE_URL', 'https://learnabouthealth.hin.gov.tt'), // No default URL
    'token' => env('MOODLE_TOKEN', ''), // No default token
    'format' => env('MOODLE_FORMAT', 'json'),
    'timeout' => env('MOODLE_TIMEOUT', 30),
    'retry_times' => env('MOODLE_RETRY_TIMES', 2),
    'retry_sleep' => env('MOODLE_RETRY_SLEEP', 200),
    'verify_ssl' => env('MOODLE_VERIFY_SSL', true),
    'auto_approve_enrollments' => env('MOODLE_AUTO_APPROVE_ENROLLMENTS', false),
    'auto_create_users' => env('MOODLE_AUTO_CREATE_USERS', true),
    
    // Email settings for Moodle credentials
    'send_credentials_email' => env('MOODLE_SEND_CREDENTIALS_EMAIL', true),
    
    /*
    |--------------------------------------------------------------------------
    | Role IDs (Standard Moodle roles)
    |--------------------------------------------------------------------------
    */
    'default_student_role_id' => env('MOODLE_DEFAULT_STUDENT_ROLE_ID', 5),
    'default_teacher_role_id' => env('MOODLE_DEFAULT_TEACHER_ROLE_ID', 3),
    'default_category_id' => env('MOODLE_DEFAULT_CATEGORY_ID', 10), // LMS Support
    
    /*
    |--------------------------------------------------------------------------
    | Course Defaults
    |--------------------------------------------------------------------------
    */
    'course_defaults' => [
        'format' => env('MOODLE_COURSE_FORMAT', 'topics'),
        'showgrades' => env('MOODLE_SHOW_GRADES', 1),
        'newsitems' => env('MOODLE_NEWS_ITEMS', 5),
        'maxbytes' => env('MOODLE_MAX_BYTES', 0),
        'showreports' => env('MOODLE_SHOW_REPORTS', 0),
        'visible' => env('MOODLE_COURSE_VISIBLE', 1),
        'groupmode' => env('MOODLE_GROUP_MODE', 0),
        'groupmodeforce' => env('MOODLE_GROUP_MODE_FORCE', 0),
        'defaultgroupingid' => env('MOODLE_DEFAULT_GROUPING_ID', 0),
        'lang' => env('MOODLE_COURSE_LANG', 'en'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Ministry of Health Moodle Categories
    |--------------------------------------------------------------------------
    | Based on your Moodle instance categories
    */
    'categories' => [
        'lms_support' => 10,
        'sandboxes' => 14,
        'office_productivity' => 27,
        'hiv_training' => 2,
        'hiv_testing' => 23,
        'hcw_education' => 24,
        'ipc' => 22, // Infection Prevention and Control
        'monitoring_evaluation' => 26,
        'job_aids' => 25,
        'capacity_building' => 18,
    ],
];