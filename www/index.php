<?php

require_once("config.php");

require_once("head.php");

require_once("menu.php");

?>

  <div class="container">
<?php require_once("labels.php"); ?>

  <h2><?php __("Project list"); ?></h2>

<?php
  $d=opendir(PROJECT_ROOT);
$projects=array();
while (($c=readdir($d))!=false) {
  if (substr($c,0,1)!="." && is_dir(PROJECT_ROOT."/".$c)) {
    $projects[$c]=array("name" => $c);
    if (is_file(PROJECT_ROOT."/".$c."/meta.json")) {
      $projects[$c]["meta"]=json_decode(file_get_contents(PROJECT_ROOT."/".$c."/meta.json"),true);
    } else {
      $projects[$c]["meta"]=array();
    }
  }
}
closedir($d);
?>

<table class="hlist"><tr><th></th><th><?php __("Project"); ?></th><th><?php __("Metadata"); ?></th><th><?php __("Status"); ?></th>
<th colspan="4"><?php __("Actions"); ?></th>
</tr>
<?php
foreach($projects as $proj=>$val) {
  echo "<tr>";
  echo "<td><img src=\"";
  if (is_file(PROJECT_ROOT."/".$proj."/tmp/cover.jpg")) 
    echo PROJECT_WWW."/".$proj."/tmp/cover.jpg";
  else
    echo "/default_cover.png";
  echo "\" style=\"height: 80px\"></td>";
  echo "<td>".he($proj)."</td>";
  echo "<td>";  
  foreach($ameta as $k=>$v) {
    if (isset($val["meta"][$k])) {
      if (is_array($val["meta"][$k])) {
	echo "<i>".$v[0].":</i> ".he(implode(", ",$val["meta"][$k]))."<br />";
      } else {
	echo "<i>".$v[0].":</i> ".he($val["meta"][$k])."<br />";
      }
    }
  }
  if (!count($val["meta"])) echo "<i>no metadata</i>";
  echo "</td>";
  echo "<td>";
  if (is_file(PROJECT_ROOT."/".$proj."/status")) {
    echo $asteps[trim(file_get_contents(PROJECT_ROOT."/".$proj."/status"))];
  }
  echo "</td>";
  echo "<td><a href=\"s0_name.php?rename=".urlencode($proj)."\">"._("Rename")."</a></td>";
  echo "<td><a href=\"s1_meta.php?name=".urlencode($proj)."\">"._("Metadata")."</a></td>";
  echo "<td><a href=\"s2_scan.php?name=".urlencode($proj)."\">"._("Scan")."</a></td>";
  echo "<td><a href=\"s3_check.php?name=".urlencode($proj)."\">"._("Check")."</a></td>";
  echo "</tr>";
}
?>
</table>

</div>


<?php
require_once("foot.php");

?>