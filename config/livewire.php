<?php

return [
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMP_DISK', 'public'),
        'directory' => env('LIVEWIRE_TEMP_DIR', 'livewire-tmp'),
        // max in kilobytes (default 12288 => 12MB)
        'rules' => ['required', 'file', 'max:5120'], // 5 MB
        'max_upload_time' => 5,
        'cleanup' => true,
        'middleware' => 'throttle:60,1',
    ],
];
