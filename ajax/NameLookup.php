<?php

/* lookup household by household name for lookup.js */
require_once "../libe.php";

class SF {
  public $label;
  public $value;
  function __construct($l,$v) {
    $this->label=$l;
    $this->value=$v;
  }
}

$ErrMsg='';
if(isset($_GET['value'])){
    $value='%'.strtolower($_GET['value']).'%';
    //$query="select name from household where lower(name) like '".$value."'";
    
    $query="select name from household where lower(name) like '$value'";
    if(!$result=$msi->query($query)) {
      $ErrMsg=buildErrorMessage($ErrMsg,'unable to execute look up name query'.
         $msi->error);
      goto sqlerror;
    }
    while($rx=$result->fetch_row()) {
      $retval[]=new SF($rx[0],$rx[0]);
    }
    echo json_encode($retval);
}
else {
  $ErrMsg=buildErrorMessage($ErrMsg,'no value provided');
}
sqlerror:
if(strlen($ErrMsg)) {
  echo $ErrMsg;
}
?>
