<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
     */

    'default' => 'oss',

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

    'cloud'   => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
     */

    'disks'   => array(

        'local'  => array(
            'driver' => 'local',
            'root'   => storage_path('app'),
        ),

        'public' => array(
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'visibility' => 'public',
        ),

        's3'     => array(
            'driver' => 's3',
            'key'    => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ),

        'oss'    => array(
            'driver'     => 'oss',
            'access_id'  => env('OSS_ACCESS_ID'),
            'access_key' => env('OSS_ACCESS_KEY'),
            'bucket'     => env('OSS_FILE_BUCKET'),
            'endpoint'   => env('OSS_ENDPOINT'),
            'isCName'    => true,
            'debug'      => '<true|false>',
        ),
    ),

);
