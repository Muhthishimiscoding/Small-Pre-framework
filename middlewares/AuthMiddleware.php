<?php
namespace MuhthishimisCoding\PreFramework\middlewares;
use MuhthishimisCoding\PreFramework\Application;
class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];
    public function __construct($actions=[]){
        $this->actions = $actions;
    }
    public function execute(){
        if(!Application::isLogedin()){
            if(empty($this->actions)|| in_array(Application::app()->controller->action,$this->actions)){
                header('location:/');
                exit;
            }
        }
    }
}
