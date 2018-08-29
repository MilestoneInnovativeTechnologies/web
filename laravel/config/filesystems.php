<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'tpa' => [
            'driver' => 'local',
            'root' => base_path('../public_html/third_party_applications'),
            'url' => env('APP_URL').'/third_party_applications',
            'visibility' => 'public',
        ],

        'ppo' => [
            'driver' => 'local',
            'root' => base_path('../public_html/public_print_objects'),
            'url' => env('APP_URL').'/public_print_objects',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'branding' => [
            'driver' => 'local',
            'root' => base_path('../public_html/branding'),
            'url' => env('APP_URL').'/branding',
            'visibility' => 'public',
        ],

        'printobject' => [
            'driver' => 'local',
            'root' => base_path('../public_html/printobject'),
            'url' => env('APP_URL').'/printobject',
            'visibility' => 'public',
        ],

        'generalupload' => [
            'driver' => 'local',
            'root' => base_path('../public_html/generalupload'),
            'url' => env('APP_URL').'/generalupload',
            'visibility' => 'public',
        ],

        'www' => [
            'driver' => 'local',
            'root' => base_path('../public_html'),
            'url' => env('APP_URL').'/',
            'visibility' => 'public',
        ],

        'resumes' => [
            'driver' => 'local',
            'root' => storage_path('app/resumes'),
        ],

    ],

];
