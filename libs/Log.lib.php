<?php

/*
*   Log Library
*/

import("config");

class log {

    private static $instance;
    private $logging_enabled;
    private $log_folder;
    private $log_level;

    // Constructor of the log library 
    private function __construct(){

        $this->logging_enabled = get_config_param('enable_logging',true);
        $this->log_folder = $this->get_config_folder_path();
        $this->log_level = $this->get_config_log_level();

    }

    // Function used to get Instance of the Log Library
    // @Returns: Instance Object pf the class Log  
    public static function get_instance(): log{
        
        $cls = static::class;
        
        if(!isset(self::$instance[$cls])){ 
        
            self::$instance[$cls] = new static();
        
        }
        
        return self::$instance[$cls];

    }

    // Function used to get the log path configuration existing in the config file
    // @Returns: String value that represents the requested log path
    private function get_config_folder_path(){
        
        return get_config_param('log_folder',"../logs");

    }

    // Function used to get the log level from the configuration file
    // @Returns: Number value that represents the debug log level
    private function get_config_log_level(){

        return get_config_param('log_level',"3");

    }

    // Function that searches for the valid file path of the log path
    // @Param : String that represents the type of the log (WARNING,INFO,ERROR)
    // @Returns: The String value that represents the `log file path` that the log must be written into
    private function get_log_file_path($log_type = 'default'){

        $today_date = date('mdy',time());
        $probable_dirs = [$this->log_folder,'./logs/','./log/','../logs/','./log/','./'];

        if(strtolower($log_type) != 'default' && strtolower($log_type) != 'error'){
            $log_type = 'default';
        }

        // Try to find an existing folder from the valid paths if not creates it
        foreach($probable_dirs as $dir){
            if(file_exists($dir) && is_dir($dir)){
                try{
                    
                    if(strlen($dir) > 0 && $dir[strlen($dir)-1] != '/') { $dir .='/'; }
                    
                    $new_filepath = $dir.$today_date.'_'.$log_type.'.log';    
                    $handler = fopen($new_filepath,"a") or new Exception("File Unable to create");
                    fclose($handler);
                    
                    return $new_filepath;

                }catch(Exception $e){
                    
                    $e->getMessage();

                }
            }
        }

        return 'GET_LOG_FILE_EXCEPTION';

    }

    // Function that generates the format of each record in log (To make the logs records uniform and add important informations)
    // @Param 1: String that contains the content to be appended in the log file
    // @Param 2: String that defines the type of the log record
    // @Returns: String that matches the structure of the log records
    private function generate_new_log_record($log,$log_type = "INFO"){

        $time  = date('Y-m-d H:i:s',time());
        return "$time - $log_type - $log\r";

    }

    // Function that concatenates a new record to a given log file.
    // It locks the file to avoid collisions between multiple records from different users.
    // @Param 1: String of the file that we want to concatenate to
    // @Param 2: The new record that will be appended to the end of the function
    // @Returns: Exception Message if failed and true statement if the concatenated successfully
    private function concat_log_to_file($file_path,$record){
        
        try{
            
            $handler = fopen($file_path,"a") or new Exception("Couldn't Open the log file");
    
            if(flock($handler,LOCK_UN | LOCK_NB)){
                fwrite($handler,$record);
                fclose($handler);
            }else
                new Exception("Couldn't lock the log file");
    
        }catch(Exception $e){
            
            return $e->getMessage();

        }

    } 

    // Void Function used to concatenate informations into the default log file
    // @Param 1: Record to add to the file
    public function log_default($content,$log_level){

        if(!$this->logging_enabled || $log_level > $this->log_level) return;

        $file_path = $this->get_log_file_path();        

        if($file_path == 'GET_LOG_FILE_EXCEPTION') return;

        $log = $this->generate_new_log_record($content);
        $this->concat_log_to_file($file_path,$log);

    }

    // Void Function used to concatenate informations into the default log file
    // @Param 1: Record to add to the file
    public function log_error($content,$log_level){

        if(!$this->logging_enabled || $log_level > $this->log_level) return;

        $file_path = $this->get_log_file_path("error");

        if($file_path == 'GET_LOG_FILE_EXCEPTION') return;

        $log = $this->generate_new_log_record($content);
        $this->concat_log_to_file($file_path,$log);

    }
    
}