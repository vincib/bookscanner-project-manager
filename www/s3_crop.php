<?php

// TODO: allow deleting / reordering of images.
require_once("config.php");

if (!isset($mode) || ($mode!="left" && $mode!="right")) {
  $error=_("Mode is missing");
  require("index.php");
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

$d=@opendir(PROJECT_ROOT."/".$name."/".$mode);
if (!$d) {
  $error=sprintf(_("Folder %s not found, you should scan pages first"),$mode);
  require("index.php");
  exit();
}
while (($c=readdir($d))!=false) {
  if (is_file(PROJECT_ROOT."/".$name."/".$mode."/".$c)) {
    $pics[]=$c;
  }
}
closedir($d);
sort($pics);
require_once("head.php");

require_once("menu.php");

$meta=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);

require_once("menu2.php");
?>


<?php require_once("labels.php"); ?>

  <h2><?php printf(_("Cropping %s pictures for project '%s'"),$mode,he($name)); ?></h2>
<div class="row my3col"><div class="span2 cropscroll" id="cropscroll">
<ul>
<?php
  $i=1;
  foreach($pics as $pic) {
    echo "<li><a href=\"#\" onclick=\"crop('".$pic."',".$i.",'".addslashes(PROJECT_WWW)."','".addslashes($name)."','".$mode."')\" id=\"cl".$i."\"><img  id=\"cr".$i."\" src=\"".PROJECT_WWW."/".$name."/temp/small".$mode."/".$pic."\" style=\"height: 96px\" alt=\"".$pic."\" title=\"".$pic."\"></a></li>";
  $i++;
}
?>
</ul>
</div>

<style type="text/css">
  #croppingcanvas,  #croppingarea {
position: absolute;
left: 0; top: 0;
width: 600px; height: 576px;
}
#croppingarea {
  z-index: 2;
  float: left;
}
#croppingcanvas {
  z-index: 3;
}
#cropzone {
position: relative;
}
</style>
<div class="span6" id="cropzone">

<div id="croppingarea"></div>
<canvas id="croppingcanvas" width="500px" height="576px" style="width: 500px; height: 576px"></canvas>

	</div>
<script type="text/javascript">
    $(document).ready(function () {
	$("#croppingcanvas").click(clickCanvas);
	  $('#cl1').click();
	  })
</script>
<div class="span4" id="formarea">
  <form method="post" action="s3_crop.php">
    <table><tr><td><?php __("Filename"); ?></td><td><input type="text" name="filename" id="filename" value=""/></td></tr>
<tr>
  <td><?php __("Top X Left"); ?></td>
  <td><input type="text" class="xy" id="relx1" name="relx1" value="" /> X <input type="text" class="xy" id="rely1" name="rely1" value="" /> <button class="button" name="btncleartopleft" id="btncleartopleft" type="button" onclick="cleartopleft()" shortcut="t" alt="(Alt-t)"><?php __("Clear"); ?></button></td>
</tr>
<tr>
  <td><?php __("Bottom X Right"); ?></td>
  <td><input type="text" class="xy" id="relx2" name="relx2" value="" /> X <input type="text" class="xy" id="rely2" name="rely2" value="" /> <button class="button" name="btnclearbottomright" id="btnclearbottomright" type="button" onclick="clearbottomright()" shortcut="b" alt="(Alt-b)"><?php __("Clear"); ?></button></td>
</tr>
<tr>
  <td><?php __("Width X Height"); ?></td>
  <td><input type="text" class="xy" id="w" name="w" value="<?php echo $width; ?>" /> X <input type="text" class="xy" id="h" name="h" value="<?php echo $height; ?>" /> <button class="button" name="btnclearwidthheight" id="btnclearwidthheight" type="button" onclick="clearwidthheight()" shortcut="w" alt="(Alt-w)"><?php __("Clear"); ?></button></td>
</tr>
<tr><td colspan="2">
<p>
  <button type="button" id="prev" name="prev" shortcut="n" alt="(Alt-n)" onclick="submit_crop(1,'<?php echo $mode; ?>','<?php echo addslashes($name); ?>')"><?php __("OK & Previous"); ?></button>
  <button type="button" id="next" name="next" shortcut="p" alt="(Alt-p)" onclick="submit_crop(2,'<?php echo $mode; ?>','<?php echo addslashes($name); ?>')"><?php __("OK & Next"); ?></button>
</p>
<p>
  <button type="button" id="allnext" name="allnext" shortcut="a" alt="(Alt-a)" onclick="submit_crop(3,'<?php echo $mode; ?>','<?php echo addslashes($name); ?>')"><?php __("Apply to this one and all following"); ?></button>
</p>
</td></tr>
</table>
</form>

  <div id="submitmsg">
  </div>

</div>


</div>


<?php
require_once("foot.php");

?>