<?php

/* lookup household by household name for LookupDialog.html */
require_once "../libe.php";

class SF {
  public $label;
  public $value;
  public $hid;
  public $housename;
  public $name;
  function __construct($l,$v, $hid, $housename, $name) {
    $this->label=$l;
    $this->value=$v;
    $this->hid=$hid;
    $this->housename=$housename;
    $this->name=$name;
  }
}

$ErrMsg='';
if(isset($_GET['value'])){
    $value="'%".strtolower($msi->escape_string($_GET['value']))."%'";    
    $query="select name, household_id from households where lower(name) like $value";
    if(!$result=$msi->query($query)) {
      $ErrMsg=buildErrorMessage($ErrMsg,'unable to execute look up name query'.
         $msi->error);
      goto sqlerror;
    }
    while($rx=$result->fetch_row()) {
      $retval[]=new SF($rx[0], $rx[1], $rx[1], $rx[0], "");
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
