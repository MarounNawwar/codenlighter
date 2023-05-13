<?php

import("Log");
import("Database");

class Authentication{

    private $db;
    private static $instance;

    private function __construct(){
        $this->db = DataBase::getConnection();
    }

    public static function getInstance(): Authentication{
        $cls = static::class;
        if (!isset(self::$instance[$cls])) {
            log_write("Creating Instance of Authentication...");
            self::$instance[$cls] = new static();
        }
        return self::$instance[$cls];
    }

    // TODO: Work on the implementation using the Password Hash Library

    ###################################
    #   USER FUNCTIONS
    ###################################
    public function create_user(){

        $user = [];        
        $args_nb = func_num_args();

        switch ($args_nb) {
            case 6: $user["is_super_admin"] = func_get_arg(5);
            case 5: $user["is_admin"] = func_get_arg(4);
            case 4: $user["password"] = func_get_arg(3);
            case 3: $user["email"] = func_get_arg(2);
            case 2: $user["lastname"] = func_get_arg(1);
            case 1: $user["firstname"] = func_get_arg(0); break;
            default: return false;
        }
        $user["is_active"] = false;
        $user["date_joined"] = date('Y-m-d', strtotime('today'));
        $user["last_login"] = "";
        $user["session_id"] = "";

        

        return $user;

    }

    public function update_user(){
        // TODO: To Implement
    }

    public function authenticate_user(){
        // TODO: To Implement
    }

    ###################################
    #   GROUP FUNCTIONS
    ###################################
    public function create_group(){
        
    }
    
}