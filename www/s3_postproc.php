<?php

require_once("config.php");

if (isset($_REQUEST["stgo"])) {
  $_REQUEST["out"]=$_REQUEST["name"].".scantailor";
  require_once("gen-scantailor.php");
  $info = _("Scantailor file created.");
}

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

  if (!is_dir(PROJECT_ROOT."/".$name."/left")) 
    mkdir(PROJECT_ROOT."/".$name."/left");
  if (!is_dir(PROJECT_ROOT."/".$name."/right")) 
    mkdir(PROJECT_ROOT."/".$name."/right");

$left=0; $right=0;
$lastLeft=0; $lastLeftFile="";
$lastRight=0; $lastRightFile="";

$d=opendir(PROJECT_ROOT."/".$name."/left");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/left/".$c)) {
    $left++;
  }
  if (filemtime(PROJECT_ROOT."/".$name."/left/".$c)>$lastLeft) $lastLeftFile=$c;
}
closedir($d);
$d=opendir(PROJECT_ROOT."/".$name."/right");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/right/".$c)) {
    $right++;
  }
  if (filemtime(PROJECT_ROOT."/".$name."/right/".$c)>$lastRight) $lastRightFile=$c;
}
closedir($d);



require_once("head.php");

require_once("menu.php");

require_once("menu2.php");

?>
  <div class="container">
<?php require_once("labels.php"); ?>

<form method="post" action="s3_postproc.php">
<input type="hidden" name="name" value="<?php echo $name; ?>" />
<h2><?php printf(_("Post processing for project '%s'"),he($name)); ?></h2>

<p>
  <?php __("Please check that you have all pictures in the right order on both Left and Right folders before launching Scantailor. Remove all unnecessary pictures at the beginning of the 'right' folder and at the end of the 'left' folder."); ?> 
  <?php __("We will start by the first picture in the left folder, which is usually the cover of the book."); ?>
</p>

<table class="hlist" style="float: left; margin-right: 50px"> 
  <?php

 foreach($ameta as $key => $attribs) { 
/*
  0: short name   1: long name   2: type 
*/
?>
  <tr><th><?php echo $attribs[0] ?></th><td>
   <?php
  switch ($attribs[2]) { 
  case TYPE_LIST:
  $list="a_list_".$key;
  echo $GLOBALS[$list][$meta[$key]];
  break;
  case TYPE_SINGLE:
  echo he($meta[$key]);
  break;
  case TYPE_MULTIPLE:
  $i=0;
  foreach($meta[$key] as $v) {
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

  <tr><td colspan="2">
<?php __("If scantailor will be launched on another computer, set this field to the path of all the projects<br />Example: on windows you could set it to <code>z:\\</code>, on Linux/mac, this could be <code>/mnt</code>"); echo "<br>"; ?>
</td></tr>
  <tr><th><?php __("Substitute"); ?></th>
  <td>
  <input type="text" name="substitute" style="width: 300px" value="" />
  </td></tr>

<?php 
     // now show the image count and cropping information
?>
  <tr><th></th><td>
<?php 
     if ($left>0 && $right>0) {
       $already=false;
       if (file_exists(PROJECT_ROOT."/".$name."/".$name.".scantailor")) {
	 echo "<p><b>"._("Scantailor file already generated. You can use it or regenerate it")."</b></p>";
	 $already=true;
       } 
?>
  <button type="submit" class="button" name="stgo" id="stgo"
       <?php if ($already) { ?>
       onclick="return confirm('<?php __("This will delete all the existing files in the book/ folder and subfolder. Please confirm."); ?>');" 
       <?php } ?>
       ><?php  __("Enhance this project's images through Scantailor"); ?></button>
 <?php } else {  ?>
   <?php __("No picture, can't enhance the project's pictures"); ?>
   <?php } ?>
  </td></tr>
  </table>

</div>

<?php
require_once("foot.php");
?>