<?php

namespace MuhthishimisCoding\PreFramework;
class Response
{
    public function setStatusCode($code){
        http_response_code($code);
    }
}