<?php

import("Route");

// List all routes to be accessed in the web app Here
// @param 1 : Route(s) allowed
// @param 2 : Behavior/Logic made if route matched  || file name to access 

//Routing for the default landing Pages
Route::set(["index.php",'/'],"homepage");

//Single route destination example
Route::set("about",function(){
    
});

Route::set("test",function(){
    require_once("./Views/test.php");
});