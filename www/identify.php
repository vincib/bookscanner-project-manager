<?php

// Identify existing left/ and right/ images
// and compute rotated resized thumbnails for CROP/CHECK process
// also identify the existing pictures

// v2: TODO : fork as many processes as we have cores in the system ;) (may not be useful at once, since we are unlikely to have a big machine ;) ) 

//for each project
define("NO_AUTH","1");
require_once("config.php");

$h_projects=opendir(PROJECT_ROOT);
while (($project=readdir($h_projects))!=false) {
  if (is_dir(PROJECT_ROOT."/".$project."/left")) {
    @mkdir(PROJECT_ROOT."/".$project."/temp");
    @mkdir(PROJECT_ROOT."/".$project."/temp/left");
    @mkdir(PROJECT_ROOT."/".$project."/temp/smallleft");
    process(PROJECT_ROOT."/".$project,
	    "left",
	    90);
  }
  if (is_dir(PROJECT_ROOT."/".$project."/right")) {
    @mkdir(PROJECT_ROOT."/".$project."/temp");
    @mkdir(PROJECT_ROOT."/".$project."/temp/right");
    @mkdir(PROJECT_ROOT."/".$project."/temp/smallright");
    process(PROJECT_ROOT."/".$project,
	    "right",
	    270);
  }
}
closedir($h_projects);


// find pictures and rotate + crop them as required
function process($root,$mode,$rotate) {
  echo "Processing $root $mode $rotate\n"; flush();
  $src=$root."/".$mode;
  $dst=$root."/temp/".$mode;
  $dstsmall=$root."/temp/small".$mode;

  $d=opendir($src);
  while (($c=readdir($d))!=false) {
    if (is_file($src."/".$c) &&
	(
	 (!is_file($dst."/".$c) || !is_file($dstsmall."/".$c)) ||
	 (is_file($dst."/".$c) && filemtime($dst."/".$c) < filemtime($src."/".$c) ) ||
	 (is_file($dstsmall."/".$c) && filemtime($dstsmall."/".$c) < filemtime($src."/".$c) )
	 ) 
	) {
      // we need to process this one ... let's do it

      // ../../p/foret/left/IMG_2040.JPG JPEG 4000x3000 4000x3000+0+0 8-bit DirectClass 2.425MB 0.000u 0:00.000
      $noext=preg_replace('#\.[^\.]*$#','',$c);
      unset($out);
      exec("identify ".escapeshellarg($src."/".$c),$out);
      if (preg_match('# ([0-9]+)x([0-9]+) #',$out[0],$mat)) {
	// we have mat[1]=X mat[2]=Y size of the original picture.
	$crop=@json_decode(file_get_contents($root."/crop.json"),true);
	if (!$crop) $crop=array();
	$crop[$mode][$c]["width"]=intval($mat[1]);
	$crop[$mode][$c]["height"]=intval($mat[2]);
	file_put_contents($root."/crop.json",json_encode($crop));
      }
      // now CONVERT
      exec("convert ".escapeshellarg($src."/".$c)." -rotate $rotate -resize 96x -quality 80% ".escapeshellarg($dstsmall."/".$noext.".jpg"));
      exec("convert ".escapeshellarg($src."/".$c)." -rotate $rotate -resize x576 -quality 80% ".escapeshellarg($dst."/".$noext.".jpg"));
      echo "  converted $c\n"; flush();
      }
    }
	
  closedir($d);
}


// 