<?php

/*
*   Codenlighter Main Library 
*/

import("Log");

function get_configurations_file(){
    return require_once('conf/config.php');
}

function import($libName){

    $extension = ".php";
    $libraryPath =  "./libs/" . $libName .".lib" . $extension;

    if(file_exists($libName)){
    
        require_once($libName);       
    
    }else if(file_exists($libraryPath)){
    
        require_once($libraryPath);       
    
    }
}

function get_config_param($param_name,$default_value = "PARAM_NOT_FOUND"){

    $conf = get_configurations_file();

    if(!array_key_exists($param_name,$conf)){ 
        return $default_value; 
    }

    if(gettype($conf[$param_name]) == "string"){
        return strval($conf[$param_name]);
    }
    
    return $conf[$param_name];

}

function log_write(){
    
}