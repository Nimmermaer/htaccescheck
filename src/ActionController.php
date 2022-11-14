<?php

namespace Iwmedien\Htaccescheck;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function Composer\Autoload\includeFile;

class ActionController
{

    protected Environment $view;
    
    protected Routing $route;

    public function __construct()
    {
        $this->route = new Routing();
    }

    private function initFrontend(): void
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../resources/templates');
        $this->view = new \Twig\Environment($loader, [
            'debug' => true,
            'cache' => '../var/cache',
        ]);
        $this->view->addExtension(new \Twig\Extension\DebugExtension());
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function run(): void
    {
        $arguments = $_REQUEST['arguments'] ?? [];
        $this->checkHtaccessPath();
        $this->routing($_SERVER, $arguments);
        $this->initFrontend();
        $paths = (new PathController())->getPaths();
        $this->view->display('index.html',  ['paths' => $paths]);
    }

    private function checkHtaccessPath(): void
    {
        if (!file_exists(dirname((string)$_SERVER['SCRIPT_FILENAME']) . '/.htaccess')) {
            echo "please set a htacces e.g templates/htaccess-template";
            exit;
        };
    }


    private function routing(array $server, array $arguments): void
    {
        $this->route::run($server, $arguments);
    }
}