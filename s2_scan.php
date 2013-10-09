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

require_once("head.php");

require_once("menu.php");

$meta=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);

?>

  <div class="container">
<?php require_once("labels.php"); ?>

<h2><?php printf(_("Scanning project '%s'"),he($name)); ?></h2>
<div class="row my3col">

<div class="span4">
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
$d=opendir(PROJECT_ROOT."/".$name."right");
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
  echo "<a href=\"".PROJECT_WWW."/".$name."/left/".$lastLeftFile."\" target=\"blank\"><img src=\"".PROJECT_WWW."/".$name."/left/".$lastLeftFile."\" style=\"width: 100px;\" title=\""._("Last LEFT picture, click to see it")."\">";
}

if ($right) {
  echo "<a href=\"".PROJECT_WWW."/".$name."/left/".$lastLeftFile."\" target=\"blank\"><img src=\"".PROJECT_WWW."/".$name."/left/".$lastLeftFile."\" style=\"width: 100px;\" title=\""._("Last RIGHT picture, click to see it")."\">";
}

?>
</div>
<div class="span4">
  <h4><?php __("State of the scanning session"); ?></h4>
  Camera : 
<div id="camerastatus" />

</div>
<div class="span4">
  <h4><?php __("Actions"); ?></h4>
  <button class="button" type="button" name="resetzoom" id="resetzoom" onclick="resetzoom()" /><?php __("Reset Zoom"); ?></button>
  <button class="button" type="button" name="zoomin" id="zoomin" onclick="zoomin()" /><?php __("Zoom IN"); ?></button>
  <button class="button" type="button" name="zoomout" id="zoomout" onclick="zoomout()" /><?php __("Zoom OUT"); ?></button>
</div>

</div>

</div>


<?php
require_once("foot.php");

?>