<?php


$GLOBALS['ROUTES'] = [
    [
        'expression' => '/addPath',
        'function' => static function (string $call, array $arguments) {
            (new \Iwmedien\Htaccescheck\PathController())->addPath($arguments);
        },
        'method' => 'post'
    ],
    [
        'expression' => '/getPaths',
        'function' => static function (string $call, array $arguments) {
            (new \Iwmedien\Htaccescheck\PathController())->getPaths();
        },
        'method' => 'post'
    ],
    [
        'expression' => '/removePath',
        'function' => static function (string $call, array $arguments) {
            (new \Iwmedien\Htaccescheck\PathController())->removePath($arguments);
        },
        'method' => 'get'
    ],
];