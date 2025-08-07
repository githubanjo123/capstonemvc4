<?php

namespace App\Core;

class Router
{
    private $routes = [];

    /**
     * Add a GET route
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Add a POST route
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Add routes for all HTTP methods
     */
    public function any($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
        $this->routes['POST'][$path] = $callback;
        $this->routes['PUT'][$path] = $callback;
        $this->routes['DELETE'][$path] = $callback;
    }

    /**
     * Handle the current request
     */
    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash
        $path = rtrim($path, '/');
        if (empty($path)) {
            $path = '/';
        }

        // Check if route exists
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            $this->executeCallback($callback);
        } else {
            // Check for parameterized routes
            $matchedRoute = $this->findParameterizedRoute($method, $path);
            if ($matchedRoute) {
                $this->executeCallback($matchedRoute['callback'], $matchedRoute['params']);
            } else {
                $this->notFound();
            }
        }
    }

    /**
     * Find parameterized route
     */
    private function findParameterizedRoute($method, $path)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = $this->convertRouteToPattern($route);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove the full match
                return [
                    'callback' => $callback,
                    'params' => $matches
                ];
            }
        }

        return null;
    }

    /**
     * Convert route to regex pattern
     */
    private function convertRouteToPattern($route)
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute callback with parameters
     */
    private function executeCallback($callback, $params = [])
    {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            // Handle Controller@method format
            list($controller, $method) = explode('@', $callback);
            
            if (class_exists($controller)) {
                $instance = new $controller();
                if (method_exists($instance, $method)) {
                    call_user_func_array([$instance, $method], $params);
                } else {
                    $this->notFound();
                }
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    /**
     * Handle 404 Not Found
     */
    private function notFound()
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Route not found.'
        ]);
    }

    /**
     * Get all registered routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}