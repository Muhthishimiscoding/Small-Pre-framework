<?php

namespace MuhthishimisCoding\PreFramework;

class Request
{
    function getPath()
    {

        $path = $_SERVER['REQUEST_URI'] ?? '/';
        // Creating position variable   | comparing with ternary operator
        return ($position = strpos($path, '?')) ? substr($path, 0, $position) : $path;
    }
    function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    protected function giveBody(array &$valuesArray, int $TYPE, int $filter, $trim, $escape, $htmlentities)
    {

        if ($trim == null && $escape == null && $htmlentities == null) {
            return $this->cleandata($valuesArray, $TYPE, $filter);
        } else {
            return CleanData::cleanPostdata($valuesArray, Application::app()->db, $trim, $escape, $htmlentities);
        }

    }
    protected function cleandata(&$valuesArray, $TYPE, $filter)
    {
        $cleandata = [];
        foreach ($valuesArray as $key => $value) {
            $cleandata[$key] = filter_input($TYPE, $key, $filter);
        }
        return $cleandata;
    }
    function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }
    function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }
    function getBody($trim = null, $escape = null, $htmlentities = null)
    {
        if ($this->getMethod() === 'get') {
            return $this->giveBody($_GET, INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS, $trim, $escape, $htmlentities);
        }
        if ($this->getMethod() === 'post') {
            return $this->giveBody($_POST, INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS, $trim, $escape, $htmlentities);
        }
    }
}