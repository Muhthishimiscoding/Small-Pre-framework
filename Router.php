<?php

namespace MuhthishimisCoding\PreFramework;

class Router
{
    public Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    protected array $routes = [];
    public function get(string $path, \Closure|array|string $callBack = [])
    {
        $this->routes['get'][$path] = $callBack;
    }
    public function post(string $path, \Closure|array $callBack = [])
    {
        $this->routes['post'][$path] = $callBack;
    }
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callBack = $this->routes[$method][$path] ?? false;
        if (!$callBack) {
            http_response_code(404);
            $this->renderView(['view' => '_404']);
            exit;
        }
        return is_string($callBack) ? $this->renderView($callBack) : call_user_func($callBack, $this->request);
    }
    public function renderView(string $view, $params = [])
    {
        $view = ['view' => $view];
        $view = array_merge(['header' => 'header', 'footer' => 'footer'], $view);

        $viewDetails = method_exists(Application::app()->view, $view['view']) ? Application::app()->view->{$view['view']}() : [];
        $view['header'] = $viewDetails['header'] ?? $view['header'];
        $view['footer'] = $viewDetails['footer'] ?? $view['footer'];

        // unset($viewDetails['header'], $viewDetails['footer']);
        $params = array_merge([
            'session' => Application::app()->session
        ], $params, $viewDetails);
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        // Application::app()->controller->action = $view['view'];
        // print_r(Application::app()->controller->getMiddleWares());
        // foreach (Application::app()->controller->getMiddleWares() as $middleWare) {
        //     $middleWare->execute();
        // }
        ;
        // if ($view['css']) {
        //     echo str_replace("<!-- CSS LINKS HERE -->", $view['css'], $this->layOutContent(Application::$ROOT_DIR . "/views/layouts/{$view['header']}.php"));
        // } else {
            include_once Application::$ROOT_DIR . "/views/layouts/{$view['header']}.php";
        // }
        include_once Application::$ROOT_DIR . "/views/{$view['view']}.php";
        // if ($view['scripts']) {
            echo str_replace("<!-- SCRIPTS LINKS HERE -->", $view['scripts'], $this->layOutContent(Application::$ROOT_DIR . "/views/layouts/{$view['footer']}.php"));
        // } else {
            include_once Application::$ROOT_DIR . "/views/layouts/{$view['footer']}.php";
        // }

    }
    public function layOutContent($path)
    {
        ob_start();
        include_once $path;
        return ob_get_clean();
    }
}