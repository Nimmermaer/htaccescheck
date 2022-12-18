<?php

namespace Iwmedien\Htaccescheck\Controller;

use Iwmedien\Htaccescheck\Utilities\Routing;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ActionController
{
    /**
     * @var string
     */
    public const HTTP_OK = "HTTP/1.1 200 OK";
    
    /**
     * @var string
     */
    public const HTTP_UNAUTHORIZED = "HTTP/1.1 401 Unauthorized";
    
    protected Environment $view;

    protected Routing $route;

    public function __construct()
    {
        $this->route = new Routing();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function dashboard(): void
    {
        $this->checkHtaccessPath();
        $this->initFrontend();
        $paths = (new PathController())->getPaths();
        $this->view->display('templates/dashboard.html', ['paths' => $paths, 'messages' => $this->checkFeedback()]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function index(): void
    {
        $arguments = $_REQUEST['arguments'] ?? [];
        $this->checkHtaccessPath();
        $this->routing($_SERVER, $arguments);
        $this->initFrontend();
        $paths = (new PathController())->getPaths();
        $this->view->display('templates/index.html', ['paths' => $paths]);
    }

    protected function initFrontend(): void
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../resources/');
        $this->view = new \Twig\Environment($loader, [
            'debug' => true,
            'cache' => '../var/cache',
        ]);
        $this->view->addExtension(new \Twig\Extension\DebugExtension());
    }



    public function run(): void
    {
        try {
            match (ltrim ($_SERVER['REQUEST_URI'], '/')) {
                'dashboard' => self::dashboard(),
                 default => self::index()
            };
        } catch (\Exception $exception) {
            throw new \Exception('Looks like something go wrong: ' . $exception->getMessage());
        }

    }

    protected function checkHtaccessPath(): void
    {
        if (!file_exists(dirname((string)$_SERVER['SCRIPT_FILENAME']) . '/.htaccess')) {
            echo "please set a htacces e.g templates/htaccess-template";
            exit;
        };
    }


    protected function routing(array $server, array $arguments): void
    {
        $this->route::run($server, $arguments);
    }

    private function checkFeedback()
    {
        $messages = [];
        $file = fopen((new PathController())->getPathToCSVFile(), 'r');
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        while (($line = fgetcsv($file)) !== false) {

            if (filter_var($line[0], FILTER_VALIDATE_URL)) {
                file_get_contents((string)$line[0], false, $context);
                if ($http_response_header[0] !== self::HTTP_UNAUTHORIZED) {
                    $message = sprintf(' Bitte Htaccess bei %s wieder einsetzen', $line[0]);

                    $messages[] = $message;
                }
            }
        }
        
        fclose($file);
        return $messages;
    }
}