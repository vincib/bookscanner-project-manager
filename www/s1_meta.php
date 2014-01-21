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
  $json=array();
  foreach($_POST as $key=>$val) {
    if($key!="name" && $val) {
      if (!is_array($val)) {
	$json[$key]=$val;
      } else {
	$tmp=array();
	foreach($val as $k=>$v) {
	  if ($v) $tmp[]=$v;
	}
	$json[$key]=$tmp;
      }
    }
  }
  if (count($json)) {
    file_put_contents(PROJECT_ROOT."/".$name."/meta.json",json_encode($json));
    if (!is_file(PROJECT_ROOT."/".$name."/status")) {
      file_put_contents(PROJECT_ROOT."/".$name."/status","TOSCAN");
    }
    $success=sprintf(_("Metadata for project '%s' successfully edited"),$name);
  } else {
    $warning=sprintf(_("Metadata for project '%s' not changed: nothing submitted"),$name);
  }
  require("s2_scan.php");
  exit();
}

require_once("head.php");

require_once("menu.php");

$c=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);
foreach($c as $key=>$val) {
  if ($key!="name") {
    $_REQUEST[$key]=$val;
  }
}

require_once("menu2.php");
?>

  <div class="container">
<?php require_once("labels.php"); ?>

<form method="post" action="s1_meta.php">
<input type="hidden" name="name" value="<?php echo $name; ?>" />
<h2><?php printf(_("Editing metadata of project '%s'"),he($name)); ?></h2>
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
  echo "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"".he($_REQUEST[$key])."\" class=\"fmetatext\" onfocus=\"help('".addslashes($attribs[1])."')\" />";
  break;
  case TYPE_MULTIPLE:
  $i=0;
  echo "<input type=\"text\" name=\"".$key."[]\" id=\"".$key."_".$i."\" value=\"".he($_REQUEST[$key][$i])."\"  onfocus=\"help('".addslashes($attribs[1])."')\"  class=\"fmetatext\" />";
  echo " <a href=\"#\" onclick=\"$('#more_".$key."').show(); return false;\">"._("more...")."</a>";
  $i++;
  echo "<div id=\"more_".$key."\"";
  if (count($_REQUEST[$key])<=1) {
    echo " style=\"display:none\"";
  }
  echo ">\n";
  for(;$i<10;$i++) {
    echo "<input type=\"text\" name=\"".$key."[]\" id=\"".$key."_".$i."\" value=\"".he($_REQUEST[$key][$i])."\"  onfocus=\"help('".addslashes($attribs[1])."')\" class=\"fmetatext\" />";
    echo "<br />";
  }
  echo "</div>";
  break;
  case TYPE_DATE:
  echo "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"".he($_REQUEST[$key])."\"  onfocus=\"help('".addslashes($attribs[1])."')\" class=\"fmetadate\" /> "._("(yyyy or yyyy-mm or yyyy-mm-dd)");
  break;
  case TYPE_EAN13:
  echo "<input type=\"text\" name=\"$key\" id=\"$key\" value=\"".he($_REQUEST[$key])."\"  onfocus=\"help('".addslashes($attribs[1])."')\" class=\"fmetaean\" /> "._("(barcode, often starting by 978, no '-')");
  break;
  } 
?>
  </td></tr>
   <?php } ?>

  <tr><th></th><td>
  <button type="submit" class="button" name="go" id="go"><?php  __("Save this project's Metadata"); ?>"</button>
  </td></tr>

  </table>

<div class="thint">
  <div class="pull-left alert" id="help">
</div>
</div>


</div>


<?php
require_once("foot.php");

?>