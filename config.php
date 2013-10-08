<?php

header("Content-Type: text/html; charset=UTF-8");

require_once("functions.php");
require_once("bsm.php");

define("PROJECT_ROOT","/home/benjamin/lqdn2/bookscanner/p");
// allowed characters in project name : 
define("PROJECT_PREG",'#^[0-9a-z,\.-]+$#');

