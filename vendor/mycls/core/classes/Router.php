<?php

namespace mycls;

class Router
{

    public array $routes = [];
    private string $uri = '';
    private string $method = '';

    function __construct()
    {
        $this->uri = trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    protected function add($uri, $controller, $method)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'middleware' => null
        ];
        return $this;
    }

    public function math()
    {
        $math = false;

        foreach ($this->routes as $route) {
            if (($this->uri == $route['uri']) && ($this->method == strtoupper($route['method']))) {
                $math = true;

                if($route['middleware']) {
                    $middleware = MIDDLEWARES[$route['middleware']] ?? false;

                    if(!$middleware) {
                        throw new \Exception('Incorrect middleware');
                    }

                    (new $middleware)->handle();
                }

                require CONTROLLERS . "/{$route['controller']}";
                exit;
            }
        }
        if (!$math) {
            http_response_code(404);
            require VIEWS . '/errors/404.tpl.php';
            die;
        }
    }

    public function only($middleware) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $middleware;
        return $this;
    }

    public function get($uri, $controller)
    {
        return $this->add($uri, $controller, 'GET');
    }

    public function post($uri, $controller)
    {
        return $this->add($uri, $controller, 'POST');
    }
}
