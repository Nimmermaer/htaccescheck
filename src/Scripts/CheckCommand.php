<?php
declare (strict_types=1);

namespace Iwmedien;


use Iwmedien\Htaccescheck\Controller\PathController;

if (PHP_SAPI !== 'cli') {
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
    /**
     * @var string
     */
    public const HTTP_OK = "HTTP/1.1 200 OK";
    
    /**
     * @var string
     */
    public const HTTP_UNAUTHORIZED = "HTTP/1.1 401 Unauthorized";

    public function run(): void
    {
        $file = fopen((new PathController())->getPathToCSVFile(), 'r');
        while (($line = fgetcsv($file)) !== false) {

            if (filter_var($line[0], FILTER_VALIDATE_URL) && $http_response_header[0] !== self::HTTP_UNAUTHORIZED) {
                $message = sprintf(' Bitte Htaccess bei %s wieder einsetzen', $line[0]);
                mail(
                    'programmierung@iwkoeln.de',
                    $line[0] . ' ist ohne htaccess Schutz',
                    $message,
                    'From: Htaccesscheck <programmierung@iwkoeln.de> \n '
                );
            }
        }
        
        fclose($file);
    }
}