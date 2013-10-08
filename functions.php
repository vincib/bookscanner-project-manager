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

/* Lance une requete mysql et loggue éventuellement l'erreur) */
function mq($query) {
  global $er;
  $r=@mysql_query($query);
  if (mysql_errno()) {
    // TODO : probleme lors du RAISE : il lance un "log" donc fait un mysql insert !!!
    //       echo "ERREUR MYSQL : ".mysql_error()."<br>QUERY: ".$query."<br>\n";
    //    $er->raise(1,mysql_error());
    //$er->log(ERROR_LEVEL_FPUT,"mqerr",array("query"=>$query,"ERROR"=>mysql_error()));
  } else {
    // Uncomment this to log every request : 
    //$er->log(ERROR_LEVEL_FPUT,"mqok",array("query"=>$query));
  }
  return $r;
}

/* Lance une requete mysql et loggue éventuellement l'erreur), et retourne la liste des résultats dans un tableau de tableaux associatifs */
function mqlist($query) {
  global $er;
  $r=mq($query);
  if (mysql_errno()) {
    //$er->raise(1,mysql_error()."Q:".$query);
    return false;
  }
  $res=array();
  while ($c=mysql_fetch_array($r)) {
    $res[]=$c;
  }
  return $res;
}

/* Lance une requete mysql et loggue éventuellement l'erreur), et retourne la liste des résultats dans un tableau associatif (champ unique) */
function mqlistone($query) {
  global $er;
  $r=mq($query);
  if (mysql_errno()) {
    //$er->raise(1,mysql_error()."Q:".$query);
    return false;
  }
  $res=array();
  while ($c=mysql_fetch_array($r)) {
    $res[]=$c[0];
  }
  return $res;
}


/* Lance une requete mysql et loggue éventuellement l'erreur), et retourne la liste des résultats dans un tableau associatif ou les clés sont le premier champ et les valeurs le second. */
function mqassoc($query) {
  global $er;
  $r=mq($query);
  if (mysql_errno()) {
    //$er->raise(1,mysql_error()."Q:".$query);
    return false;
  }
  $res=array();
  while ($c=mysql_fetch_array($r)) {
    $res[$c[0]]=$c[1];
  }
  return $res;
}


/* Lance une requete mysql et loggue éventuellement l'erreur), et retourne le résultat unique dans un tableau associatif */
function mqone($query) {
  global $er;
  $r=mq($query);
  if (mysql_errno()) {
    //$er->raise(1,mysql_error()."Q:".$query);
    return false;
  }
  return mysql_fetch_array($r);
}

/* Lance une requete mysql et loggue éventuellement l'erreur), et retourne le champ unique du résultat unique. */
function mqonefield($query) {
  global $er;
  $r=mq($query);
  if (mysql_errno()) {
    //$er->raise(1,mysql_error()."Q:".$query);
    return false;
  }
  if (list($res)=mysql_fetch_array($r)) 
    return $res;
  else 
    return false;
}

function shorturl($url) {
  $url2=preg_replace("#\?.*$#","",$url);
  if ($url2!=$url) $url2.=" ...";
  if (strlen($url2)<=40) return $url2;
  $url3=preg_replace('#(https?://[^/]*)/.*/([^/]*)$#','$1 ... $2',$url2);
  return $url3;
}


