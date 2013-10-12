<div class="span2">
<ul class="nav nav-pills nav-stacked">
   <li><?php __("Project Steps"); ?></li>
<?php
  $asub=array(
	      _("Rename")=>"s0_name.php?rename=",
	      _("Metadata")=>"s1_meta.php?name=",
	      _("Scan")=>"s2_scan.php?name=",
	      _("Check")=>"s3_check.php?name=",
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
