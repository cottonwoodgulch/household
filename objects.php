<?php

class HouseData {
  /* household info - name, salutation, mail name,
     preferred postal address, member array, donations array */
  public $household_id;
  public $hd;
  public $members = array();
  public $donations = array();
  public $addresses = array();
  private $errormsg = '';

  function __construct($msi, $smarty, $hid) {
    if($stmt=$msi->prepare(
         "select h.household_id, h.name, h.salutation, h.mailname,
                 h.address_id
            from household h
           where h.household_id=?")) {
      $stmt->bind_param('i',$hid);
      $stmt->execute();
      $result=$stmt->get_result();
      $this->hd = $result->fetch_assoc();
      $stmt->close();
      $result->free();
      $this->household_id=$this->hd['household_id'];
    }
    else {
      buildErrorMessage(
        "HouseData info: unable to create mysql statement object: ".
        $msi->error);
      return;
    }
    /* get member info for the household */
    if($stmt=$msi->prepare("select c.contact_id, c.primary_name, c.first_name,
                                   c.middle_name, c.nickname, d.degree
                              from household_members hm
                             inner join contacts c
                                on c.contact_id=hm.contact_id
                              left join degrees d
                                on d.degree_id=c.degree_id
                             where hm.household_id=?")) {
      $stmt->bind_param('i',$this->household_id);
      $stmt->execute();
      $result=$stmt->get_result();
      while($tx = $result->fetch_assoc()) {
        $this->members[] = $tx;
      }
      $stmt->close();
      $result->free();
    }
    else {
     buildErrorMessage(
      "HouseData members: unable to create mysql statement object: ".
      $msi->error);
    }

    /* get donations */
    //if($stmt=$msi->prepare("select d.date,d.amount,d.anonymous,d.private,
    if($stmt=$msi->prepare("select date_format(d.date,'%c/%d/%Y') date,
                                   format(d.amount,2) amount,d.anonymous,
                                   f.fund,d.purpose,c.first_name
                              from household_members hm
                             inner join donation_associations da
                                on da.contact_id=hm.contact_id
                             inner join donations d
                                on d.donation_id=da.donation_id
                             inner join contacts c
                                on c.contact_id=hm.contact_id
                             inner join funds f
                                on f.fund_id=d.fund_id
                             where hm.household_id=?
                             order by d.date desc")) {
      $stmt->bind_param('i',$this->household_id);
      $stmt->execute();
      $result=$stmt->get_result();
      while($tx = $result->fetch_assoc()) {
        $this->donations[] = $tx;
      }
      $stmt->close();
      $result->free();
    }
    else {
      buildErrorMessage(
       "HouseData donations: unable to create mysql statement object: ".
        $msi->error);
    }
    
    /* get all addresses for all household members */
    $member_id_list = '';
    foreach($this->members as $tx) {
      $member_id_list .= (strlen($member_id_list) ? ',' : '').$tx['contact_id'];
    }
    if($stmt=$msi->prepare(
         "select distinct 0 preferred, at.address_type, a.address_id,
                 cx.first_name,
                 a.street_address_1, a.street_address_2,
                 a.city, a.state, a.postal_code, a.country
            from contacts c
           inner join address_associations aa
              on aa.contact_id=c.contact_id
           inner join addresses a
              on a.address_id=aa.address_id
           inner join address_types at
              on at.address_type_id=a.address_type_id
            left join contacts cx
              on cx.contact_id=a.owner_id
           where c.contact_id in (?)")) {
      $stmt->bind_param('i',$member_id_list);
      $stmt->execute();
      $result=$stmt->get_result();
      while($tx = $result->fetch_assoc()) {
        if($tx['address_id'] == $this->hd['address_id']) {
          $tx['preferred'] = 1;
        }
        $this->addresses[] = $tx;
      }
      $stmt->close();
      $result->free();
    }
    else {
      buildErrorMessage(
        "Address info: unable to create mysql statement object: ".
        $msi->error);
      return;
    }

    if(strlen($this->errormsg))$smarty->assign('footer',$this->$errormsg);
  }
  // end construct function
  
  public function getPreferredAddress() {
    foreach($this->addresses as $ad) {
      if($ad['preferred']) {
        return $ad;
      }
    }
    return null;
  }
  
  function buildErrorMessage($err) {
    $this->errormsg .= strlen($this->errormsg) ? '<br />' : '' .$err;
  }

/*
  public function updateHouse($msi,$smarty) {
    if (!isset($_POST['house_name']) || strlen($_POST['house_name'])<1) {
      buildErrorMessage('Household Name is required');
    }
    else if(!isUniqueHouseholdName($_POST['house_name'],$this->household_id)) {
      buildErrorMessage('Household Name is already in use');
    }
    if(strlen($this->errormsg))
    if($stmt=$msi->prepare(
      "select household_id
         from household
        where 
  
  }
*/
}
?>
