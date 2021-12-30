<?php
/* look up city and state by zip code */
/* lookup member by first/middle/last name for AddDialog.html */
/* identical to MemberLookup.php except returns contact_id instead of household_id */
require_once "../libe.php";

class SF {
  public $zip;  // zip code sent in
  public $city;
  public $state;
  public $country;
  function __construct($zip,$city,$state) {
    $this->zip=$zip;
    $this->city=$city;
    $this->state=$state;
    $this->country = $zip = 0 ? '' : 'United States';
  }
}

if(isset($_GET['zip'])){
  // assumes calling program has verified it's a 5 numeric digit string
  $zip=$_GET['zip'];
  if($result=$msi->query(
      "select city,state from zip_codes where zip=$zip")) {
    $rx=$result->fetch_row();
    $retval=new SF($zip,$rx[0],$rx[1]);
  }
  else {
    $retval=new SF(0,'','');
  }
  $result->free();
}
else {
  $retval=new SF(0,'','');
}
echo json_encode($retval);
?>
