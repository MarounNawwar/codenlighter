<?php

/*
*   Codenlighter Main Library 
*/
import("Log");

function import($libName){

    $extension = ".php";
    $libraryPath =  "./libs/" . $libName .".lib" . $extension;

    if(file_exists($libName)){

        require_once($libName);       
    
    }else if(file_exists($libraryPath)){
    
        require_once($libraryPath);       
    
    }
}

function get_operating_system() {
    
    $u_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $operating_system = 'Unknown Operating System';

    //Get the operating_system name
    if($u_agent) {
        if (preg_match('/linux/i', $u_agent)) {
            $operating_system = 'Linux';
        } elseif (preg_match('/macintosh|mac os x|mac_powerpc/i', $u_agent)) {
            $operating_system = 'Mac';
        } elseif (preg_match('/windows|win32|win98|win95|win16/i', $u_agent)) {
            $operating_system = 'Windows';
        } elseif (preg_match('/ubuntu/i', $u_agent)) {
            $operating_system = 'Ubuntu';
        } elseif (preg_match('/iphone/i', $u_agent)) {
            $operating_system = 'IPhone';
        } elseif (preg_match('/ipod/i', $u_agent)) {
            $operating_system = 'IPod';
        } elseif (preg_match('/ipad/i', $u_agent)) {
            $operating_system = 'IPad';
        } elseif (preg_match('/android/i', $u_agent)) {
            $operating_system = 'Android';
        } elseif (preg_match('/blackberry/i', $u_agent)) {
            $operating_system = 'Blackberry';
        } elseif (preg_match('/webos/i', $u_agent)) {
            $operating_system = 'Mobile';
        }
    } else {
        $operating_system = php_uname('s');
    }

    return $operating_system;

}

function get_configurations_file(){

    //TODO: Implement the ini and conf files
    
    $os = get_operating_system();

    if(strtolower($os) == strtolower("windows")){
        return 'conf/default.ini';    //Windows
    }else if(strtolower($os) == strtolower("linux")){
        return 'conf/default.conf';    //Linux
    }
    return require('conf/config.php');   //Test
}

//FIXME: Should rename function to more relevant function
function read_config() {
    
    $os = get_operating_system();
    
    if(strtolower($os) == strtolower("windows")){

        $filename = get_configurations_file();
        return parse_ini_file($filename, false, INI_SCANNER_NORMAL);

    }else if(strtolower($os) == strtolower("linux")){

        $filename = get_configurations_file();

        define('HTTPD_CONF', $filename);
        $lines = file(HTTPD_CONF);
        $config = array();

        foreach ($lines as $l) {
            preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", $l, $matches);
            if (isset($matches['key'])) {
                $config[$matches['key']] = $matches['value'];
            }
        }

        return $config;

    }

}

function get_config_param($param_name,$default_value = "PARAM_NOT_FOUND"){

    //Retrieve the default configurations and the specific modifications
    //FIXME: This should be retrieved using read_config function
    $conf = get_configurations_file();
    
    if(!array_key_exists($param_name,$conf)){ 
        return $default_value; 
    }

    if(gettype($conf[$param_name]) == "string"){
        return strval($conf[$param_name]);
    }

    return $conf[$param_name];

}

function log_write($content,$log_level = null){

    $handler = Log::get_instance();
    $handler->log_default($content,$log_level);

}

function log_error($content,$log_level = null){

    $handler = Log::get_instance();
    $handler->log_error($content,$log_level);

}