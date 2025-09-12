<?php

return [
    'base_url' => env('MOODLE_BASE_URL', 'https://moodle.example.com'),
    'token' => env('MOODLE_TOKEN', ''),
    'format' => env('MOODLE_FORMAT', 'json'),
    'default_student_role_id' => env('MOODLE_DEFAULT_STUDENT_ROLE_ID', 5),
    'timeout' => env('MOODLE_TIMEOUT', 20),
    'retry_times' => env('MOODLE_RETRY_TIMES', 2),
    'retry_sleep' => env('MOODLE_RETRY_SLEEP', 200),
    'verify_ssl' => env('MOODLE_VERIFY_SSL', true), 
    
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
    
    'categories' => [
        'miscellaneous' => 1,
        'computer_science' => 2,
        'mathematics' => 3,
        'business' => 4,
        'science' => 5,
        'languages' => 6,
    ],
];