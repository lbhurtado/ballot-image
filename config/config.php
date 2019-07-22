<?php

return [
    'zbar' => [
        'path' => '/usr/local/bin/'
    ],
    'files' => [
        'image' => [
            'seed' => env('IMAGE_SEED', 'tests/files/image.jpg'),
            'source' => env('BALLOT_SOURCE', storage_path('app/public/ballot.jpg')),
            'destination' => env('BALLOT_DESTINATION', storage_path('app/public/ballot.jpg')),
//            'qrcode' => env('BALLOT_QRCODE', storage_path('app/qrcode.jpg')),
        ],
        'temp' => storage_path('app')
    ],
];
