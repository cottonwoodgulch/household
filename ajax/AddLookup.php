<?php
/* lookup member by first/middle/last name for AddDialog.html */
/* identical to MemberLookup.php except returns contact_id instead of household_id */
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

// $_GET['value']="Pax";

$ErrMsg='';
if(isset($_GET['value'])){
    $value="'%".strtolower($_GET['value'])."%'";
    $query="select c.contact_id, concat(ifnull(h.name, 'no house'),': ',concat_ws(' ',c.first_name, ".
           "c.primary_name,d.degree)), ifnull(hm.household_id, 0), h.name, ".
           "concat(c.first_name,' ', c.primary_name) ".
           "from contacts c left join household_members hm ".
           "on hm.contact_id=c.contact_id left join households h ".
           "on h.household_id=hm.household_id left join degrees d ".
           "on d.degree_id=c.degree_id where lower(c.primary_name) like $value ".
           "or lower(c.first_name) like $value or lower(c.middle_name) like $value ".
           "or lower(c.nickname) like $value";
    // echo $query;
    if(!$result=$msi->query($query)) {
      $ErrMsg=buildErrorMessage($ErrMsg,'unable to execute look up member query'.
         $msi->error);
      goto sqlerror;
    }
    while($rx=$result->fetch_row()) {
      $retval[]=new SF($rx[1],$rx[0], $rx[2], $rx[3], $rx[4]);
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
