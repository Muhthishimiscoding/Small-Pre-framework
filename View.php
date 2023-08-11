<?php
namespace MuhthishimisCoding\PreFramework;

class View
{
    public string $title = '';
    public function giveViewDetail($key): array|bool
    {
        return $this->{$key}() ?? false;
    }
    public function title($key)
    {
        return $this->{$key}()['title'] ?? false;
    }
    public function login()
    {
        return [
            'title' => 'Login',
        ];
    }
    public function register()
    {
        return [
            'title' => 'Register with us now',
        ];
    }
    public function home()
    {
        return [
            'title' => 'Let us show what we got!',
            'css' => '<link rel="stylesheet" href="/views/styles/style.css">',
        ];
    }
    public function profile(){
        return [
            'title'=>'Profile'
        ];
    }
}