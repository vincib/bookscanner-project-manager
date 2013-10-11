<?php

?><div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <div class="nav-collapse collapse">
            <ul class="nav">
<?php
$amenu["index.php"] = _("Project List");
$amenu["s0_name.php"] = _("New Project");

foreach($amenu as $link => $menu) {
  if (substr($_SERVER["REQUEST_URI"],1,strlen($link))==$link) 
    $active="active";
  else 
    $active="";
  echo "       <li class=\"".$active."\"><a href=\"".$link."\">".$menu."</a> </li>  \n";
}		
?>
            </ul>
          </div>
        </div>
      </div>
    </div>


<header class="jumbotron subhead" id="overview">
  <div class="container">
  <h1><?php __("Bookscanner Manager"); ?></h1>
  <p class="lead"><?php __("Industrializing the book scanning process"); ?></p>
  </div>
</header>
