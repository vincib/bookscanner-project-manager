<?php

require_once("config.php");

// limit access to this page from localhost only : 
if ($_SERVER["REMOTE_ADDR"]!="127.0.0.1") {
  require_once("s2_scan_no.php"); 
  exit();
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

require_once("head.php");

require_once("menu.php");

$meta=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);

require_once("menu2.php");
?>


  <div class="container">
<?php require_once("labels.php"); ?>

<h2><?php printf(_("Scanning project '%s'"),he($name)); ?></h2>
<div class="row my3col">

<div class="span6">
  <h4><?php __("Current state of the project"); ?></h4>
<?php
  if (!is_dir(PROJECT_ROOT."/".$name."/left")) 
    mkdir(PROJECT_ROOT."/".$name."/left");
  if (!is_dir(PROJECT_ROOT."/".$name."/right")) 
    mkdir(PROJECT_ROOT."/".$name."/right");

$left=0; $right=0;
$lastLeft=0; $lastLeftFile="";
$lastRight=0; $lastRightFile="";

$d=opendir(PROJECT_ROOT."/".$name."/left");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/left/".$c)) $left++;
  if (filemtime(PROJECT_ROOT."/".$name."/left/".$c)>$lastLeft) $lastLeftFile=$c;
}
closedir($d);
$d=opendir(PROJECT_ROOT."/".$name."/right");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/right/".$c)) $right++;
  if (filemtime(PROJECT_ROOT."/".$name."/right/".$c)>$lastRight) $lastRightFile=$c;
}
closedir($d);

echo "<p>";
printf(_("%s left pictures present")."<br />",$left);
printf(_("%s right pictures present")."<br />",$right);
echo "</p>\n";

if ($left) {
  echo "<a href=\"".PROJECT_WWW."/".$name."/left/".$lastLeftFile."\" target=\"blank\"><img src=\"".PROJECT_WWW."/".$name."/left/".$lastLeftFile."\" style=\"width: 100px;\" title=\""._("Last LEFT picture, click to see it")."\"></a>";
}

if ($right) {
  echo "<a href=\"".PROJECT_WWW."/".$name."/right/".$lastRightFile."\" target=\"blank\"><img src=\"".PROJECT_WWW."/".$name."/right/".$lastRightFile."\" style=\"width: 100px;\" title=\""._("Last RIGHT picture, click to see it")."\"></a>";
}

?>
  <h4><?php __("State of the scanning session"); ?></h4>

<div id="camerastatus" />

</div>
<script type="text/javascript">
  $().ready(function() {
      cam_search()
    })
</script>
	</div>


<div class="span6">
   <h4><?php __("Actions"); ?></h4>
<?php
				/*
 <p>
  <button class="button" type="button" name="search" id="search" onclick="iamlost()" /><?php __("I am lost, reset everything"); ?></button>
</p> 
				*/
?>
<p>
  <button class="button" type="button" name="search" id="search" onclick="cam_search()" /><?php __("Search for Cameras"); ?></button>
</p><p>
  <button class="button" type="button" name="prepare" id="prepare" onclick="cam_prepare()" /><?php __("Prepare cam (before zooming/shooting)"); ?></button>
</p><p>
  <button class="button" type="button" name="zoomin" id="zoomin" onclick="cam_zoomin(1)" /><?php __("Zoom IN"); ?></button>
  <button class="button" type="button" name="zoomout" id="zoomout" onclick="cam_zoomout(1)" /><?php __("Zoom OUT"); ?></button>
</p><p>
  <button class="button" type="button" name="shoot" id="shoot" onclick="cam_shoot('<?php echo addslashes($name); ?>',0)" /><?php __("Shoot Pictures!"); ?></button>
  <button class="button" type="button" name="shoot" id="shoot" onclick="cam_shoot('<?php echo addslashes($name); ?>',1)" /><?php __("Shoot Pictures and get Files!"); ?></button>
</p><p>
  <button class="button" type="button" name="get" id="get" onclick="cam_get('<?php echo addslashes($name); ?>')" /><?php __("Get Files"); ?></button>
</p>
</div>

</div>

</div>


<?php
require_once("foot.php");

?>