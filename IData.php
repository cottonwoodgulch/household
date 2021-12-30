<?php
/* IData.php - object to contain data for an individual */

class IData {
  public $contact_id;
  public $Contact;
  public $phones = array();
  public $emails = array();
  public $URLS = array();
  public $addresses = array();
  public $relationships = array();
  public $groups = array();
  public $notes = array();

  function __construct($msi,$smarty,$cid) {
    $this->contact_id=0;
    if($result=$msi->query(
       "select c.contact_id,ct.contact_type,c.primary_name,".
           "c.first_name,c.middle_name,d.degree,c.nickname,".
           "if(c.birth_date is null || c.birth_date='0000-00-00',".
               "'',c.birth_date) dob, ifnull(c.gender,'') gender,".
           "if(c.deceased,'yes','no') deceased, ".
           "h.mailname,ifnull(h.household_id,0) household_id ".
           "from contacts c ".
           "left join contact_types ct on ct.contact_type_id=c.contact_type_id ".
           "left join degrees d on d.degree_id=c.degree_id ".
           "left join household_members hm on hm.contact_id=c.contact_id ".
           "left join households h on h.household_id=hm.household_id ".
           "where c.contact_id=$cid")) {
      $this->Contact=$result->fetch_assoc();
      $this->contact_id=$this->Contact['contact_id'];
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "Contact info: query exec error: ",$msi->error);
    }
    $result->free();

    /* PHONES */
    if($result=$msi->query(
       "select p.phone_type_id,pt.phone_type,p.owner_id,o.first_name, ".
            "o.primary_name, ifnull(od.degree,'') degree, ".
            "p.phone_id,p.number,p.formatted ".
            "from phone_associations pa ".
            "inner join phones p on p.phone_id=pa.phone_id ".
            "left join phone_types pt on pt.phone_type_id=p.phone_type_id ".
            "left join contacts o on o.contact_id=p.owner_id ".
            "left join degrees od on od.degree_id=o.degree_id ".
            "where pa.contact_id=".$this->contact_id)) {
      while($tx = $result->fetch_assoc()) {
        $this->phones[] = $tx;
      }
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "Phone info: query exec error: ",$msi->error);
    }
    $result->free();

    /* E-MAILS */
    if($result=$msi->query(
       "select e.email_type_id,et.email_type,e.owner_id,o.first_name, ".
           "o.primary_name,ifnull(od.degree,'') degree,e.email_id,e.email ".
           "from email_associations ea ".
           "inner join emails e on e.email_id=ea.email_id ".
           "left join email_types et on et.email_type_id=e.email_type_id ".
           "left join contacts o on o.contact_id=e.owner_id ".
           "left join degrees od on od.degree_id=o.degree_id ".
           "where ea.contact_id=".$this->contact_id)) {
      while($tx = $result->fetch_assoc()) {
        $this->emails[] = $tx;
      }
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "E-mail info: query exec error: ",$msi->error);
    }
    $result->free();

    /* ADDRESSES */
    if($result=$msi->query(
       "select a.address_type_id,at.address_type,a.owner_id, ".
               "o.first_name,o.primary_name,ifnull(od.degree,'') degree, ".
               "a.address_id,a.street_address_1 addr1,".
               "ifnull(a.street_address_2,'') addr2,".
               "a.city,a.state,a.postal_code zip,a.country ".
               "from address_associations aa ".
               "inner join addresses a on a.address_id=aa.address_id ".
               "left join address_types at on at.address_type_id=a.address_type_id ".
               "left join contacts o on o.contact_id=a.owner_id ".
               "left join degrees od on od.degree_id=o.degree_id ".
               "where aa.contact_id=".$this->contact_id)) {
      while($tx = $result->fetch_assoc()) {
        $this->addresses[] = $tx;
      }
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "Address info: query exec error: ",$msi->error);
    }
    $result->free();
    
    /* NOTES */
    if($result=$msi->query("select ".
        "replace(replace(n.note,'\r\n',' '),'\"','') note,".
        "date_format(n.modified,'%m/%d/%Y') as ddate from notes n ".
        "left join contacts c on c.contact_id=n.contact_id ".
        "where length(note)>0 and c.contact_id=".$this->contact_id.
        " order by n.modified desc")) {
      while($tx=$result->fetch_assoc()) {
        $this->notes[]=$tx;
      }
    }
    else {
      echo 'notes query error: '.$msi->error.'<br>';
      exit;
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "Notes: query exec error: ",$msi->error);
    }
    $result->free();

    /* GROUPS */
    if($result=$msi->query(
       "select ifnull(ro.role,'') role,r.year,".
       "g.group_id,g.group ".
       "from roster_memberships rm ".
       "inner join rosters r on r.roster_id=rm.roster_id ".
       "inner join groups g on g.group_id=r.group_id ".
       "left join roles ro on ro.role_id=rm.role_id ".
       "where rm.contact_id=".$this->contact_id.' order by 2 desc')) {
      while($tx = $result->fetch_assoc()) {
        $this->groups[] = $tx;
      }
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "Group info: query exec error: ",$msi->error);
    }
    $result->free();

  }  // constructor

} // class IData
?>
