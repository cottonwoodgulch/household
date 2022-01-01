<?php
/* get roster members */
require_once "../libe.php";

class SF {
  public $first;
  public $middle;
  public $last;
  public $contact_id;
  public $role;
  function __construct($first,$middle,$last,$contact_id,$role) {
    $this->first=$first;
    $this->middle=$middle;
    $this->last=$last;
    $this->contact_id=$contact_id;
    $this->role=$role;
  }
}

$year=$_GET['year'];
$group_id=$_GET['group_id'];
if($result=$msi->query(
    "select c.first_name,ifnull(c.middle_name,'') middle_name,".
    "c.primary_name,c.contact_id,ifnull(ro.role,'') role from rosters r ".
    "inner join roster_memberships rm on rm.roster_id=r.roster_id ".
    "inner join contacts c on c.contact_id=rm.contact_id ".
    "left join roles ro on ro.role_id=rm.role_id ".
    "where r.group_id=$group_id and r.year=$year ".
    "order by ro.rank,c.primary_name,c.first_name")) {
  $retval=array();
  while($rx=$result->fetch_row()) {
    $retval[]=new SF($rx[0],$rx[1],$rx[2],$rx[3],$rx[4]);
  }
  $result->free();
}
echo json_encode($retval);
?>
