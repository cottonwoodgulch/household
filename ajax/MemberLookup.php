<?php
/* lookup household by member name for LookupDialog.html */
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
    $value='%'.strtolower($_GET['value']).'%';
    $query="select h.household_id, concat(h.name,': ',concat_ws(' ',c.first_name, ".
           "c.primary_name,d.degree)), h.name ".
           "from contacts c inner join household_members hm ".
           "on hm.contact_id=c.contact_id left join households h ".
           "on h.household_id=hm.household_id left join degrees d ".
           "on d.degree_id=c.degree_id where c.primary_name like '".$value."' ".
           "or c.first_name like '".$value."' or c.middle_name like '".$value."' ".
           "or c.nickname like '".$value."'";
    if(!$result=$msi->query($query)) {
      $ErrMsg=buildErrorMessage($ErrMsg,'unable to execute look up member query'.
         $msi->error);
      goto sqlerror;
    }
    while($rx=$result->fetch_row()) {
      $retval[]=new SF($rx[1],$rx[0],$rx[0],$rx[2],"");
    }
    $result->free();
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
