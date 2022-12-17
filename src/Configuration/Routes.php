<?php


use Iwmedien\Htaccescheck\Controller\PathController;

$GLOBALS['ROUTES'] = [
    [
        'expression' => '/addPath',
        'function' => static function (string $call, array $arguments): void {
            (new PathController())->addPath($arguments);
        },
        'method' => 'post'
    ],
    [
        'expression' => '/getPaths',
        'function' => static function (string $call, array $arguments): void {
            (new PathController())->getPaths();
        },
        'method' => 'post'
    ],
    [
        'expression' => '/removePath',
        'function' => static function (string $call, array $arguments) {
            (new PathController())->removePath($arguments);
        },
        'method' => 'get'
    ],
];