<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method: string, path: string, handler: string, middleware: array}> */
    private array $routes = [];

    private string $groupPrefix = '';
    /** @var array<string> */
    private array $groupMiddleware = [];

    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    /** @param callable(Router): void $callback */
    public function group(array $options, callable $callback): void
    {
        $prevPrefix = $this->groupPrefix;
        $prevMw = $this->groupMiddleware;
        $this->groupPrefix .= $options['prefix'] ?? '';
        $this->groupMiddleware = array_merge($this->groupMiddleware, $options['middleware'] ?? []);
        $callback($this);
        $this->groupPrefix = $prevPrefix;
        $this->groupMiddleware = $prevMw;
    }

    private function add(string $method, string $path, string $handler, array $middleware): void
    {
        $fullPath = $this->groupPrefix . $path;
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath === '' ? '/' : $fullPath,
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $params = $this->match($route['path'], $path);
            if ($params === null) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw);
            }

            [$class, $action] = explode('@', $route['handler']);
            $controller = 'App\\Controllers\\' . $class;
            if (!class_exists($controller)) {
                http_response_code(500);
                echo 'Controller not found';
                return;
            }
            (new $controller())->$action(...array_values($params));
            return;
        }

        http_response_code(404);
        echo View::render('errors.404', [], 'layouts.guest');
    }

  /** @return array<string, string>|null */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        if (!preg_match($regex, $path, $matches)) {
            return null;
        }
        $params = [];
        foreach ($matches as $key => $val) {
            if (!is_int($key)) {
                $params[$key] = $val;
            }
        }
        return $params;
    }

    private function runMiddleware(string $middleware): void
    {
        if (str_contains($middleware, ':')) {
            [$class, $param] = explode(':', $middleware, 2);
        } else {
            $class = $middleware;
            $param = null;
        }
        $mwClass = str_starts_with($class, 'App\\') ? $class : 'App\\Middleware\\' . $class;
        (new $mwClass())->handle($param);
    }
}
