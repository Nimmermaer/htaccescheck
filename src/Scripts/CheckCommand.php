<?php
declare (strict_types=1);

namespace Iwmedien;

if (php_sapi_name() !== 'cli') {
    exit;
}

$root_app = dirname(__DIR__);

if (!is_file($root_app . '/vendor/autoload.php')) {
    $root_app = dirname(__DIR__, 4);
}
require $root_app . '/vendor/autoload.php';

$command = new CheckCommand();
$command->run();

final class CheckCommand
{
    public function run()
    {
        echo 'Hello World';
    }
}