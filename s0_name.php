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
	$success=_("Project successfully renamed");
	require("index.php");
	exit();
      } else {
	mkdir( PROJECT_ROOT."/".$name );
	$success=_("Project successfully created, please set its metadata");
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

?>

  <div class="container">
<?php require_once("labels.php"); ?>

<?php 
  if ($isrename) 
    echo "<h2>Renaming project '".he($_REQUEST["rename"])."'</h2>";
  else 
    echo "<h2>New Book scanning project</h2>";
?>

<form method="post" action="s0_name.php">
  <?php if ($isrename) { ?>
<input type="hidden" name="rename" value="<?php eher("rename"); ?>"/>
  <?php } ?>
<table class="hlist"> 
  <tr><th><?php __("Project Name"); ?></th><td>
  <input type="text" name="name" id="name" value="<?php eher("name"); ?>" class="fmeta" />
  </td></tr>

  <tr><th></th><td>
  <input type="submit" name="go" id="go" value="<?php if ($isrename)  __("Rename this project"); else __("Create this project"); ?>" class="fmeta" />
  </td></tr>

  </table>

</div>


<?php
require_once("foot.php");

?>