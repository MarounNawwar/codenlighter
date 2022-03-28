<?php

/*
*   Codenlighter Main Library 
*/

function import($libName){

    $extension = ".php";
    $libraryPath =  "./libs/" . $libName .".lib" . $extension;

    if(file_exists($libName)){
    
        require_once($libName);       
    
    }else if(file_exists($libraryPath)){
    
        require_once($libraryPath);       
    
    }
}

