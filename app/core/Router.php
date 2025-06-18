<?php

/**
 * Router Class
 *
 * Handles the registration and dispatching of routes in the application
 *
 * @package App\Core
 */
class Router
{
    /**
     * Stores all registered routes
     *
     * @var array<string, array<string, string>>
     */
    protected static array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => []
    ];

    /**
     * Error handler for 404 routes
     *
     * @var callable|null
     */
    protected static $notFoundHandler = null;

    /**
     * Register a GET route
     *
     * @param string $path The URL path
     * @param string $handler Controller@method string
     */
    public static function get(string $path, string $handler): void
    {
        self::$routes['GET'][self::formatRoute($path)] = $handler;
    }

    /**
     * Register a POST route
     *
     * @param string $path The URL path
     * @param string $handler Controller@method string
     */
    public static function post(string $path, string $handler): void
    {
        self::$routes['POST'][self::formatRoute($path)] = $handler;
    }

    /**
     * Set a handler for 404 Not Found errors
     *
     * @param callable $handler The handler function
     */
    public static function notFound(callable $handler): void
    {
        self::$notFoundHandler = $handler;
    }

    /**
     * Format the route by ensuring it starts with a forward slash
     * and doesn't end with one
     *
     * @param string $route The route to format
     * @return string
     */
    protected static function formatRoute(string $route): string
    {
        return '/' . trim($route, '/');
    }

    /**
     * Dispatch the request to the appropriate handler
     *
     * @return void
     */
    public static function dispatch(): void
    {
        // Get the URL from the request
        $url = isset($_GET['url']) ? parse_url($_GET['url'], PHP_URL_PATH) : '';

        // Get the HTTP method
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Clean the URL
        $cleanedUrl = self::formatRoute($url);

        // Try to match the route
        $foundRoute = self::match($method, $cleanedUrl);

        if ($foundRoute) {
            // Extract controller and action
            [$controller, $action] = explode('@', $foundRoute['handler']);

            // Check if controller exists
            if (!class_exists($controller)) {
                self::handleNotFound("Controller {$controller} not found");
                return;
            }

            // Create controller instance
            $controllerInstance = new $controller();

            // Check if method exists
            if (!method_exists($controllerInstance, $action)) {
                self::handleNotFound("Method {$action} not found in {$controller}");
                return;
            }

            // Call the controller method with parameters
            call_user_func_array([$controllerInstance, $action], $foundRoute['params']);
        } else {
            self::handleNotFound("No route found for {$method} {$cleanedUrl}");
        }
    }

    /**
     * Match a route to the request
     *
     * @param string $method The HTTP method
     * @param string $requestUrl The requested URL
     * @return array|false The matched route or false if no match
     */
    protected static function match(string $method, string $requestUrl)
    {
        // Check if method exists in routes
        if (!isset(self::$routes[$method])) {
            return false;
        }

        // Loop through all routes for this method
        foreach (self::$routes[$method] as $route => $handler) {
            // Extract parameter names from route definition
            $paramNames = [];
            if (preg_match_all('#\{([a-zA-Z0-9_]+)\}#', $route, $matches)) {
                $paramNames = $matches[1];
            }

            // Convert route to regex pattern
            $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([^/]+)', $route);

            // Try to match the route
            if (preg_match('#^' . $pattern . '$#', $requestUrl, $matches)) {
                // Remove the full match
                array_shift($matches);

                // Create named parameters array if parameter names exist
                $params = [];
                if (!empty($paramNames)) {
                    foreach ($paramNames as $index => $name) {
                        $params[$name] = $matches[$index] ?? null;
                    }
                } else {
                    $params = $matches;
                }

                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }

        return false;
    }

    /**
     * Handle 404 Not Found errors
     *
     * @param string $message Error message
     * @return void
     */
    protected static function handleNotFound(string $message = 'Page not found'): void
    {
        if (self::$notFoundHandler !== null) {
            call_user_func(self::$notFoundHandler, $message);
            return;
        }

        // Default 404 handler
        header("HTTP/1.0 404 Not Found");
        echo '<h1>404 Not Found</h1>';
        echo '<p>' . htmlspecialchars($message) . '</p>';
    }
}
