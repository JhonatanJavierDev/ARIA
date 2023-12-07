<?php

namespace AriaRouter\Aria;

class Router
{
    private static array $routes = [];

    public static function get($route, $callback)
    {
        self::addRoute('GET', $route, $callback);
    }

    public static function post($route, $callback)
    {
        self::addRoute('POST', $route, $callback);
    }

    public static function put($route, $callback)
    {
        self::addRoute('PUT', $route, $callback);
    }

    public static function patch($route, $callback)
    {
        self::addRoute('PATCH', $route, $callback);
    }

    public static function delete($route, $callback)
    {
        self::addRoute('DELETE', $route, $callback);
    }

    public static function any($route, $callback)
    {
        self::addRoute('ANY', $route, $callback);
    }

    private static function addRoute($method, $route, $callback)
    {
        self::$routes[] = ['method' => $method, 'route' => $route, 'callback' => $callback];
    }

    public static function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = strtok(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '?');

        foreach (self::$routes as $route) {
            if (($route['method'] === 'ANY' || $route['method'] === $requestMethod) &&
                self::matchRoute($route['route'], $requestUri, $parameters)) {
                self::executeCallback($route['callback'], $parameters);
                exit();
            }
        }

        self::notFound();
    }

    private static function matchRoute($pattern, $uri, &$parameters)
    {
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $pattern);
        $pattern = "/^{$pattern}$/";

        if (preg_match($pattern, $uri, $matches)) {
            $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    private static function executeCallback($callback, $parameters)
    {
        if (is_callable($callback)) {
            call_user_func_array($callback, $parameters);
        } else {
            include_once __DIR__ . "/$callback";
        }
    }

    private static function notFound()
    {
        http_response_code(404);
        include_once __DIR__ . "/404.php";
        exit();
    }
}

function out($text)
{
    echo htmlspecialchars($text);
}

function set_csrf()
{
    session_start();
    if (!isset($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(50));
    }
    echo '<input type="hidden" name="csrf" value="' . $_SESSION["csrf"] . '">';
}

function is_csrf_valid()
{
    session_start();
    return isset($_SESSION['csrf']) && isset($_POST['csrf']) && ($_SESSION['csrf'] === $_POST['csrf']);
}
