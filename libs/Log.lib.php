<?php

/*
*   Log Library 
*/

import("config");

class log {

    private static $instance;
    private $log_folder;

    private function __construct(){
        $log_folder = get_config_param('log_folder',"../logs");
    }

    public static function get_instance(): log{
        
        $cls = static::class;
        
        if(!isset(self::$instance[$cls])){ 
            self::$instance[$cls] = new static();
        }

        return self::$instance[$cls];

    }

    public function concat_logs(){
        
    }

}