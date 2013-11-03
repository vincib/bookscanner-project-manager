<?php

function __($str) {
  echo _($str);
}

function eher($k) {
  if (isset($_REQUEST[$k])) echo he($_REQUEST[$k]);
}

function he($str) { return htmlentities($str,ENT_COMPAT,"UTF-8"); } 

function aslr($str) {
  return addslashes($_REQUEST[$str]);
}

/* select_values($arr,$cur) echo des <option> du tableau $values ou de la table sql $values
   selectionne $current par defaut. Par defaut prends les champs 0 comme id et 1 comme 
   donnees pour la table. sinon utilise $info[0] et $info[1].
*/
function eoption($values,$cur,$info="") {
  if (is_array($values)) {
    foreach ($values as $k=>$v) {
      echo "<option value=\"$k\"";
      if ($k==$cur) echo " selected=\"selected\"";
      echo ">".$v."</option>";
    }
  } else {
    if (is_array($info)) {
      $r=mqlist("SELECT ".$info[0].", ".$info[1]." FROM $values ORDER BY ".$info[0].";");
    } else {
      $r=mqlist("SELECT * FROM $values ORDER BY 2;");
    }

    foreach ($r as $c) {
      echo "<option value=\"".$c[0]."\"";
      if ($c[0]==$cur) echo " selected=\"selected\"";
      echo ">".$c[1]."</option>";
    }
  }
}


/* ifcheck($str) simplifie l'affichage des valeur des checkboxes et radioboxes */
function ifcheck($str) { if ($str) echo " checked=\"checked\""; }

function ifselect($str) { if ($str) echo " selected=\"selected\""; }


