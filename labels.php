<?php

if (!empty($success) || !empty($warning) 
    || !empty($error) || !empty($info)
    ) {
?>
  <div class="alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
<?php
  if (!empty($success)) {  echo " <strong>Success</strong> ".$success."<br />"; }
  if (!empty($warning)) {  echo " <strong>Warning</strong> ".$warning."<br />"; }
  if (!empty($error)) {  echo " <strong>Error</strong> ".$error."<br />"; }
  if (!empty($info)) {  echo " <strong>Info</strong> ".$info."<br />"; }
?>
    </div>
<?php 
    }
?>
