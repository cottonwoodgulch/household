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

  function __construct($msi,$smarty,$cid,&$ErrMsg) {
    $tx=pSelect($msi,
       "select c.contact_id,ct.contact_type,c.contact_type_id,".
       "c.primary_name,c.first_name,c.middle_name,".
       "d.degree,c.degree_id,c.nickname,".
       "if(c.birth_date is null || c.birth_date='0000-00-00',".
           "'',c.birth_date) dob, ifnull(c.gender,'') gender,".
       "if(c.deceased,'yes','no') deceased,c.username,".
       "c.redrocks,h.mailname,".
       "ifnull(h.household_id,0) household_id ".
       "from contacts c left join contact_types ct ".
       "on ct.contact_type_id=c.contact_type_id ".
       "left join degrees d on d.degree_id=c.degree_id ".
       "left join household_members hm ".
       "on hm.contact_id=c.contact_id ".
       "left join households h on h.household_id=hm.household_id ".
       "where c.contact_id=?",$cid,'contact info',$ErrMsg);
    if(count($tx)) {
      $this->Contact=$tx[0];
      $this->contact_id=$this->Contact['contact_id'];
    }
    else {
      $this->contact_id=0;
    }

    /* PHONES */
    $this->phones=pSelect($msi,"select p.phone_type_id,pt.phone_type,p.owner_id,o.first_name, ".
            "o.primary_name, ifnull(od.degree,'') degree, ".
            "p.phone_id,p.number,p.formatted ".
            "from phone_associations pa ".
            "inner join phones p on p.phone_id=pa.phone_id ".
            "left join phone_types pt on pt.phone_type_id=p.phone_type_id ".
            "left join contacts o on o.contact_id=p.owner_id ".
            "left join degrees od on od.degree_id=o.degree_id ".
            "where pa.contact_id=?",$this->contact_id,
            'phone info query error',$ErrMsg);

    /* E-MAILS */
    $this->emails=pSelect($msi,"select e.email_type_id,et.email_type,e.owner_id,o.first_name, ".
           "o.primary_name,ifnull(od.degree,'') degree,e.email_id,e.email ".
           "from email_associations ea ".
           "inner join emails e on e.email_id=ea.email_id ".
           "left join email_types et on et.email_type_id=e.email_type_id ".
           "left join contacts o on o.contact_id=e.owner_id ".
           "left join degrees od on od.degree_id=o.degree_id ".
           "where ea.contact_id=?",$this->contact_id,
           'e-mail info query error',$ErrMsg);

    /* ADDRESSES */
    $this->addresses=pSelect($msi,
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
           "where aa.contact_id=?",$this->contact_id,
           'address info query error',$ErrMsg);
    
    /* NOTES */
    //"replace(replace(n.note,'\r\n',' '),'\"','') note,".
    $this->notes=pSelect($msi,"select n.note_id,n.note,".
        "date_format(n.modified,'%Y-%m-%d') as ddate from notes n ".
        "where length(note)>0 and n.contact_id=? ".
        "order by n.modified desc",$this->contact_id,
        'notes info query error',$ErrMsg);

    /* RELATIONSHIPS */
    $this->relationships=pSelect($msi,
       "select r.relationship_type_id,".
       "if(c.gender is null,rt.relationship_type,".
       "if(c.gender='Male',rt.male,rt.female)) relationship,".
       "if(rc.nickname is null || rc.nickname='',".
       "rc.first_name,rc.nickname) first,rc.primary_name,".
       "r.relationship_id,r.relative_id from relationships r ".
       "inner join contacts c on c.contact_id=r.contact_id ".
       "inner join contacts rc on rc.contact_id=r.relative_id ".
       "inner join relationship_types rt ".
       "on rt.inverse_relationship_id=r.relationship_type_id ".
       "where r.contact_id=?",$this->contact_id,
       'relationship info query error',$ErrMsg);

    /* GROUPS */
    $this->groups=pSelect($msi,
       "select ifnull(ro.role,'') role,".
       "r.year,rm.roster_id,g.group_id,g.group ".
       "from roster_memberships rm ".
       "inner join rosters r on r.roster_id=rm.roster_id ".
       "inner join groups g on g.group_id=r.group_id ".
       "left join roles ro on ro.role_id=rm.role_id ".
       'where rm.contact_id=? order by 2 desc',$this->contact_id,
       'group info query error',$ErrMsg);
  }  // constructor
} // class IData
?>
