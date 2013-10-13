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
}
</script>
<div class="span8" id="croppingarea">

	</div>


</div>

</div>


<?php
require_once("foot.php");

?>