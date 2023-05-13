<?php

class Route{

    //Arrays containing the valid URL's
    public static $validRoutes = array();
    private static $validMethods = ['POST','GET','PUT','DELETE','PATCH'];

    //Function to set the valid URL
    private static function access($route,$handler){
        if((isset($_GET['url']) && $_GET['url'] == $route) || $route == $_SERVER['REQUEST_URI'] ){

            $requested_route = $_SERVER['REQUEST_URI'];
            $subdirectory = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            $route_regex = '#^' . preg_quote($subdirectory . $route, '#') . '\/?(.*)$#';

            if (preg_match($route_regex, $requested_route, $matches)) {
                if(is_callable($handler)){
                    $params = array_slice($matches,1);
                    $handler(...$params);
                }else{
                    self::redirect($handler);
                }
            }
        }else{
            //Handle Not Valid Requests
        }
    }

    public static function post($route,$function){
        self::$validRoutes[] = $route;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::set($route,$function);
        }else{
            //Handle Not Valid Requests
        }
    }

    public static function get($route,$function){
        self::$validRoutes[] = $route;
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            self::set($route,$function);
        }else{
            //Handle Not Valid Requests
        }
    }

    public static function set($routes,$function){
        
        if(gettype($routes) === 'array'){
            foreach($routes as $route){
                self::$validRoutes[] = $route;        
                self::access($route,$function);
            }
        }
        else{
            self::$validRoutes[] = $routes;        
            self::access($routes,$function);
        }

    }

    public static function any($allowed_methods,$route,$function){
        self::$validRoutes[] = $route;
        if(in_array(strtoupper($_SERVER['REQUEST_METHOD']),$allowed_methods) && in_array($allowed_methods,self::$validMethods)){
            self::set($route,$function);
        }else{
            //Handle Not Valid Requests
        }
    }

    public static function has_access(){

        //TODO: Implement function to check access for calls

    }

    public static function action_allowed(){

        //TODO: Implement function to check if the action is allowed or not

    }

    public static function redirect($target_destination){

        //TODO: Implement function to redirect to the target needed

    }

    public static function access_error(){

        //TODO: Redirect web app to Error Page

    }

}