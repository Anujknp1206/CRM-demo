<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2', '3', '4', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'm', 'n', 'p', 'q', 'r', 't', 'u', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'M', 'N', 'P', 'Q', 'R', 'T', 'U', 'X', 'Y', 'Z'],
    'default' => [
        'length' => 5,
        'width' => 160,
        'height' => 50,
        'quality' => 100,
        'lines' => 2,
        'bgImage' => false,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'contrast' => 0,
        'angle' => 5,
        'sharpen' => 0,
        'blur' => 0,
        'math' => false,
    ],
    'math' => [
        'length' => 6,
        'width' => 150,
        'height' => 45,
        'quality' => 100,
        'math' => true,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'lines' => 1,
    ],

    'flat' => [
        'length' => 5,
        'width' => 150,
        'height' => 45,
        'quality' => 100,
        'lines' => 2, // 🔥 reduce lines
        'bgImage' => false,
        'bgColor' => '#ffffff', // 🔥 white background
        'fontColors' => ['#000000'], // 🔥 black text only
        'contrast' => 0,
        'angle' => 5, // 🔥 reduce rotation
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        'sharpen' => 10,
        'blur' => 2,
        'invert' => true,
        'contrast' => -5,
    ]
];
