<?php

//Start Session
session_start();

// This function loads automatically all the required classes and controllers
require_once('./includes/autoloader.inc.php');

// Loading Main Library for the framework
require_once('./libs/codenlighter.php');

//Loading Routing Library
require_once('./includes/Routes.inc.php');

//Loading the start script for the framework
// require_once('./includes/framework_start_script.inc.php');