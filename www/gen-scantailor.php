<?php

// Params : out (file to save into)  substitute (path of the projets in the machine that will use scantailor)   name (project to generate)

require_once("config.php");

$_REQUEST["name"]="damasio-zone";

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

$meta=json_decode(file_get_contents(PROJECT_ROOT."/".$name."/meta.json"),true);

if (!is_dir(PROJECT_ROOT."/".$name."/left")) 
  mkdir(PROJECT_ROOT."/".$name."/left",0777);
if (!is_dir(PROJECT_ROOT."/".$name."/right")) 
  mkdir(PROJECT_ROOT."/".$name."/right",0777);
if (!is_dir(PROJECT_ROOT."/".$name."/book")) 
  mkdir(PROJECT_ROOT."/".$name."/book",0777);


if (isset($_REQUEST["substitute"]) && $_REQUEST["substitute"]) {
  define("XML_ROOT",$_REQUEST["substitute"]);
} else {
  define("XML_ROOT",PROJECT_ROOT);
}

$left=0; $right=0;
$allleft=array(); $allright=array();

$d=opendir(PROJECT_ROOT."/".$name."/left");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/left/".$c)) {
    $left++;
    $allleft[]=$c;
  }
}
closedir($d);
$d=opendir(PROJECT_ROOT."/".$name."/right");
while (($c=readdir($d))!==false) {
  if (is_file(PROJECT_ROOT."/".$name."/right/".$c)) {
    $right++;
    $allright[]=$c;
  }
}
closedir($d);

sort($allleft);
sort($allright);

if (!$right && !$left) {
  echo "No Picture File in this project !!";
  exit();
}

$id=1;

// We purge the book/ subfolder in the project folder.
exec("rm -rf ".escapeshellarg(PROJECT_ROOT."/".$name."/book"));
mkdir(PROJECT_ROOT."/".$name."/book",0777);
exec("rm -rf ".escapeshellarg(PROJECT_ROOT."/".$name."/booktif"));
mkdir(PROJECT_ROOT."/".$name."/booktif",0777);

ob_start();
?>
<project outputDirectory="<?php echo XML_ROOT."/".$name."/booktif"; ?>" layoutDirection="LTR">
  <directories>
    <directory path="<?php echo XML_ROOT."/".$name."/book"; ?>" id="<?php echo $id++; ?>"/>
  </directories>
  <files>
  <?php $firstfileid=$id; ?>
  <?php $found=true;
  reset($allleft);
  reset($allright);
$images=array();
while ($found) {
  $found=false;
  if ($v=each($allleft)) {

    $s=bsm_imagesize(PROJECT_ROOT."/".$name."/left/".$v[1]);
    if (count($s)==2) {
      // We symlink every picture file from the first left one from page 1
      symlink("../left/".$v[1],PROJECT_ROOT."/".$name."/book/i".sprintf("%05d",$page).".jpg");
      echo '    <file dirId="1" id="'.$id.'" name="'.'i'.sprintf("%05d",$page).'.jpg'.'"/>
';
      $images[$id]=array("name" => 'i'.sprintf("%05d",$page).'.jpg', "width" => $s[0], "height" => $s[1], "rotate" => 90,
			 "original" => $v[1]);
      $id++;
      $page++;
      $found=true;
    }
  }
  if ($v=each($allright)) {
    $s=bsm_imagesize(PROJECT_ROOT."/".$name."/right/".$v[1]);
    if (count($s)==2) {
      symlink("../right/".$v[1],PROJECT_ROOT."/".$name."/book/i".sprintf("%05d",$page).".jpg");
      echo '    <file dirId="1" id="'.$id.'" name="'.'i'.sprintf("%05d",$page).'.jpg'.'"/>
';
      $images[$id]=array("name" => 'i'.sprintf("%05d",$page).'.jpg', "width" => $s[0], "height" => $s[1], "rotate" => 270,
			 "original" => $v[1]);
      $id++;
      $page++;
      $found=true;
    }
  }
}
$lastfileid=$id;
?>
  </files>
  <images>
<?php
  $firstimageid=$id;
foreach($images as $i=>$image) { ?>
    <image subPages="1" fileImage="0" fileId="<?php echo $i; ?>" id="<?php echo $id; ?>">
      <size width="<?php echo $image["width"]; ?>" height="<?php echo $image["height"]; ?>"/>
      <dpi vertical="300" horizontal="300"/>
    </image>
      <?php 
    $images[$i]["id"]=$id;
      $id++;
}
$lastimageid=$id;
 ?>
  </images>
  <pages>
<?php
  $first=true;
  foreach($images as $i=>$image) { ?>
    <page imageId="<?php echo $image["id"]; ?>" subPage="single"<?php if ($first) echo " selected=\"selected\""; ?> id="<?php echo $id; ?>"/>
<?php
    $first=false;
    $images[$i]["page"]=$id;
    $id++;
  }
?>
  </pages>
  <file-name-disambiguation>
<?php
  foreach($images as $i=>$image) { 
    echo "<mapping file=\"".$i."\" label=\"0\"/>\n";
  }
?>
  </file-name-disambiguation>
  <filters>
    <fix-orientation>
<?php
    //print_r($images);
  foreach($images as $i=>$image) {
    echo "<image id=\"".$image["id"]."\">
        <rotation degrees=\"".$image["rotate"]."\"/>
      </image>
";
  }
?>
    </fix-orientation>
    <page-split defaultLayoutType="single-cut">
<?php
	foreach($images as $i=>$image) {
?>
      <image layoutType="auto-detect" id="<?php echo $image["id"]; ?>">
        <params mode="auto">
          <pages type="single-cut">
            <outline>
              <point x="0" y="0"/>
              <point x="<?php echo $image["height"]; ?>" y="0"/>
              <point x="<?php echo $image["height"]; ?>" y="<?php echo $image["width"]; ?>"/>
              <point x="0" y="<?php echo $image["width"]; ?>"/>
              <point x="0" y="0"/>
            </outline> 
            <cutter1>
              <p1 x="0" y="<?php echo $image["width"]; ?>"/>
              <p2 x="0" y="0"/>
            </cutter1>
            <cutter2>
              <p1 x="<?php echo $image["height"]; ?>" y="<?php echo $image["width"]; ?>"/>
              <p2 x="<?php echo $image["height"]; ?>" y="0"/>
            </cutter2>
          </pages>
          <dependencies>
            <rotation degrees="<?php echo $image["rotate"]; ?>"/>
            <size width="<?php echo $image["width"]; ?>" height="<?php echo $image["height"]; ?>"/>
            <layoutType>auto-detect</layoutType>
          </dependencies>
        </params>
      </image>
<?php } ?>
    </page-split>

    <deskew/>

    <select-content/>

    <page-layout/>

    <output>
	<?php foreach($images as $i=>$image) { ?>
      <page id="<?php echo $image["page"]; ?>">
        <zones/>
        <fill-zones/>
        <params depthPerception="2" despeckleLevel="cautious" dewarpingMode="off">
          <dpi vertical="300" horizontal="300"/>
          <color-params colorMode="bw">
            <color-or-grayscale whiteMargins="0" normalizeIllumination="0"/>
            <bw thresholdAdj="0"/>
          </color-params>
        </params>
      </page>
	<?php } ?>
    </output>
  </filters>
</project>
<?php

    if (isset($_REQUEST["out"])) {
      $out=str_replace("/","",str_replace("..","",$_REQUEST["out"]));
      file_put_contents(PROJECT_ROOT."/".$name."/".$out,ob_get_clean());
    }

?>