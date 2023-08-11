<?php

namespace MuhthishimisCoding\PreFramework;

class Application
{
    static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Errorhandler $error;
    public Controller $controller;
    public View $view;
    protected static Application $app;
    public $loginClass;
    public Database $db;
    public function __construct($rootPath,array $config)
    {   
        date_default_timezone_set("Asia/Karachi");
        self::$app = $this;
        self::$ROOT_DIR = $rootPath;
        $this->error = new Errorhandler(self::$ROOT_DIR.$config['logFile'],$config['error_reporting']);
        $this->session = new Session();
        $this->loginClass =new $config['loginClass']();
        $this->request = new Request;
        $this->response = new Response;
        $this->view = new View;
        $this->router = new Router($this->request);
        $this->controller = new Controller();
        $this->db = new Database($config['db'],$config['error_reporting']);
    }
    static function app(){
        return self::$app;
    }
    public function run()
    {
        $this->router->resolve();
    }
    public static function isLogedin():bool{
        return Application::app()->loginClass->isLogedin();
    }
    public static function user():array|bool{
        if(Application::isLogedin()){
            return Application::app()->loginClass->retriveUser();
        }
        return false;
    }
    public static function printformat(...$args){
        foreach ($args as $array) {
            echo '<pre>';
            print_r($array);
            echo '</pre>';
        }
    }
}