<?php 

namespace MuhthishimisCoding\PreFramework;
class Errorhandler
{
    /**
     * @param $development to turn on development mode make it 1 which is default to turn off just type 0
     */
    public function __construct(private string $logFilePath,private int $development = 1,private int $inJson = 0)
    {
        if ($development) {
            ini_set('display_errors', true);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', false);
            error_reporting(0);
        }
        ini_set('error_reporting', E_ERROR);
        ini_set('log_errors', 1);
        ini_set('error_log', $this->logFilePath);
    }
    public function setinJson($json =1){
        $this->inJson = $json;
    }
    public function handleException(\Throwable $exception,
    $message ='We are facing a techinal difficulty kindly continue later. Thanks for your coopration.'): void
    {
        http_response_code(500); //500 generic server error
        $errors = $this->giveErrorArray($exception);
        if ($this->development) {
            if ($this->inJson) {
                echo json_encode($errors);
            } else {
                Application::printformat($errors);
            }
        } else {
            echo ($this->inJson === 1) ? json_encode(['message' => $message, 'status' => false]) : $message;
            $this->logErrors($errors);
        }
    }
    public function logErrors(array|string|null $errors,$e=null)
    {
        if($e !==null){
            $errors = $this->giveErrorArray($e);
        }
        error_log(json_encode($errors));
        // $errors = is_array($errors) ?json_encode($errors):$errors;
        // file_put_contents($this->logFilePath,$errors);
    }
    public function getMode(){
        return $this->development;
    }
    private function giveErrorArray(\Throwable &$e):array{
        return [
            "message" => $e->getMessage(),
            "line" => $e->getLine(),
            "file" => $e->getFile(),
            "code" => $e->getCode(),
            "timestamp" => date("Y-m-d H:i:s a"),
        ];
    }
    public function handleError($errors)
    {
    }
}
