<?php
require_once("head.php");
require_once("menu.php");
require_once("menu2.php");
?>


  <div class="container">
<?php require_once("labels.php"); ?>

<h2><?php printf(_("Scanning project '%s'"),he($name)); ?></h2>
<div class="row my3col">

<div class="span12">
     <h2><?php __("This page can be accessed only from the raspberry itself. Thanks"); ?></2>
</div>
</div>
</div>
<?php
require_once("foot.php");
?>