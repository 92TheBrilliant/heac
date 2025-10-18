<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Upload Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security settings for file uploads in the application.
    |
    */

    'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 10485760), // 10MB in bytes

    'allowed_mime_types' => [
        'images' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ],
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ],
    ],

    'blocked_extensions' => [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr',
        'js', 'vbs', 'wsf', 'wsh',
        'sh', 'bash',
    ],

    'scan_uploads' => env('SCAN_UPLOADS', false), // Enable virus scanning if available

    'storage_disk' => env('UPLOAD_STORAGE_DISK', 'public'),

];
