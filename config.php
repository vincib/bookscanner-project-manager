<?php

header("Content-Type: text/html; charset=UTF-8");

require_once("functions.php");
require_once("bsm.php");

// Path where the projects will be stored
define("PROJECT_ROOT","/home/benjamin/lqdn2/bookscanner/p");
// Path where the projects are available through a web server
define("PROJECT_WWW","/projects");
// allowed characters in project name : 
define("PROJECT_PREG",'#^[0-9a-z,\.-]+$#');

// path to camdriver script shell
define("CAMDRIVER", dirname(__FILE__)."/camdriver.sh");
