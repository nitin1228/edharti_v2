<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2', '3', '4', '6', '7', '8', '9', 'a', 'b', 'c','2', '3', '4', '6', '7', '8', '9', 'd', 'e','2', '3', '4', '6', '7', '8', '9', 'f', 'g', 'h', 'j', 'm', '2', '3', '4', '6', '7', '8', '9','n', 'p', 'q', 'r', 't', 'u', '2', '3', '4', '6', '7', '8', '9','x', 'y', 'z', 'A', 'B','2', '3', '4', '6', '7', '8', '9', 'C', 'D', 'E', 'F', 'G', '2', '3', '4', '6', '7', '8', '9','H', 'J', 'M', '2', '3', '4', '6', '7', '8', '9','N', 'P', 'Q', 'R','2', '3', '4', '6', '7', '8', '9', 'T', 'U', 'X', 'Y', 'Z'],

    // 'default' => [
    //     'length' => 6,
    //     'width' => 200,
    //     'height' => 42,
    //     'quality' => 90,
    //     'math' => false,
    //     'expire' => 120,
    //     'encrypt' => false,
    // ],
    'default' => [
    'length' => 6,
    'width' => 200,
    'height' => 42,
    'quality' => 90,
    'math' => false,
    'expire' => 750,
    'encrypt' => false,    
    'bgColor' => '#ffffff',
    'backgrounds' =>[
        public_path('storage/captcha/backgrounds/01.png')
    ],
    'fontColors' => ['#000000'],
    'lines' => 0,
    'amgles' => 0,
    'contrast' => 0,
    'blur' => 0,
    'sharpen' => 0,

],
    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'lines' => 0,
            'expire' => 750,

    ],

    'flat' => [
        'length' => 6,
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'bgImage' => false,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'lines' => 0,
        'contrast' => 0,
            'expire' => 750,

    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'lines' => 0,
            'expire' => 750,

    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 0,
        'sharpen' => 0,
        'blur' => 0,
        'invert' => false,
        'contrast' => 0,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'lines' => 0,
            'expire' => 750,

    ]
];
