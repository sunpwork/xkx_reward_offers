<?php
return [
    'region'      => env('FIYSYSTEM_COS_REGION'),
    'credentials' => [
        'appId'      => env('FIYSYSTEM_COS_APPID'), // 域名中数字部分
        'secretId'   => env('FIYSYSTEM_COS_SERCETID'),
        'secretKey'  => env('FIYSYSTEM_COS_SERCETKEY'),
    ],
    'bucket'          => env('FIYSYSTEM_COS_BUCKET'),
    'timeout'         => 60,
    'connect_timeout' => 60,
    'cdn'             => env('FIYSYSTEM_COS_CND'),
    'scheme'          => 'https',
    'read_from_cdn'   => false,
];