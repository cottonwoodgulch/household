<?php
/* contact.php */

require_once 'libe.php';
if(!$rbac->Users->hasRole('Contact Information Editor',$_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'IData.php';
$ErrMsg=array();

/*echo "$_post:<br>";
print_r($_POST);
echo "<br>";*/

if(isset($_POST['ContactID'])) {
  /* from FindContact input on Contact page,
       which also provides household_id if available */
  $cid=$_POST['ContactID'];
  $_SESSION['household_id']=$_POST['CurrentHouseID'];
}
else if(isset($_GET['cid'])) {
  /* from link on Details or Summary pages */
  $cid=$_GET['cid'];
}
else {
  $cid=0;
}

if(isset($_POST['buttonAction'])) {
  if($_POST['buttonAction'] == 'Add') {
    if(isset($_POST['EditContactID'])) {
      if(!$stmt=$msi->prepare('insert into contacts '.
        '(contact_type_id,primary_name,first_name,middle_name,'.
        'degree_id,nickname,birth_date,gender,deceased,username,'.
        'password,password_reset,redrocks,gender_id) '.
        'values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')) {
        buildErrorMessage($ErrMsg,
          'prep add contact: ',$msi->error);
        goto sqlerror;
      }
      $dob=strlen($_POST['EditDOB'])>0 ? $_POST['EditDOB'] : null;
      $deceased=isset($_POST['EditDeceased']);
      $username=!strlen($_POST['EditUsername']) ? null :
         $_POST['EditUsername'];
      if($_POST['EditPassword'] != '') {
        $pwhash=
          password_hash($_POST['EditPassword'],PASSWORD_DEFAULT);
      }
      else {
        $pwhash=null;
      }
      $pw_reset=0;
      $redrocks=isset($_POST['EditRedRocks']);
      if(!$stmt->bind_param('isssisssissiis',$_POST['EditContactType'],
          $_POST['EditLast'],$_POST['EditFirst'],$_POST['EditMiddle'],
          $_POST['EditDegree'],$_POST['EditNickname'],
          $dob,$_POST['EditGender'],$deceased,
          $username,$pwhash,$pw_reset,$redrocks,$_POST['EditGenderID'])) {
        buildErrorMessage($ErrMsg,
           'bind add contact query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec add contact query: ',$msi->error);
        goto sqlerror;
      }
      $cid=$msi->insert_id;
    }
    else if(isset($_POST['EditPhoneID'])) {
      /* can't change owner_id here */
      if(isset($_POST['EditFormatted'])) {
        $phone_number=$_POST['EditNumber'];
        $phone_formatted=true;
      }
      else {
        /* strip non-numeric characters */
        $phone_number=preg_replace('/[^0-9]/','',$_POST['EditNumber']);
        $phone_formatted=false;
      }
      if(!$stmt=$msi->prepare('insert into phones '.
        '(phone_type_id,owner_id,number,formatted) values (?,?,?,?)')) {
        buildErrorMessage($ErrMsg,
          'prep add phone query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iisi',$_POST['EditPhoneType'],$cid,
          $phone_number,$phone_formatted)) {
        buildErrorMessage($ErrMsg,
           'bind add phone query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec add phone query: ',$msi->error);
        goto sqlerror;
      }
      addassoc($msi,'phone',$cid,$msi->insert_id);
    }
    else if(isset($_POST['EditEmailID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('insert into emails '.
        '(email_type_id,owner_id,email) values (?,?,?)')) {
        buildErrorMessage($ErrMsg,
          'prep add email query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iis',$_POST['EditEmailType'],$cid,
          $_POST['EditEmail'])) {
        buildErrorMessage($ErrMsg,
           'bind add email query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec add email query: ',$msi->error);
        goto sqlerror;
      }
      addassoc($msi,'email',$cid,$msi->insert_id);
    }
    else if(isset($_POST['EditAddressID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('insert into addresses '.
        '(address_type_id,owner_id,street_address_1,street_address_2, '.
        'city,state,country,postal_code) values (?,?,?,?,?,?,?,?)')) {
        buildErrorMessage($ErrMsg,
          'prep add address query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iissssss',$_POST['EditAddressType'],$cid,
          $_POST['EditAddr1'],$_POST['EditAddr2'],$_POST['EditCity'],
          $_POST['EditState'],$_POST['EditCountry'],$_POST['EditZip'])) {
        buildErrorMessage($ErrMsg,
           'bind add address query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec edit address query: ',$msi->error);
        goto sqlerror;
      }
      addassoc($msi,'address',$cid,$msi->insert_id);
    }
    else if(isset($_POST['EditRelationshipID'])) {
      if(!$stmt=$msi->prepare('insert into relationships '.
        '(contact_id,relationship_type_id,relative_id) '.
        'values (?,?,?)')) {
        //echo 'cant prep rel: '.$msi-error.'<br>';
        buildErrorMessage($ErrMsg,
          'prep add relationship query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iii',$cid,
         $_POST['EditRelationshipType'],
          $_POST['RelativeID'])) {
        buildErrorMessage($ErrMsg,
           'bind add email query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec add email query: ',$msi->error);
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditNoteID'])) {
      if(!$stmt=$msi->prepare('insert into notes '.
        '(contact_id,modified,note) values (?,?,?)')) {
        buildErrorMessage($ErrMsg,
          'prep add note query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iss',$cid,
         $_POST['EditDate'],$_POST['EditNote'])) {
        buildErrorMessage($ErrMsg,
           'bind add note params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec add note query: ',$msi->error);
        goto sqlerror;
      }
    }
  }
  else if ($_POST['buttonAction'] == 'Edit') {
    /* editXxxIDs are set in Contact.js edit functions */
    if(isset($_POST['EditContactID'])) {
      /* this doesn't change password_reset - that happens
         at login */
      if(!$stmt=$msi->prepare('update contacts '.
        'set contact_type_id=?,primary_name=?,first_name=?,'.
        'middle_name=?,degree_id=?,nickname=?,birth_date=?,'.
        'gender=?,deceased=?,username=?,'.
        'redrocks=?,gender_id=? where contact_id=?')) {
        buildErrorMessage($ErrMsg,
          'prep edit contact query: ',$msi->error);
        goto sqlerror;
      }
      $dob=strlen($_POST['EditDOB'])>0 ? $_POST['EditDOB'] : null;
      $deceased=isset($_POST['EditDeceased']);
      $username=!strlen($_POST['EditUsername']) ? null :
         $_POST['EditUsername'];
      $redrocks=isset($_POST['EditRedRocks']);
      if(!$stmt->bind_param('isssisssisisi',$_POST['EditContactType'],
          $_POST['EditLast'],$_POST['EditFirst'],$_POST['EditMiddle'],
          $_POST['EditDegree'],$_POST['EditNickname'],
          $dob,$_POST['EditGender'],$deceased,
          $username,$redrocks,$_POST['EditGenderID'],$cid)) {
        buildErrorMessage($ErrMsg,
           'bind add contact query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec add contact query: ',$msi->error);
        goto sqlerror;
      }
      /* only update password if one has been entered */
      if($_POST['EditPassword'] != '') {
        $pwhash=
          password_hash($_POST['EditPassword'],PASSWORD_DEFAULT);
        if(!$msi->query("update contacts set password='$pwhash'".
          'where contact_id='.$_POST['ContactID'])) {
          buildErrorMessage($ErrMsg,
             'edit contact: set password: ',$msi->error);
          goto sqlerror;
        }
      }
    }
    if(isset($_POST['EditPhoneID'])) {
      /* can't change owner_id here */
      if(isset($_POST['EditFormatted'])) {
        $phone_number=$_POST['EditNumber'];
        $phone_formatted=1;
      }
      else {
        /* strip non-numeric characters */
        $phone_number=preg_replace('/[^0-9]/','',$_POST['EditNumber']);
        $phone_formatted=0;
      }
      if(!$stmt=$msi->prepare('update phones '.
        'set phone_type_id=?,number=?,formatted=? where phone_id=?')) {
        buildErrorMessage($ErrMsg,
          'prep edit phone query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('issi',$_POST['EditPhoneType'],
          $phone_number,$phone_formatted,$_POST['EditPhoneID'])) {
        buildErrorMessage($ErrMsg,
           'bind edit phone query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec edit phone query: ',$msi->error);
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditEmailID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('update emails '.
        'set email_type_id=?,email=? where email_id=?')) {
        buildErrorMessage($ErrMsg,
          'prep edit email query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('isi',$_POST['EditEmailType'],
          $_POST['EditEmail'],$_POST['EditEmailID'])) {
        buildErrorMessage($ErrMsg,
           'bind edit email query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec edit email query: ',$msi->error);
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditAddressID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('update addresses '.
        'set address_type_id=?,street_address_1=?,'.
        'street_address_2=?,city=?,state=?,country=?,postal_code=? '.
        'where address_id=?')) {
        buildErrorMessage($ErrMsg,
          'prep edit address query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('issssssi',$_POST['EditAddressType'],
          $_POST['EditAddr1'],$_POST['EditAddr2'],$_POST['EditCity'],
          $_POST['EditState'],$_POST['EditCountry'],$_POST['EditZip'],
          $_POST['EditAddressID'])) {
        buildErrorMessage($ErrMsg,
           'bind edit address query params: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec edit address query: ',$msi->error);
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditRelationshipID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('update relationships '.
        'set relationship_type_id=?,relative_id=? '.
        'where relationship_id=?')) {
        buildErrorMessage($ErrMsg,
          'prep edit relationship query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iii',$_POST['EditRelationshipType'],
          $_POST['RelativeID'],$_POST['EditRelationshipID'])) {
        buildErrorMessage($ErrMsg,
           'bind edit relationship query params: '.
           $msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec edit relationship query: ',$msi->error);
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditNoteID'])) {
      if(!$stmt=$msi->prepare('update notes '.
        'set modified=?,note=? where note_id=?')) {
        buildErrorMessage($ErrMsg,
          'prep edit notes query: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('ssi',$_POST['EditDate'],
          $_POST['EditNote'],$_POST['EditNoteID'])) {
        buildErrorMessage($ErrMsg,
           'bind edit note query params: '.
           $msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
           'exec edit note query: ',$msi->error);
        goto sqlerror;
      }
    }
  }
  else if ($_POST['buttonAction'] == 'Delete') {
    /* editXxxIDs are set in Contact.js edit functions */
    if(isset($_POST['EditContactID'])) {
      deletecoordinate($msi,'contact',$_POST['EditContactID']);
      $cid=0;
    }
    if(isset($_POST['EditPhoneID'])) {
      deletecoordinate($msi,'phone',$_POST['EditPhoneID'],$cid);
    }
    else if(isset($_POST['EditEmailID'])) {
      deletecoordinate($msi,'email',$_POST['EditEmailID'],$cid);
    }
    else if(isset($_POST['EditAddressID'])) {
      deletecoordinate($msi,'address',$_POST['EditAddressID'],$cid);
    }
    else if(isset($_POST['EditRelationshipID'])) {
      deletecoordinate($msi,'relationship',
         $_POST['EditRelationshipID'],$cid);
    }
    else if(isset($_POST['EditNoteID'])) {
      /* notes don't have associations */
      if(!$msi->query('delete from notes where note_id='.
           $_POST['EditNoteID'])) {
        buildErrorMessage($ErrMsg,"delete note failed: ",
           $msi->error);
      }
    }
  }
  sqlerror:
    if($stmt)$stmt->close();
}  // isset buttonAction

/* explanation for formatted checkbox for phone numbers */
$smarty->assign('phone_formatted','If this is checked, the program assumes the number should be presented as entered. If not, the program presents the number as a Canadian/US number: (123) 456-7890');


if($cid) {
  $cdata=new IData($msi,$smarty,$cid,$ErrMsg);
  $smarty->assign('cdata',$cdata);
}

/* phone, e-mail, aaddress types for add & edit dialogs */
$smarty->assign('phone_type_list',uSelect($msi,
   'select phone_type_id,phone_type from phone_types',
   'phone types',$ErrMsg));
$smarty->assign('email_type_list',uSelect($msi,
   'select email_type_id,email_type from email_types',
   'email types',$ErrMsg));
$smarty->assign('address_type_list',uSelect($msi,
   'select address_type_id,address_type from address_types',
   'address types',$ErrMsg));
$smarty->assign('contact_type_list',uSelect($msi,
   'select contact_type_id,contact_type from contact_types',
   'contact types',$ErrMsg));
$smarty->assign('degree_list',uSelect($msi,
   'select degree_id,degree from degrees','degrees',$ErrMsg));
$rtype=$cdata->Contact['gender'] == '' ?
   'relationship_type' : $cdata->Contact['gender'];
$smarty->assign('relationship_type_list',uSelect($msi,
   'select inverse_relationship_id relationship_type_id,'.
   "ifnull($rtype,relationship_type) relationship_type ".
   'from relationship_types','relationship types',$ErrMsg));
$smarty->assign('role_list',uSelect($msi,
   'select role_id,role from roles','roles',$ErrMsg));

displayFooter($smarty,$ErrMsg);
$smarty->display('ContactMain.tpl');

function get_types($msi,$table,$gender='') {
  $type_list=array();
  if($table=='degree') {
    $query="select degree_id,degree from degrees";
  }
  else if($table=='role') {
    $query='select role_id,role from roles';
  }
  else if ($table=='relationship') {
    if($gender=='' || is_null($gender)) {
      $query='select inverse_relationship_id relationship_type_id,'.
         'relationship_type from relationship_types';
    }
    else {
      $query="select inverse_relationship_id relationship_type_id,".
      "ifnull($gender,relationship_type) relationship_type ".
      "from relationship_types";
    }
  }
  else {
    $query="select $table"."_type_id,$table"."_type ".
       "from $table"."_types";
  }
  if($result=$msi->query($query)) {
    while($tx = $result->fetch_assoc()) {
      $type_list[]=$tx;
    }
  }
  return $type_list;
}

function addassoc($msi,$coordinate,$cid,$coordinate_id) {
  if(!$msi->query("insert into $coordinate"."_associations ".
   "(contact_id,$coordinate"."_id) values ($cid,$coordinate_id)")) {
    buildErrorMessage($ErrMsg,
       "add $coordinate association failed: ",$msi->error);
  }
}

function deletecoordinate($msi,$coordinate,$key_value,$contact_id) {
  if($coordinate=='address') {
    $table='addresses';
  }
  else {
    $table=$coordinate.'s';
  }
  $a_table=$coordinate.'_associations';
  $key_name=$coordinate.'_id';
  /* first delete association, then if there are no other
     associations for this coordinate, delete the coordinate */
  //echo "key value: $key_value<br>";
  if(!$result=$msi->query(
     "select $key_name from $a_table where $key_name=$key_value")) {
    buildErrorMessage($ErrMsg,
       "association check query failed: ",$msi->error);
    return;
  }
  $association_count=$result->num_rows;
  $result->free();
  //echo "associations: $association_count<br>";
  if(!$msi->query("delete from $a_table where ".
     "$key_name=$key_value and contact_id=$contact_id")) {
    buildErrorMessage($ErrMsg,
       "delete $a_table query failed: ",$msi->error);
    return;
  }
  if($association_count == 1) {
    /* this is the only association to that coordinate - ok to del */
    if(!$msi->query("delete from $table where $key_name=$key_value")) {
      buildErrorMessage($ErrMsg,
         "delete $table query failed: ",$msi->error);
    }
  }
  return;
}
?>
