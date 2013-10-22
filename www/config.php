<?php

header("Content-Type: text/html; charset=UTF-8");

require_once("functions.php");
require_once("lang_env.php");
require_once("bsm.php");

// Path where the projects will be stored (in the filesystem)
define("PROJECT_ROOT","/home/benjamin/lqdn2/bookscanner/p");
// Path where the projects are available in the web server (will be used in http://localhost/<PROJECT_WWW>/<project name>/...)
define("PROJECT_WWW","/projects");
// allowed characters in project names : 
define("PROJECT_PREG",'#^[0-9a-z,\.-]+$#');

// path to camdriver script shell
define("CAMDRIVER", "".dirname(__FILE__)."/../sh/camdriver_driver.sh");
