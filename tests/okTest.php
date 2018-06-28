<?php


function  __autoload($className)
{
    $filePath = "test/{$className}.php";
    if (is_readable($filePath)) {
        require($filePath);
    }
}