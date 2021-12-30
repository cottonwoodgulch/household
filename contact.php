<?php
/* contact.php */

require_once 'libe.php';
if(!$rbac->Users->hasRole('Contact Information Editor',$_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'IData.php';

/*
echo "$_post:<br>";
print_r($_POST);
echo "<br>";
*/

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
    if(isset($_POST['EditPhoneID'])) {
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
        echo 'phone insert prep: '.$msi->error.'<br>';
        $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to prep add phone query: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iisi',$_POST['EditPhoneType'],$cid,
          $phone_number,$phone_formatted)) {
        echo 'phone insert bind: '.$msi->error.'<br>';
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind add phone query params: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        echo 'phone insert exec: '.$msi->error.'<br>';
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec add phone query: '.$msi->error);
        goto sqlerror;
      }
      addassoc($msi,'phone',$cid,$msi->insert_id);
    }
    else if(isset($_POST['EditEmailID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('insert into emails '.
        '(email_type_id,owner_id,email) values (?,?,?)')) {
        $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to prep add email query: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iis',$_POST['EditEmailType'],$cid,
          $_POST['EditEmail'])) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind add email query params: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec add email query: '.$msi->error);
        goto sqlerror;
      }
      addassoc($msi,'email',$cid,$msi->insert_id);
    }
    else if(isset($_POST['EditAddressID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('insert into addresses '.
        '(address_type_id,owner_id,street_address_1,street_address_2, '.
        'city,state,country,postal_code) values (?,?,?,?,?,?,?,?)')) {
        $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to prep add address query: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iissssss',$_POST['EditAddressType'],$cid,
          $_POST['EditAddr1'],$_POST['EditAddr2'],$_POST['EditCity'],
          $_POST['EditState'],$_POST['EditCountry'],$_POST['EditZip'])) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind add address query params: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec edit address query: '.$msi->error);
        goto sqlerror;
      }
      addassoc($msi,'address',$cid,$msi->insert_id);
    }
  }
  else if ($_POST['buttonAction'] == 'Edit') {
    /* editXxxIDs are set in Contact.js edit functions */
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
        $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to prep edit phone query: '.$msi->error);
        echo 'edit prep failed: '.$msi->error.'<br>';
        goto sqlerror;
      }
      if(!$stmt->bind_param('issi',$_POST['EditPhoneType'],
          $phone_number,$phone_formatted,$_POST['EditPhoneID'])) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind edit phone query params: '.$msi->error);
        echo 'edit bind failed: '.$msi->error.'<br>';
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec edit phone query: '.$msi->error);
        echo 'edit exec failed: '.$msi->error.'<br>';
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditEmailID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('update emails '.
        'set email_type_id=?,email=? where email_id=?')) {
        $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to prep edit email query: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('isi',$_POST['EditEmailType'],
          $_POST['EditEmail'],$_POST['EditEmailID'])) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind edit email query params: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec edit email query: '.$msi->error);
        goto sqlerror;
      }
    }
    else if(isset($_POST['EditAddressID'])) {
      /* can't change owner_id here */
      if(!$stmt=$msi->prepare('update addresses '.
        'set address_type_id=?,street_address_1=?,'.
        'street_address_2=?,city=?,state=?,country=?,postal_code=? '.
        'where address_id=?')) {
        $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to prep edit address query: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('issssssi',$_POST['EditAddressType'],
          $_POST['EditAddr1'],$_POST['EditAddr2'],$_POST['EditCity'],
          $_POST['EditState'],$_POST['EditCountry'],$_POST['EditZip'],
          $_POST['EditAddressID'])) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind edit address query params: '.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec edit address query: '.$msi->error);
        goto sqlerror;
      }
    }
  }
  else if ($_POST['buttonAction'] == 'Delete') {
    /* editXxxIDs are set in Contact.js edit functions */
    if(isset($_POST['EditPhoneID'])) {
      deletecoordinate($msi,'phone',$_POST['EditPhoneID']);
    }
    else if(isset($_POST['EditEmailID'])) {
      deletecoordinate($msi,'email',$_POST['EditEmailID']);
    }
    else if(isset($_POST['EditAddressID'])) {
      deletecoordinate($msi,'address',$_POST['EditAddressID']);
    }
  }
  sqlerror:
    if($stmt)$stmt->close();
}  // isset buttonAction

/* phone, e-mail, aaddress types for add & edit dialogs */
$smarty->assign('phone_type_list',get_types($msi,'phone'));
$smarty->assign('email_type_list',get_types($msi,'email'));
$smarty->assign('address_type_list',get_types($msi,'address'));
/* explanation for formatted checkbox for phone numbers */
$smarty->assign('phone_formatted','If this is checked, the program assumes the number should be presented as entered. If not, the program presents the number as a Canadian/US number: (123) 456-7890');


if($cid) {
  $cdata=new IData($msi,$smarty,$cid);;
  $smarty->assign('cdata',$cdata);
}
$smarty->display('contact.tpl');

function get_types($msi,$table) {
  $type_list=array();
  if($result=$msi->query("select $table"."_type_id,$table"."_type ".
     "from $table"."_types")) {
    while($tx = $result->fetch_assoc()) {
      $type_list[]=$tx;
    }
  }
  return $type_list;
}

function addassoc($msi,$coordinate,$cid,$coordinate_id) {
  if(!$msi->query("insert into $coordinate"."_associations ".
   "(contact_id,$coordinate"."_id) values ($cid,$coordinate_id)")) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       "add $coordinate association failed: ".$msi->error);
  }
}

function deletecoordinate($msi,$coordinate,$key_value) {
  if($coordinate=='address') {
    $table='addresses';
  }
  else {
    $table=$coordinate.'s';
  }
  $a_table=$coordinate.'_associations';
  $key_name=$coordinate.'_id';
  if(!$msi->query("delete from $table where $key_name=$key_value")) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       "delete $table query failed: ".$msi->error);
    return;
  }
  if(!$msi->query("delete from $a_table where $key_name=$key_value")) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       "delete $a_table query failed: ".$msi->error);
  }
  return;
}
?>
