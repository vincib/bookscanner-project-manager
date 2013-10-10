<?php

require_once("config.php");

sleep(1);

if (!isset($_REQUEST["action"])) {
  echo "error: no action requested";
  exit();
}

switch ($_REQUEST["action"]) {
  
case "search":
  unset($out);
  exec(CAMDRIVER." search 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;
  
case "resetzoom":
  file_put_contents("tmp/zoom","40");
  unset($out);
  exec(CAMDRIVER." zoom 40 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "zoomin":
  $zoom=intval(@file_get_contents("tmp/zoom"));
  if (!$zoom) {
    $zoom=40;
  }
  $zoom+=5;
  if ($zoom>ZOOM_MAX) {
    echo "ERROR: ".sprintf(_("can't zoom in, at %s"),ZOOM_MAX);
    exit();
  }
  file_put_contents("tmp/zoom 2>&1",$zoom);
  unset($out);
  exec(CAMDRIVER." zoom $zoom",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "zoomout":
  $zoom=intval(@file_get_contents("tmp/zoom"));
  if (!$zoom) {
    $zoom=40;
  }
  $zoom-=5;
  if ($zoom<ZOOM_MIN) {
    echo "ERROR: ".sprintf(_("can't zoom out, at %s"),ZOOM_MAX);
    exit();
  }
  file_put_contents("tmp/zoom 2>&1",$zoom);
  unset($out);
  exec(CAMDRIVER." zoom $zoom",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "shoot":
  exec(CAMDRIVER." shoot 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);  
  break;

default:
  echo "error: action not found, ".he($_REQUEST["action"]);
  exit();
}

?>