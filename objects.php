<?php

class HouseData {
  /* household info - name, salutation, mail name,
     preferred postal address, member array, donations array */
  public $household_id;
  public $hd;
  public $members = array();
  public $donations = array();
  public $addresses = array();
  public $emails = array();
  public $phones = array();

  function __construct($msi, $smarty, $hid, &$ErrMsg) {
    $tx=pSelect($msi,
       "select h.household_id, h.name, h.salutation, h.mailname,
          ifnull(h.address_id,0) address_id
          from households h
         where h.household_id=?",$hid,'HouseData info',$ErrMsg);
    $this->hd=$tx[0];
    $this->household_id=$this->hd['household_id'];

    /* get member info for the household */
    $this->members=pSelect($msi,
       "select c.contact_id,c.primary_name,
       c.first_name,c.middle_name,c.nickname,d.degree
       from household_members hm
       inner join contacts c on c.contact_id=hm.contact_id
       left join degrees d on d.degree_id=c.degree_id
       where hm.household_id=?",$this->household_id,
       'HouseData members',$ErrMsg);
    foreach($this->members as &$tx) {
      $tx['trek_list']=trek_list($msi,$tx['contact_id'],$ErrMsg);
    }
    unset($tx);

    /* get donations */
    $this->donations=pSelect($msi,'select d.donation_id,'.
        "d.primary_donor_id, d.ddate, d.amount,".
        "format(d.amount,2) famount,d.anonymous,".
        "f.fund,d.purpose,c.first_name, de.degree ".
        "from household_members hm inner join hdonations d ".
        "on d.primary_donor_id=hm.contact_id ".
        "inner join contacts c on c.contact_id=hm.contact_id ".
        "left join funds f on f.fund_id=d.fund_id ".
        "left join degrees de on de.degree_id=c.degree_id ".
        "where hm.household_id=? order by d.ddate desc",
        $this->household_id,'HouseData donations',$ErrMsg);
        
    /* get all addresses for all household members */
    $member_id_list = '';
    foreach($this->members as $tx) {
      $member_id_list .= (strlen($member_id_list) ? ',' : '').$tx['contact_id'];
    }
    if(strlen($member_id_list)) {
      $this->addresses=uSelect($msi,
         "select distinct 0 preferred, at.address_type,
         a.address_id,a.owner_id,cx.first_name,a.street_address_1,
         a.street_address_2,a.city, a.state, 
         a.postal_code, a.country from contacts c
         inner join address_associations aa 
           on aa.contact_id=c.contact_id
         inner join addresses a on a.address_id=aa.address_id
         inner join address_types at
           on at.address_type_id=a.address_type_id
         left join contacts cx on cx.contact_id=a.owner_id
         where c.contact_id in ($member_id_list)",
         'HouseData address',$ErrMsg);
      foreach($this->addresses as &$tx) {
        if($tx['address_id'] == $this->hd['address_id']) {
          $tx['preferred'] = 1;
          break;
        }
      }
      unset($tx);

      /* get all emails for all household members
         - member_id_list was created in the address section */
      $this->emails=pSelect($msi,
         "select distinct !isnull(pe.email_id) preferred,
         et.email_type, e.email_id,cx.first_name, e.email
         from contacts c inner join email_associations ea
         on ea.contact_id=c.contact_id
         inner join emails e on e.email_id=ea.email_id
         left join email_types et
         on  et.email_type_id=e.email_type_id
         left join contacts cx on cx.contact_id=e.owner_id
         left join preferred_emails pe
         on pe.email_id=e.email_id and pe.household_id=?
         where c.contact_id in ($member_id_list)",
         $this->household_id,'e-mail info',$ErrMsg);

      /* get all phones for all household members
         - member_id_list was created in the address section */
      $this->phones=uSelect($msi,"select distinct pt.phone_type,
         p.phone_id,cx.first_name,p.formatted,p.number
         from contacts c
         inner join phone_associations pa on
         pa.contact_id=c.contact_id
         inner join phones p on p.phone_id=pa.phone_id
         left join phone_types pt
         on pt.phone_type_id=p.phone_type_id
         left join contacts cx on cx.contact_id=p.owner_id
         where c.contact_id in ($member_id_list)",
         'phones info',$ErrMsg);
      
    }
  } // end construct function
  
  public function getPreferredAddress() {
    foreach($this->addresses as $ad) {
      if($ad['preferred']) {
        return $ad;
      }
    }
    return null;
  }

  public function updateHouse($msi,$smarty,&$ErrMsg) {
    if (!isset($_POST['house_name']) || strlen($_POST['house_name'])<1) {
      buildErrorMessage($ErrMsg,'Household Name is required');
      goto updateError;
    }
    // check if name is in use other than for this household_id
    if(isDupeHousehold($msi,$_POST['house_name'],$this->household_id,$ErrMsg)) {
      buildErrorMessage($ErrMsg,'Household Name is already in use');
      goto updateError;
    }
    /* change the current values 
       these will be re-displayed even if the db update fails */
    $this->hd['name']=$_POST['house_name'];
    $this->hd['salutation']=$_POST['salutation'];
    $this->hd['mailname']=$_POST['mail_name'];
    /* value of pref radio button group is
         the address_id of the selected address */
    $this->hd['address_id']=$_POST['prefaddress'];
    /* pretend that MySQL transactions work */
    $msi->autocommit(false);
    if(!$stmt=$msi->prepare(
      "update households set name=?,salutation=?,mailname=?,address_id=?,
         modified=now() where household_id=?")) {
        buildErrorMessage($ErrMsg,
          "update household: unable to prep sql update stmt: ",$msi->error);
      goto updateError;
    }
    if(!$stmt->bind_param('sssii',$_POST['house_name'],
       $_POST['salutation'],$_POST['mail_name'],
       $_POST['prefaddress'],$this->household_id)) {
      buildErrorMessage($ErrMsg,
        "update household: unable to execute sql update: ",
        $msi->error);
      $stmt->close();
      goto updateError;     
    }
    if(!$stmt->execute()) {
      buildErrorMessage($ErrMsg,
        "update household: unable to execute sql update: ",
        $msi->error);
      $stmt->close();
      goto updateError;
    }
    /* update preferred emails */
    if(!$msi->query("delete from preferred_emails ".
       "where household_id=".$this->household_id)) {
      buildErrorMessage($ErrMsg,
        "update pref e-mail: unable to delete: ",$msi->error);
      goto updateError;       
    }
    $email_error="";
    foreach($this->emails as $tx) {
      if(isset($_POST['prefemail'.$tx['email_id']])) {
        if(!$msi->query("insert into preferred_emails values(null,".
           $this->household_id.",".$tx['email_id'].")")) {
          $email_error.=$tx['email_id']." ";
        }
      }
    }
    if(strlen($email_error)) {
      buildErrorMessage($ErrMsg,
        "update pref e-mail: error inserting ids:".$email_error,
        $msi->error);
      goto updateError;
    }
    $msi->commit();
    $msi->autocommit(true);
    return;
updateError:
    $msi->rollback();
    $msi->autocommit(true);
  }
}  // end HouseData object
?>
