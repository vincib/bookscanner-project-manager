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

if (isset($_POST["name"])) {
  // posted data ... check them, fix them, post them into json object
}

require_once("head.php");

require_once("menu.php");

$meta=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);

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
  echo he($_REQUEST[$key]);
  break;
  case TYPE_MULTIPLE:
  $i=0;
  foreach($_REQUEST[$key][$i] as $v) {
    echo he($v)."<br />\n";
  }
  break;
  case TYPE_DATE:
  echo he($_REQUEST[$key]);
  break;
  case TYPE_EAN13:
  echo he($_REQUEST[$key]);
  break;
  } 
?>
  </td></tr>
   <?php } ?>

<?php 
     // now show the image count and cropping information
?>
  <tr><th></th><td>
  <button type="submit" class="button" name="go" id="go"><?php  __("Generate this project's PDF"); ?></button>
  </td></tr>

  </table>

<div class="thint">
  <div class="pull-left alert" id="help">
<?php

if (is_file(PROJECT_ROOT."/".$name."/book.pdf")) {
echo "<a href=\"".PROJECT_WWW."/".$name."/book.pdf\">"._("Download that book")."</a>\n";
}
?>
</div>
</div>


</div>


<?php
require_once("foot.php");

?>