<?php
require_once "../libe.php";

if($_GET['ButtonAction'] == 'AddressLookup') {
  /* look up list of names, get salutation & preferred address
     this section creates a csv file, but doesn't return anything to
     javascript function
  */
  class SF {
    public $first;
    public $middle;
    public $last;
    public $nickname;
    public $salutation;
    public $addr1;
    public $addr2;
    public $city;
    public $state;
    public $zip;
    public $country;
    function __construct($first,$middle,$last,$nickname,$salutation,
        $addr1,$addr2,$city,$state,$zip,$country) {
      $this->first=$first;
      $this->middle=$middle;
      $this->last=$last;
      $this->nickname=$nickname;
      $this->salutation=$salutation;
      $this->addr1=$addr1;
      $this->addr2=$addr2;
      $this->city=$city=$city;
      $this->state=$state=$state;
      $this->zip=$zip=$zip;
      $this->country = $country=$country;
    }
  }
  
}

