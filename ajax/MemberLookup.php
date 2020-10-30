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
  if(!strlen($_GET['value'])) {
    // don't search on empty string - it will return everyone in the db
    $ErrMsg=buildErrorMessage($ErrMsg,'Search string is empty');
    goto sqlerror;
  }
  $query="select concat(h.name,': ',concat_ws(' ',c.first_name, ".
           "c.primary_name,d.degree)), h.household_id, h.name ".
           "from contacts c inner join household_members hm ".
           "on hm.contact_id=c.contact_id left join households h ".
           "on h.household_id=hm.household_id left join degrees d ".
           "on d.degree_id=c.degree_id where ";
  $st=explode(' ',strtolower($msi->escape_string($_GET['value'])));
  $is_first=true;
  foreach($st as $wx) {
    if(!$is_first) $query.=' && ';
    $wx="'".$wx."%'";
    $query.="(lower(c.first_name) like $wx || lower(c.middle_name) like $wx || ".
        "lower(c.nickname) like $wx || lower(c.primary_name) like $wx)";
    $is_first=false;
  }
  //echo "query: $query\n\n";
  if(!$result=$msi->query($query)) {
    $ErrMsg=buildErrorMessage($ErrMsg,
      'unable to execute look up member query'.$msi->error);
    goto sqlerror;
  }
  while($rx=$result->fetch_row()) {
    $retval[]=new SF($rx[0],$rx[1],$rx[1],$rx[2],"");
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
