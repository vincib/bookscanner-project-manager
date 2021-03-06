<?php

require_once("config.php");

sleep(1);

if (!isset($_REQUEST["action"])) {
  echo "error: no action requested";
  exit();
}

switch ($_REQUEST["action"]) {
  
case "resetall":
  unset($out);
  exec("sudo /var/www/sh/boot.sh 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "search":
  unset($out);
  exec(CAMDRIVER." search 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;
  
case "prepare":
  exec(CAMDRIVER." prepare 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "resetzoom":
  file_put_contents("/tmp/zoom","40");
  unset($out);
  exec(CAMDRIVER." zoom 40 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "zoomin":
  $zoom=intval(@file_get_contents("/tmp/zoom"));
  if (!$zoom) {
    $zoom=45;
  }
  if ($zoom>=ZOOM_MAX) {
    echo "ERROR: ".sprintf(_("can't zoom in, at %s"),$zoom);
    exit();
  }
  if (isset($_REQUEST["much"]) && intval($_REQUEST["much"])>0 && intval($_REQUEST["much"])<10) 
    $zoom+=intval($_REQUEST["much"])*5;
  else
    $zoom+=5;
  if ($zoom>ZOOM_MAX) $zoom=ZOOM_MAX;

  file_put_contents("/tmp/zoom",$zoom);
  unset($out);
  exec(CAMDRIVER." zoom $zoom",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "zoomout":
  $zoom=intval(@file_get_contents("/tmp/zoom"));
  if (!$zoom) {
    $zoom=40;
  }
  
  if ($zoom<=ZOOM_MIN) {
    echo "ERROR: ".sprintf(_("can't zoom out, at %s"),ZOOM_MIN);
    exit();
  }
  if (isset($_REQUEST["much"]) && intval($_REQUEST["much"])>0 && intval($_REQUEST["much"])<10) 
    $zoom-=intval($_REQUEST["much"])*5;
  else 
    $zoom-=5;
  if ($zoom<ZOOM_MIN) $zoom=ZOOM_MIN;
  file_put_contents("/tmp/zoom",$zoom);
  unset($out);
  exec(CAMDRIVER." zoom $zoom",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);
  break;

case "shoot":
  if (!isset($_REQUEST["project"])) {
    echo _("ERROR: project not set"); exit();
  }
  if (isset($_REQUEST["alsoget"]) && $_REQUEST["alsoget"]!=0) {
    exec(CAMDRIVER." shootget ".escapeshellarg(PROJECT_ROOT."/".trim($_REQUEST["project"])."/left")." ".escapeshellarg(PROJECT_ROOT."/".trim($_REQUEST["project"])."/right")." 2>&1",$out,$ret);
  } else {
    exec(CAMDRIVER." shoot ".escapeshellarg(PROJECT_ROOT."/".trim($_REQUEST["project"])."/left")." ".escapeshellarg(PROJECT_ROOT."/".trim($_REQUEST["project"])."/right")." 2>&1",$out,$ret);
  }
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);  
  break;

case "get":
  if (!isset($_REQUEST["project"])) {
    echo _("ERROR: project not set"); exit();
  }
  exec(CAMDRIVER." get ".escapeshellarg(PROJECT_ROOT."/".trim($_REQUEST["project"])."/left")." ".escapeshellarg(PROJECT_ROOT."/".trim($_REQUEST["project"])."/right")." 2>&1",$out,$ret);
  if ($ret!=0) echo "ERROR: "; else echo "OK: ";
  echo implode("<br />",$out);  
  break;

default:
  echo "error: action not found, ".he($_REQUEST["action"]);
  exit();
}

?>