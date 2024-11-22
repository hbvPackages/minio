<?php


return [
    'minio' => [
        'driver' => 's3',
        'endpoint' => env('MINIO_ENDPOINT'),
        'use_path_style_endpoint' => true,
        'key' => env('MINIO_ACCESS_KEY'),
        'secret' => env('MINIO_SECRET_KEY'),
        'region' => env('MINIO_REGION'),
        'bucket' => env('MINIO_BUCKET'),
    ],
];
