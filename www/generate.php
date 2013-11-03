<?php

// Generate Image PDF when asked for...
$convertparams=" -quality 90% -modulate 160 -sigmoidal-contrast 10x80% -colors 2 ";

//for each project
define("NO_AUTH","1");
require_once("config.php");

function dogeom($a) {
  $width=$a["height"];  $height=$a["width"];  // yes > we rotate the pictures so we need that :)
  $left=$a["left"]; $right=$a["right"];
  $top=$a["top"]; $bottom=$a["bottom"];
  // Compute the appropriate Geometry string for Convert 
  // when asked for the left/right/top/bottom cropping of the image
  // having width/height size, *BUT* the cropping area is made from a x576 pixels image.

  // first we compute the width of the small image.
  $h2=576;
  $w2=$width*$h2/$height;
  // then we compute the geometry string : widthxheight+left+top
  $geometry="";
  $geometry.= intval( ($right-$left)*$width/$w2 );
  $geometry.= "x";
  $geometry.= intval( ($bottom-$top)*$height/$h2 );
  $geometry.= "+";
  $geometry.= intval( ($left)*$width/$w2 );
  $geometry.= "+";
  $geometry.= intval( ($top)*$height/$h2 );
  return $geometry;
}


$h_projects=opendir(PROJECT_ROOT);
while (($project=readdir($h_projects))!=false) {
  if (file_exists(PROJECT_ROOT."/".$project."/generate")) {
    // ok, let's read CROP and process all the files
    $crop=json_decode(file_get_contents(PROJECT_ROOT."/".$project."/crop.json"),true);
    $cleft=$crop["left"];
    $cright=$crop["right"];
    ksort($cleft);
    ksort($cright);
    // LEFT pictures must be rotated 90°,   RIGHT pictures must be rotated 270°
    reset($cleft); reset($cright);
    $image=0;
    $totimage=count($cleft)+count($cright);
    @mkdir(PROJECT_ROOT."/".$project."/temp/generator");
    do {
      $oneleft=each($cleft);
      $oneright=each($cright);
      $stilltodo=is_array($oneleft);
      if ($stilltodo) {
	// Rotate and Crop a picture
	if ($image!=0) {
	  // do the right picture (but not if it's the first one (cover))
	  $geometry=dogeom($oneright[1]);	
	  $exe="convert ".escapeshellarg(PROJECT_ROOT."/".$project."/right/".$oneright[0])." -rotate 270 -crop $geometry $convertparams ".escapeshellarg(PROJECT_ROOT."/".$project."/temp/generator/".sprintf("%05d",$image).".jpg");
	  echo "EXEC: $exe\n";
	  exec($exe);
	  $image++;
	}
	// Do the left one (but not if it's the last one (cover))
	if ($image!=($totimage-2)) {
	  $geometry=dogeom($oneleft[1]);
	  $exe="convert ".escapeshellarg(PROJECT_ROOT."/".$project."/left/".$oneleft[0])." -rotate 90 -crop $geometry $convertparams ".escapeshellarg(PROJECT_ROOT."/".$project."/temp/generator/".sprintf("%05d",$image).".jpg");
	  echo "EXEC: $exe\n";
	  exec($exe);
	  $image++;
	}
      }
    } while ($stilltodo);
    echo "Doing book.pdf for $project...\n";
    $exe="convert ".escapeshellarg(PROJECT_ROOT."/".$project."/temp/generator/")."* ".escapeshellarg(PROJECT_ROOT."/".$project."/book.pdf");
    echo "EXEC: $exe\n";
    exec($exe);
    echo "Done\n";
    @unlink(PROJECT_ROOT."/".$project."/generate");
  } // shall we generate pdf for this one ? 

}
closedir($h_projects);

