<?php

spl_autoload_register(function ($class_name) {
    $extension = ".php";
    $model_path = "./Models/" . $class_name .".class". $extension;
    $controller_path =  "./Controllers/" . $class_name .".controller" . $extension;
    $view_path = "./Views/".$class_name.$extension;
    if(file_exists($model_path)){//Checks if the targetted parameter is a Class
        include_once $model_path;
    }else if (file_exists($controller_path)){//Checks if the targetted parameter is a Controller
        include_once $controller_path; 
    }else if(file_exists($view_path)){
        include_once $view_path;
    } else{//Return false when there is no valid controller or class for the given  parameter
        return false;
    }
});          