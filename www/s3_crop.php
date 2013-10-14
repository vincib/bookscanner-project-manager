<?php

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


  <div class="container">
<?php require_once("labels.php"); ?>

  <h2><?php printf(_("Cropping %s pictures for project '%s'"),$mode,he($name)); ?></h2>
<div class="row my3col"><div class="span2 cropscroll">
<ul>
<?php
  foreach($pics as $pic) {
  $noext=preg_replace('#\.[^\.]*$#','',$pic);
  echo "<li><a href=\"#\" onclick=\"crop('".$noext.".jpg"."')\"><img src=\"".PROJECT_WWW."/".$name."/temp/small".$mode."/".$noext.".jpg\" style=\"height: 96px\" alt=\"".$pic."\" title=\"".$pic."\"></a></li>";
}
?>
</ul>
</div>

<script type="text/javascript">
// select an image for cropping
function crop(i) {
    $('#croppingarea').html('<img src="<?php echo PROJECT_WWW."/".$name."/temp/".$mode."/"; ?>'+i+'" alt="'+i+'" id="croppingimage"/>');
    $('#filename').val(i);
}
</script>

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
<canvas id="croppingcanvas" width="600px" height="576px" style="width: 600px; height: 576px"></canvas>

	</div>
<script type="text/javascript">
    $(document).ready(function () {
	$("#croppingcanvas").click(clickCanvas)
	  })

    /*
    function(e){ 

      if ($("#relx1").val() && $("#relx2").val()) {
	// clear everything
	$("#relx1").val(""); $("#rely1").val("");
	var context=document.getElementById("croppingcanvas");
	context.width=context.width;
      }

      var parentOffset = $(this).offset(); 
      //or $(this).parent().offset(); if you really just want the current element's offset
      var relX = e.pageX - parentOffset.left;
      var relY = e.pageY - parentOffset.top;
      if ($("#relx1").val()) {
	me=2;
      } else {
	me=1;
      }

      $("#relx"+me).val(relX);
      $("#rely"+me).val(relY);
      var context=document.getElementById("croppingcanvas").getContext("2d");
      context.moveTo(relX-10, relY);
      context.lineTo(relX+10, relY);
      context.moveTo(relX, relY-10);
      context.lineTo(relX, relY+10);
      context.strokeStyle = "#F00";
      context.stroke();

      if (me==1 && $("#w").val()) {
	// Automatically draw the rectangle : 

	relX2=relX+parseInt($("#w").val(),10);
	relY2=relY+parseInt($("#h").val(),10);
	$("#relx2").val(relX2);
	$("#rely2").val(relY2);
	var context=document.getElementById("croppingcanvas").getContext("2d");
	context.moveTo(relX-10, relY);
	context.lineTo(relX2, relY);
	context.moveTo(relX, relY-10);
	context.lineTo(relX, relY2);
	context.moveTo(relX2, relY);
	context.lineTo(relX2, relY2+10);
	context.moveTo(relX, relY2);
	context.lineTo(relX2+10, relY2);
	context.strokeStyle = "#F00";
	context.stroke();
	
      }
      $('#go').focus();
    });
    */

</script>
<div class="span4" id="formarea">
  <form method="post" action="s3_crop.php">
    <table><tr><td><?php __("Filename"); ?></td><td><input type="text" name="filename" id="filename" value=""/></td></tr>
<tr>
  <td><?php __("Top X Left"); ?></td>
  <td><input type="text" class="xy" id="relx1" name="relx1" value="" /> X <input type="text" class="xy" id="rely1" name="rely1" value="" /> <button class="button" name="cleartopleft" id="cleartopleft" type="button" onclick="cleartopleft()" shortcut="t" alt="(Alt-t)"><?php __("Clear"); ?></button></td>
</tr>
<tr>
  <td><?php __("Bottom X Right"); ?></td>
  <td><input type="text" class="xy" id="relx2" name="relx2" value="" /> X <input type="text" class="xy" id="rely2" name="rely2" value="" /> <button class="button" name="clearbottomright" id="clearbottomright" type="button" onclick="clearbottomright()" shortcut="b" alt="(Alt-b)"><?php __("Clear"); ?></button></td>
</tr>
<tr>
  <td><?php __("Width X Height"); ?></td>
  <td><input type="text" class="xy" id="w" name="w" value="<?php echo $width; ?>" /> X <input type="text" class="xy" id="h" name="h" value="<?php echo $height; ?>" /> <button class="button" name="clearwidthheight" id="clearwidthheight" type="button" onclick="clearwidthheight()" shortcut="w" alt="(Alt-w)"><?php __("Clear"); ?></button></td>
</tr>
<tr><td colspan="2">
  <input type="submit" id="prev" name="prev" value="<?php __("OK & Previous"); ?>" shortcut="n" alt="(Alt-n)"/>
  <input type="submit" id="next" name="next" value="<?php __("OK & Next"); ?>" shortcut="p" alt="(Alt-p)"/>
</td></tr>
</table>
</form>

</div>


</div>

</div>


<?php
require_once("foot.php");

?>