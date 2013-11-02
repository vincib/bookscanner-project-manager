<?php

require_once("config.php");

if (!isset($_REQUEST["name"])) {
  $error=_("Project name missing");
  require("index.php");
  exit();
}
$name=trim(strtolower($_REQUEST["name"]));
if (!preg_match(PROJECT_PREG,$name) || !is_dir(PROJECT_ROOT."/".$name)) {
  $error=_("Project name is incorrect");
  require("index.php");
  exit();
}

$meta=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);
$crop=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/crop.json"),true);

  if (!is_dir(PROJECT_ROOT."/".$name."/left")) 
    mkdir(PROJECT_ROOT."/".$name."/left");
  if (!is_dir(PROJECT_ROOT."/".$name."/right")) 
    mkdir(PROJECT_ROOT."/".$name."/right");

$left=0; $right=0;
$cleft=0; $cright=0;
$lastLeft=0; $lastLeftFile="";
$lastRight=0; $lastRightFile="";

$d=opendir(PROJECT_ROOT."/".$name."/left");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/left/".$c)) {
    $left++;
    if (isset($crop["left"][$c]) && isset($crop["left"][$c]["top"])) {
      $cleft++;
    }
  }
  if (filemtime(PROJECT_ROOT."/".$name."/left/".$c)>$lastLeft) $lastLeftFile=$c;
}
closedir($d);
$d=opendir(PROJECT_ROOT."/".$name."/right");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/right/".$c)) {
    $right++;
    if (isset($crop["right"][$c]) && isset($crop["right"][$c]["top"])) {
      $cright++;
    }
  }
  if (filemtime(PROJECT_ROOT."/".$name."/right/".$c)>$lastRight) $lastRightFile=$c;
}
closedir($d);


if (isset($_POST["name"])) {
  // let's see if the project seems to be in good state. If yes, let's ask the daemon for a PDF :) 
  if ($cleft==$left && $cright==$right && $left>0 && $right>0) {
    file_put_contents(PROJECT_ROOT."/".$name."/generate","1");
    $info=_("The image PDF creation process has been started. PDF will be available in some time depending on your computer's power");
  }
}

require_once("head.php");

require_once("menu.php");

require_once("menu2.php");

?>
  <div class="container">
<?php require_once("labels.php"); ?>

<form method="post" action="s6_generate.php">
<input type="hidden" name="name" value="<?php echo $name; ?>" />
<h2><?php printf(_("Generating PDF Image for Project '%s'"),he($name)); ?></h2>
<table class="hlist" style="float: left; margin-right: 50px"> 
  <?php foreach($ameta as $key => $attribs) { 
/*
  0: short name   1: long name   2: type 
*/
?>
  <tr><th><?php echo $attribs[0] ?></th><td>
   <?php
  switch ($attribs[2]) { 
  case TYPE_SINGLE:
  echo he($meta[$key]);
  break;
  case TYPE_MULTIPLE:
  $i=0;
  foreach($meta[$key][$i] as $v) {
    echo he($v)."<br />\n";
  }
  break;
  case TYPE_DATE:
  echo he($meta[$key]);
  break;
  case TYPE_EAN13:
  echo he($meta[$key]);
  break;
  } 
?>
  </td></tr>
   <?php } ?>

     <tr><th><?php __("Left images count"); ?></th>
     <td><?php echo $left ?></td></tr>
     <tr><th><?php __("Right images count"); ?></th>
     <td><?php echo $right ?></td></tr>

     <tr><th><?php __("Cropped left images"); ?></th>
     <td><?php echo $cleft ?></td></tr>
     <tr><th><?php __("Cropped right images"); ?></th>
     <td><?php echo $cright ?></td></tr>

<?php 
     // now show the image count and cropping information
?>
  <tr><th></th><td>
<?php 
     if ($cleft==$left && $cright==$right && $left>0 && $right>0) {
       if (file_exists(PROJECT_ROOT."/".$name."/generate")) {
	 echo "<b>"._("PDF creation already in progress, please wait for it...")."</b>";
       } else {
?>
  <button type="submit" class="button" name="go" id="go"><?php  __("Generate this project's PDF"); ?></button>
 <?php } } else {  ?>
   <?php __("No picture or pictures not cropped, can't generate the project's PDF"); ?>
   <?php } ?>
  </td></tr>

  </table>


</div>


<?php
require_once("foot.php");

?>