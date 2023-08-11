<?php 
namespace MuhthishimisCoding\PreFramework;
class Session
{
    protected const FLASH_KEY = 'flashes';
    public function __construct(){
        session_start();
    }
    function put($name, $value)
    {
        $_SESSION[$name] = $value;
    }
   function get($name)
    {
        return  $_SESSION[$name] ?? null;
    }
    function del($name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }
     function flash($array, $shtml, $fhtml)
    {
        // $_session['flashes'] to $page like $_session['flashes']['login'] to $key
        // $key 
        if ($shtml !== null && $array[1] === true) {
            echo str_replace('{{MSG}}', $array[0], $shtml);
        } elseif ($fhtml !== null && $array[1] === false) {
            echo str_replace('{{MSG}}', $array[0], $fhtml);
        } else {
            echo $array[0];
        }
    }
    /**
     * @param $shtml success message html like this \<div class='success-msg'>{{MSG}}\</div>
     * @param $fhtml error message html like this \<div class='error-msg'>{{MSG}}\</div>
     * Here {{MSG}} is the placeholder for putting msg at the right place without 
     * breaking html
     */

    function putFlash(string $page,array $message){
        $_SESSION[self::FLASH_KEY][$page][] = $message;
     }
    function showFlashes(string $page, 
    ?string $shtml = "<div class='alert alert-success'><p class='text-center'>{{MSG}}</p></div>", 
    ?string $fhtml = "<div class='alert alert-danger'><p class='text-center'>{{MSG}}</p></div>")
    {
        if (isset($_SESSION[self::FLASH_KEY][$page])) {
            foreach ($_SESSION[self::FLASH_KEY][$page] as $value) {
                $this->flash($value,$shtml, $fhtml);
            }
            unset($_SESSION[self::FLASH_KEY][$page]);
        }
    }
}