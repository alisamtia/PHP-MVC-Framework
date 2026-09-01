<?php

namespace Core;

use Core\Middleware;
use Core\RedirectResponse;
use Core\Request;

class Router
{
    protected array $routes;
    protected $allowed_method = ["POST", "GET", "PUT", "PATCH", "DELETE"];
    protected $uriMap = [];
    protected $current_method = "";
    protected $last_regex = "";


    function __call($method, $args)
    {
        $uri = $args[0];
        $controller = $args[1];
        $method = strtoupper($method);
        $this->current_method = $method;
        $middlewares = [];

        in_array_or_error($method, $this->allowed_method, "'$method' not found! Please call a correct method in the routes.");
        $converted_regex = $this->convertToRegex($uri);
        $regex = $converted_regex['regex'];

        $this->uriMap[$regex]['regex'] = $regex;
        $this->uriMap[$regex]['method'][] = $method;

        $this->last_regex = $regex;

        $param_names = $converted_regex['params'];
        $this->routes[$method][$regex] = compact('uri', 'controller', 'middlewares', 'method', 'regex', 'param_names');
        return $this;
    }

    function resource($base_uri, $controller, $middlewares = [])
    {
        $this->get("$base_uri", "$controller@index")->middleware($middlewares);
        $this->get("$base_uri/create", "$controller@create")->middleware($middlewares);
        $this->post("$base_uri", "$controller@store")->middleware($middlewares);
        $this->get("$base_uri{:id}", "$controller@show")->middleware($middlewares);
        $this->get("$base_uri{:id}/edit", "$controller@edit")->middleware($middlewares);
        $this->get("$base_uri{:id}", "$controller@update")->middleware($middlewares);
        $this->get("$base_uri{:id}", "$controller@destroy")->middleware($middlewares);
        return $this;
    }


    function middleware(array|string $allowed_role)
    {
        foreach ((array) $allowed_role as $role) {
            Middleware::verify_middleware_exists($role);
            $this->routes[$this->current_method][$this->last_regex]["middlewares"][] = $role;
        }
    }


    private function convertToRegex(string $pattern): array
    {
        $paramNames = [];

        $regex = preg_replace_callback(
            '#\{([^}]+)\}#',
            function ($matches) use (&$paramNames) {
                $segment = $matches[1];
                $optional = false;

                // match for optional parameters
                if (str_ends_with($segment, '?')) {
                    $optional = true;
                    $to_replace = "?";
                    $name = rtrim($segment, '?');
                    $customRegex = $customRegex ?? '[^/]+';
                }

                // Check for custom regex {id:\d+} or {:id}
                if (str_contains($segment, ':')) {
                    $to_replace = ":";
                    [$name, $customRegex] = explode(':', $segment, 2);
                    if ($customRegex !== "\d+") {
                        $name = str_replace($to_replace, "", $segment);
                        $customRegex = '[^/]+';
                    }
                }
                $customRegex = $customRegex ?? '[^/]+';
                $customRegex = str_replace('/', '\/', $customRegex);

                if (isset($name)) {
                    $paramNames[] = $name;
                }

                if ($optional) {
                    return '(?:/(' . $customRegex . '))?';
                }

                return '(' . $customRegex . ')';
            },
            $pattern
        );


        // Fix double slashes issue caused by optional params
        $regex = str_replace('//', '/', $regex);

        // Add anchors
        $regex = '#^' . $regex . '$#';

        return [
            'regex' => $regex,
            'params' => $paramNames
        ];
    }
    function route($request)
    {
        $uri = $request['uri'];
        $method = $request['method'];
        $uri = explode("?", $uri)[0];
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }
        $uri = urldecode($uri);
        $method = strtoupper($method);
        in_array_or_error($method, $this->allowed_method, "The method you called can't be found");
        $filtered_routes = $this->routes[$method] ?? false;

        if ($filtered_routes) {
            foreach ($filtered_routes as $route) {
                $regex = $route['regex'];
                if (preg_match($regex, $uri, $matches)) {
                    Middleware::verify($route['middlewares'], $method);
                    $vars = [];

                    if (count($matches) > 1) {
                        array_shift($matches);
                        if (count($route['param_names']) === count($matches)) {
                            $vars = array_combine($route['param_names'], $matches);
                        }
                    }

                    $controller = explode("@", $route['controller'], 2);

                    $controllerClass = "App\\Controllers\\" . $controller[0] . "Controller";
                    $method = $controller[1];

                    if (!class_exists($controllerClass)) {
                        throw new \Exception("Controller not found: {$controllerClass}");
                    }

                    $instance = new $controllerClass();

                    if (!method_exists($instance, $method)) {
                        throw new \Exception("Method '{$method}' not found in {$controllerClass}");
                    }
                    if (Request::isGet()) {
                        RedirectResponse::setPreviousUrl($uri);
                    }

                    return $instance->$method($vars);
                }
            }
        }
        $uriMatched = false;

        foreach ($this->uriMap as $route) {
            if (preg_match($route['regex'], $uri)) {
                $uriMatched = true;
                break;
            }
        }
        if ($uriMatched) {
            abort(405);
        }
        abort(404);
    }
}
