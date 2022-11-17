<?php

namespace Iwmedien\Htaccescheck;


class Routing
{

    private static array $routes;

    private static $pathNotFound = null;

    private static $methodNotAllowed = null;

    public function __construct()
    {
        include_once __DIR__ . '/Configuration/Routes.php';
        $this::$routes = $GLOBALS['ROUTES'];
    }

    public static function pathNotFound($function): void
    {
        self::$pathNotFound = $function;
    }

    public static function methodNotAllowed($function): void
    {
        self::$methodNotAllowed = $function;
    }

    public static function run(array $server, array $arguments): void
    {

        // Parse current url
        $parsed_url = parse_url((string)$server['REQUEST_URI']);//Parse Uri

        $path = $parsed_url['path'] ?? '/';
        // Get current request method
        $method = $server['REQUEST_METHOD'];

        $path_match_found = false;

        $route_match_found = false;

        foreach (self::$routes as $route) {

            $route['expression'] = '(' . $path . ')';
            // Add 'find string start' automatically
            $route['expression'] = '^' . $route['expression'];

            // Add 'find string end' automatically
            $route['expression'] .= '$';

            // Check path match
            if (preg_match('#' . $route['expression'] . '#', $path, $matches)) {

                $path_match_found = true;
                // Check method match
                if (strtolower((string)$method) === strtolower((string)$route['method'])) {

                    array_shift($matches);// Always remove first element. This contains the whole string

                    $matches[] = $arguments;
                    call_user_func_array($route['function'], $matches);

                    $route_match_found = true;

                    // Do not check other routes
                    break;
                }
            }
        }

        // No matching route was found
        if (!$route_match_found) {

            // But a matching path exists
            if ($path_match_found) {
                header("HTTP/1.0 405 Method Not Allowed");
                if (self::$methodNotAllowed) {
                    call_user_func_array(self::$methodNotAllowed, [$path, $method]);
                }
            } else {
                header("HTTP/1.0 404 Not Found");
                if (self::$pathNotFound) {
                    call_user_func_array(self::$pathNotFound, [$path]);
                }
            }

        }

    }

}