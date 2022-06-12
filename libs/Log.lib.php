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
    private function get_log_file_paths($log_type = 'default'){

        // TODO : Approve Space Complexity and time Complexity

        $file_paths = array();
        $needed_file_paths = array();
        $today_date = date('ymd',time());
        $probable_dirs = [$this->log_folder,'./logs/','./log/','../logs/','./log/','./'];
        
        array_push($needed_file_paths,'default');
        if(strtolower($log_type) == 'error'){
            array_push($needed_file_paths,strtolower($log_type));
        }

        // Try to find an existing folder from the valid paths if not creates it
        foreach($probable_dirs as $dir){
            
            if(file_exists($dir) && is_dir($dir)){
                
                try{
                    
                    foreach($needed_file_paths as $file_path){
                        
                        if(strlen($dir) > 0 && $dir[strlen($dir)-1] != '/') { $dir .='/'; }
                    
                        $new_filepath = $dir.$today_date.'_'.$file_path.'_logs.log';    
                        $handler = fopen($new_filepath,"a") or new Exception("File Unable to create");
                        fclose($handler);
                        
                        array_push($file_paths,$new_filepath);

                    }

                }catch(Exception $e){
                    
                    $e->getMessage();
                    $file_paths = array();
                    continue;

                }

            }

            if(count($file_paths) == count($needed_file_paths)) {
                return $file_paths;
            }

        }

        return 'GET_LOG_FILES_EXCEPTION';

    }

    // Function that generates the format of each record in log (To make the logs records uniform and add important informations)
    // @Param 1: String that contains the content to be appended in the log file
    // @Param 2: String that defines the type of the log record
    // @Returns: String that matches the structure of the log records
    private function generate_new_log_record($log,$log_type = "INFO"){

        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        $time = $d->format("Y-m-d H:i:s.u");

        $pid = getmypid();

        return "$time - $pid - $log_type - $log\r";

    }

    // Function that concatenates a new record to a given log file.
    // It locks the file to avoid collisions between multiple records from different users.
    // @Param 1: String of the file that we want to concatenate to
    // @Param 2: The new record that will be appended to the end of the function
    // @Returns: Exception Message if failed and true statement if the concatenated successfully
    private function concat_log_to_files($file_paths,$record){
        
        foreach($file_paths as $path){
            try{
                $handler = fopen($path,"a") or new Exception("Couldn't Open the log file");
        
                if(flock($handler,LOCK_UN | LOCK_NB)){
                    fwrite($handler,$record);
                    fclose($handler);
                }else
                    new Exception("Couldn't lock the log file");
        
            }catch(Exception $e){
                
                return $e->getMessage();
    
            }
        }

    } 

    // Void Function used to concatenate informations into the default log file
    // @Param 1: Record to add to the file
    public function log_default($content,$log_level = 3){

        if(!$this->logging_enabled || $log_level > $this->log_level) return;

        $file_paths = $this->get_log_file_paths();        

        if($file_paths == 'GET_LOG_FILES_EXCEPTION') return;

        $log = $this->generate_new_log_record($content);
        $this->concat_log_to_files($file_paths,$log);

    }

    // Void Function used to concatenate informations into the default log file
    // @Param 1: Record to add to the file
    public function log_error($content,$log_level = 3){

        if(!$this->logging_enabled || $log_level > $this->log_level) return;

        $file_paths = $this->get_log_file_paths("error");

        if($file_paths == 'GET_LOG_FILES_EXCEPTION') return;

        $log = $this->generate_new_log_record($content,"ERROR");
        $this->concat_log_to_files($file_paths,$log);

    }
    
}