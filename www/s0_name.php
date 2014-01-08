<?php

require_once("config.php");

if (isset($_POST["name"])) {
  $name=strtolower(trim($_POST["name"]));
  if (!preg_match(PROJECT_PREG,$name)) {
    $error=_("Name must contain only a-z 0-9 - , .");
  }  else {
    if (isset($_REQUEST["rename"])) {
      $rename=strtolower(trim($_REQUEST["rename"]));
      if ($name==$rename) {
	$error=_("Nothing done, no change requested");
	require("index.php");
	exit();
      }
      if (!preg_match(PROJECT_PREG,$rename)) { 
	$error=_("BAD OLD NAME, this shall not happen ...");
	require("index.php");
	exit();
      }
    } // rename ? 
    if (file_exists(PROJECT_ROOT."/".$name)) {
      $error=_("A project with that name already exists, please choose another name");
    } else {
      // Name or rename it
      if (isset($_REQUEST["rename"])) {
	rename( PROJECT_ROOT."/".$rename, PROJECT_ROOT."/".$name );
	if (is_file(PROJECT_ROOT."/".$name."/status")) touch(PROJECT_ROOT."/".$name."/status");
	$success=_("Project successfully renamed");
	$_REQUEST["name"]=$name;
	unset($_POST);
	$_SERVER["REQUEST_URI"]="s1_meta.php";
	require("s1_meta.php");
	exit();
      } else {
	mkdir( PROJECT_ROOT."/".$name );
	$success=_("Project successfully created, please set its metadata");
	unset($_POST);
	$_SERVER["REQUEST_URI"]="s1_meta.php";
	require("s1_meta.php");
	exit();
      }
    } // already
  } // preg for name
}


require_once("head.php");

require_once("menu.php");

$isrename=false;
if (isset($_REQUEST["rename"])) {
  $isrename=true;
  $_REQUEST["name"]=$_REQUEST["rename"];
}

$name=$_REQUEST["rename"];
if ($name) 
  require_once("menu2.php");

?>

  <div class="container">
<?php require_once("labels.php"); ?>

<?php 
  if ($isrename) 
    echo "<h2>".sprintf(_("Renaming project '%s'"),he($_REQUEST["rename"]))."</h2>";
  else 
    echo "<h2>"._("New book scanning project")."</h2>";
?>
<p>
<?php __("Enter a name for this book scanning project. A folder with that name will be created. You may use only a-z A-Z 0-9 and -"); ?>
<form method="post" action="s0_name.php">
  <?php if ($isrename) { ?>
<input type="hidden" name="rename" value="<?php eher("rename"); ?>"/>
  <?php } ?>
</p>
<p>
<?php __("Project Name"); ?>

  <input type="text" name="name" id="name" value="<?php eher("name"); ?>" class="fmeta" />
</p>
<p>
  <input type="submit" name="go" id="go" value="<?php if ($isrename)  __("Rename this project"); else __("Create this project"); ?>" class="fmeta" />
</p>

</div>
<script type="text/javascript">
  $().ready(function() {
      $('#name').focus();
    })
</script>


<?php
require_once("foot.php");

?>