<?php

require_once("config.php");

if (
    !isset($_REQUEST["action"]) 
    || !isset($_REQUEST["project"])
    || !isset($_REQUEST["picture"])
    || !isset($_REQUEST["mode"])
   ) {
  header('HTTP/1.0 401 Not Implemented');
  echo "error: no action, project or picture requested";
  exit();
}

if ($_REQUEST["action"]!="get" && 
    (!isset($_REQUEST["left"])
    || !isset($_REQUEST["right"])
    || !isset($_REQUEST["top"]) 
    || !isset($_REQUEST["bottom"]) 
     || !isset($_REQUEST["rotate"]))) {
  header('HTTP/1.0 401 Not Implemented');
  echo "error: no coordinates";
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
$rotate=doubleval($_REQUEST["rotate"]);

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

if ($action=="get") {
  // return a json with the current cropping area
  $crop=@json_decode(@file_get_contents(PROJECT_ROOT."/".$project."/crop.json"),true);
  if (!is_array($crop) 
      || !isset($crop[$mode])
      || !isset($crop[$mode][$picture])
      )  {
    echo json_encode(false); 
  } else {
    echo json_encode($crop[$mode][$picture]);
  }
  exit();
}

$lock=fopen(PROJECT_ROOT."/".$project."/temp/crop.json.lock","wb");
flock($lock,LOCK_EX);

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
$crop[$mode][$picture]["rotate"]=$rotate;

// $f=fopen("/tmp/t","ab"); fputs($f,"setting $mode $picture / $left $right $top $bottom\n".print_r($crop,true)); fclose($f);

if ($_REQUEST["action"]==3) { // PUSH down to the rest
  // We scan the list of left or right pictures for this project, we sort them
  // and set the cropping area for all pictures AFTER this one
  $d=@opendir(PROJECT_ROOT."/".$project."/".$mode);
  if ($d) {
    while (($c=readdir($d))!=false) {
      if (is_file(PROJECT_ROOT."/".$project."/".$mode."/".$c)) {
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
	$crop[$mode][$pic]["rotate"]=$rotate;
      }
      if ($pic==$picture) $found=true;
    }
  }
}


file_put_contents(PROJECT_ROOT."/".$project."/crop.json",json_encode($crop));

flock($lock,LOCK_UN);
fclose($lock);
