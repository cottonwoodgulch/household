<?php

class HouseData {
  /* household info - name, salutation, mail name,
     preferred postal address, member array, donations array */
  public $household_id;
  public $hd;
  public $members = array();
  public $donations = array();
  public $addresses = array();
  private $ErrMsg = '';

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
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
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
     $this->ErrMsg=buildErrorMessage($this->ErrMsg,
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
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
       "HouseData donations: unable to create mysql statement object: ".
        $msi->error);
    }
    
    /* get all addresses for all household members */
    /* can delete the preferred code? */
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
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "Address info: unable to create mysql statement object: ".
        $msi->error);
      return;
    }

    if(strlen($this->ErrMsg))$smarty->assign('footer',$this->ErrMsg);
  }
  // end construct function
  
  /* this is not used that I know of - $this->hd['address_id'] has it */
  public function getPreferredAddress() {
    foreach($this->addresses as $ad) {
      if($ad['preferred']) {
        return $ad;
      }
    }
    return null;
  }

  public function updateHouse($msi,$smarty) {
    if (!isset($_POST['house_name']) || strlen($_POST['house_name'])<1) {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,'Household Name is required');
    }
    // check if name is in use other than for this household_id
    if($stmt=$msi->prepare("select 0 from household h
         where h.name=? and h.household_id!=?")) {
      $stmt->bind_param('si',$_POST['house_name'],$this->household_id);
      if($stmt->execute()) {
        $result=$stmt->get_result();
        if($result->num_rows) {
          $this->ErrMsg=buildErrorMessage($this->ErrMsg,
           'Household Name is already in use');
        }
      }
      else {
        $this->ErrMsg=buildErrorMessage($this->ErrMsg,
         'Could not execute dupe house query: '.$msi->error);
      }
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
       'Could not prep dupe house query: '.$msi->error);
    }
    $stmt->close;
    $result->free;

    if(!strlen($this->ErrMsg)) {
      /* change the current values */
      $this->hd['name']=$_POST['house_name'];
      $this->hd['salutation']=$_POST['salutation'];
      $this->hd['mailname']=$_POST['mail_name'];
      /* value of pref radio button group is
           the address_id of the selected address */
      $this->hd['address_id']=$_POST['pref'];
      if($stmt=$msi->prepare(
        "update household set name=?,salutation=?,mailname=?,address_id=?
           where household_id=?")) {
        $stmt->bind_param('sssii',$_POST['house_name'],$_POST['salutation'],
           $_POST['mail_name'],$_POST['pref'],$this->household_id);
        if(!$stmt->execute()) {
          $this->ErrMsg=buildErrorMessage($this->ErrMsg,
            "updateHouse: unable to execute sql update: ".$msi->error);
        }
        $stmt->close();
      }
      else {
        $this->ErrMsg=buildErrorMessage($this->ErrMsg,
          "updateHouse: unable to prep sql update stmt: ".$msi->error);
      }
    }
    displayFooter($smarty,$this->ErrMsg);
  }
}
?>
