<?php

class Route{

    //Arrays containing the valid URL's
    public static $validRoutes = array();
    private static $validMethods = ['POST','GET','PUT','DELETE','PATCH'];

    //Function to set the valid URL
    private static function access($route,$function){

        if($_GET['url'] == $route){
            
            $function->__invoke();
        
        }
    }

    public static function post($route,$function){
        
        self::$validRoutes[] = $route;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            self::access($route,$function);

        }else{

            //Handle Not Valid Requests

        }
    }

    public static function get($route,$function){

        self::$validRoutes[] = $route;

        if($_SERVER['REQUEST_METHOD'] === 'GET'){

            self::access($route,$function);

        }else{

            //Handle Not Valid Requests

        }
    }

    public static function set($route,$function){

        self::$validRoutes[] = $route;        
        self::access($route,$function);

    }

    public static function any($allowed_methods,$route,$function){
        
        self::$validRoutes[] = $route;

        if(in_array(strtoupper($_SERVER['REQUEST_METHOD']),$allowed_methods)
            && in_array($allowed_methods,self::$validMethods)){

            self::access($route,$function);
            
        }else{

            //Handle Not Valid Requests

        }

    }

}