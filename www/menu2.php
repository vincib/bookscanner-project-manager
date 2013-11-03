<div class="span2">
<ul class="nav nav-pills nav-stacked">
   <li><?php __("Project Steps"); ?></li>
<?php
  $asub=array(
	      _("Rename")=>"s0_name.php?rename=",
	      _("Metadata")=>"s1_meta.php?name=",
	      _("Scan")=>"s2_scan.php?name=",
	      _("Crop Left")=>"s3_crop_left.php?name=",
	      _("Crop Right")=>"s3_crop_right.php?name=",
	      _("Generate")=>"s6_generate.php?name=",
	      );
foreach($asub as $n=>$l) {
  if (substr($_SERVER["REQUEST_URI"],1,strlen($l))==$l) 
    $active="active";
  else 
    $active="";
  echo "       <li class=\"".$active."\"><a href=\"".$l.he($name)."\">".$n."</a> </li>  \n";
}
?>
</ul>
</div>
