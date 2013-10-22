<?php

require_once("config.php");

sleep(1);

if (
    !isset($_REQUEST["action"]) 
    || !isset($_REQUEST["project"])
    || !isset($_REQUEST["picture"])
    || !isset($_REQUEST["left"])
    || !isset($_REQUEST["right"])
    || !isset($_REQUEST["top"]) 
    || !isset($_REQUEST["bottom"])
    || !isset($_REQUEST["mode"])
   ) {
  header('HTTP/1.0 401 Not Implemented');
  echo "error: no action, project or picture requested";
  exit();
}

$project=trim($_REQUEST["project"]);
$picture=trim($_REQUEST["picture"]);
$action=intval($_REQUEST["action"]);     // 1: go+previous   2: go+next  3: push all (+ next)
$mode=trim($_REQUEST["mode"]);

$left=doubleval($_REQUEST["left"]);
$right=doubleval($_REQUEST["right"]);
$top=doubleval($_REQUEST["top"]);
$bottom=doubleval($_REQUEST["bottom"]);

if ($mode!='left' && $mode!='right') {
  header('HTTP/1.0 401 Not Implemented');
  echo "error: mode is neither left nor right ?!";
  exit();  
}

if (!is_dir(PROJECT_ROOT."/".$project)) {
  header('HTTP/1.0 401 Not Implemented');
  echo "error: project not found";
  exit();
}

// GET the current cropping area
$crop=@json_decode(@file_get_contents(PROJECT_ROOT."/".$project."/crop.json"),true);
if (!is_array($crop) || !isset($crop["left"]) || !isset($crop["right"])) {
  $crop=array(
	      "left" => array(), 
	      "right" => array()
	      );
}

$crop[$mode][$picture]["left"]=$left;
$crop[$mode][$picture]["right"]=$right;
$crop[$mode][$picture]["top"]=$top;
$crop[$mode][$picture]["bottom"]=$bottom;

// $f=fopen("/tmp/t","ab"); fputs($f,"setting $mode $picture / $left $right $top $bottom\n".print_r($crop,true)); fclose($f);

if ($_REQUEST["action"]==3) { // PUSH down to the rest
  // We scan the list of left or right pictures for this project, we sort them
  // and set the cropping area for all pictures AFTER this one
  $d=@opendir(PROJECT_ROOT."/".$project."/".$mode);
  if ($d) {
    while (($c=readdir($d))!=false) {
      if (is_file(PROJECT_ROOT."/".$name."/".$mode."/".$c)) {
	$pics[]=$c;
      }
    }
    closedir($d);
    sort($pics);
    $found=false;
    foreach($pics as $pic) {
      if ($found) {
	$crop[$mode][$pic]["left"]=$left;
	$crop[$mode][$pic]["right"]=$right;
	$crop[$mode][$pic]["top"]=$top;
	$crop[$mode][$pic]["bottom"]=$bottom;
      }
      if ($pic==$picture) $found=true;
    }
  }
}


file_put_contents(PROJECT_ROOT."/".$project."/crop.json",json_encode($crop));


switch ($_REQUEST["action"]) {
  
case "":
  unset($out);
  exec(CAMDRIVER." search 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;
  

}