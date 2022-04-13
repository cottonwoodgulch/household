<?php

require_once 'libe.php';
if(!$rbac->Users->hasRole('Contact Information Viewer',
     $_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}

require_once 'objects.php';
$ErrMsg=array();

$fy=isset($_POST['fy']) ? $_POST['fy'] : '';
$recno=isset($_POST['recno']) ? $_POST['recno'] : 0;
$hid=isset($_POST['hid']) ? $_POST['hid'] : 0;
$eof=false;

/*echo "POST fy,recno, hid: $fy, $recno, $hid<br>";
echo "POST buttonAction, subAction: ".$_POST['buttonAction'].', '.
   $_POST['subAction'].'<br>';*/

if(isset($_POST['buttonAction'])) {
  //echo 'button action: '.$_POST['buttonAction'].'<br>';
  $buttonaction=$_POST['buttonAction'];
  if($buttonaction == 'Update' || $buttonaction == 'MarkDone') {
    // if MarkDone, set done=1
    // redisplay, $hid, $fy, $recno stay the same
    $qwhere="where fy='$fy' && recno=$recno";
  }
  else if($buttonaction == 'Next') {
    $hid=0;
    $qwhere="where !done && fy>='$fy' && recno>$recno";
  }
  else if($buttonaction == "SwitchHouse") {
    /* same donation rec, different household */
    $qwhere="where fy='$fy' && recno=$recno";
  }
  else {
    // reload - get first not-done import rec
    $fy='';
    $recno=0;
    $hid=0;
    $qwhere="where !done";
    // query just !done
  }
}
else {
  // first entry
  $qwhere="where !done";
}

$tx=uSelect($msi,
   'select di_id,fy,recno,ddate,amount,lname,fname,salutation,'.
   "concat(fname,' ',lname) mailname,".
   'fund,donornote,dedication,street,city,state,zip,country,'.
   'emails email,contactnote from donationimport '.$qwhere.       
   ' order by fy,recno limit 1','import info',$ErrMsg);
if(count($tx)) {
  $eof=0;
  $di=$tx[0];
}
else {
  $eof=1;
  $di=array('di_id' => 0);
}

/* get current - target household from member names*/
//   'h.mailname,h.address_id from di_names n '.
$currh=uSelect($msi,'select distinct n.household_id,'.
   'h.mailname from di_names n '.
  'left join household_members hm on hm.contact_id=n.contact_id '.
  'left join households h on h.household_id=hm.household_id '.
  'where n.di_id='.$di['di_id'],'household info',$ErrMsg);
if(count($currh)) {
  if(!$hid) {
    $hid=$currh[0]['household_id'];
    $_SESSION['household_id']=$hid;
  }
  $smarty->assign('current',$currh);
}
else {
  $hid=0;
}

//echo "pre hid-check: hid, buttonaction: $hid, $buttonaction<br>";
if($hid) {
  /* if $hid from $_POST or by looking up member names */
  /* this is where updates are made to target household ($hid) */
  if($buttonaction == 'Update') {
    switch($_POST['subAction']) {
    case 'salutation':
      if(!$stmt=$msi->prepare(
         'update households set salutation=? where household_id=?')) {
        buildErrorMessage($ErrMsg,'prep update salutation: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('si',$di['salutation'],$hid)) {
        buildErrorMessage($ErrMsg,'bind update salutation: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($errMsg,'exec salutation: ',$msi->error);
        goto sqlerror;
      }
      break;
    case 'mailname':
      if(!$stmt=$msi->prepare(
         'update households set mailname=? where household_id=?')) {
        buildErrorMessage($ErrMsg,
           'prep update mailname: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('si',$di['mailname'],$hid)) {
        buildErrorMessage($ErrMsg,
          'bind update mailname: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,
          'exec mailname: ',$msi->error);
        goto sqlerror;
      }
      break;
    case 'Address':
      if($_POST['AddressAction'] == 'updateAddress') {
        if(!$stmt=$msi->prepare(
           'update households h '.
           'inner join addresses a on a.address_id=h.address_id '.
           'set a.address_type_id=?,a.owner_id=?,a.street_address_1=?,'.
           'a.street_address_2=?,a.city=?,a.state=?,a.postal_code=?,'.
           'a.country=? where h.household_id=?')) {
          buildErrorMessage($ErrMsg,'prep update address: ',$msi->error);
          goto sqlerror;
        }
        if(!$stmt->bind_param('iissssssi',$_POST['EditAddressType'],
           $_POST['EditOwner'],$_POST['EditAddr1'],$_POST['EditAddr2'],
           $_POST['EditCity'],$_POST['EditState'],$_POST['EditZip'],
           $_POST['EditCountry'],$hid)) {
          buildErrorMessage($ErrMsg,'bind update address: ',$msi->error);
          goto sqlerror;
        }
        if(!$stmt->execute()) {
          buildErrorMessage($ErrMsg,'exec update address: ',$msi->error);
          goto sqlerror;
        }
      }
      else {
        /* addAddress - create new address & address_association
           and set household.address_id to point to new address */
        if(!isset($_POST['EditOwner']) ||
            $_POST['EditOwner'] == 0) {
          buildErrorMessage($ErrMsg,'Owner is required');
          goto sqlerror;
        }
        if(!$stmt=$msi->prepare(
           'insert into addresses (address_type_id,owner_id,'.
           'street_address_1,street_address_2,city,state,country,'.
           'postal_code) values(?,?,?,?,?,?,?,?)')) {
          buildErrorMessage($ErrMsg,'prep add address: ',$msi->error);
          goto sqlerror;
        }
        if(!$stmt->bind_param('iissssss',$_POST['EditAddressType'],
           $_POST['EditOwner'],$_POST['EditAddr1'],$_POST['EditAddr2'],
           $_POST['EditCity'],$_POST['EditState'],
           $_POST['EditCountry'],$_POST['EditZip'])) {
          buildErrorMessage($ErrMsg,'bind add address: ',$msi->error);
          goto sqlerror;
        }
        if(!$stmt->execute()) {
          buildErrorMessage($ErrMsg,'exec add address: ',$msi->error);
          goto sqlerror;
        }
        $address_id=$msi->insert_id;
        if(!addassoc($msi,'address',$_POST['EditOwner'],$address_id)) {
          buildErrorMessage($ErrMsg,
            'add address association: ',$msi->error);
          goto sqlerror;
        }
        if(!$msi->query('update households set address_id='.
          "$address_id where household_id=$hid")) {
          buildErrorMessage($ErrMsg,
            'set new household address: ',$msi->error);
          goto sqlerror;
        }
      }
      break;
    case 'Donation':
      if(!isset($_POST['EditPrimaryDonor']) ||
         $_POST['EditPrimaryDonor'] == 0) {
        buildErrorMessage($ErrMsg,'Primary Donor is required');
        goto sqlerror;
      }
      if(!$stmt=$msi->prepare(
         'insert into hdonations (primary_donor_id,ddate,amount,'.
         'anonymous,fund_id,purpose) values (?,?,?,?,?,?)')) {
        buildErrorMessage($ErrMsg,'prep add donation: ',$msi->error);
        goto sqlerror;
      }
      $anon=isset($_POST['EditAnonymous']) ? 1 : 0;
      if(!$stmt->bind_param('isdiis',$_POST['EditPrimaryDonor'],
         $_POST['EditDate'],$_POST['EditAmount'],$anon,
         $_POST['EditFund'],$_POST['EditPurpose'])) {
        buildErrorMessage($ErrMsg,'bind add donation: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,'exec add donation: ',$msi->error);
        goto sqlerror;
      }
      break;
    case 'Phone':
      if(!isset($_POST['EditOwner']) ||
            $_POST['EditOwner'] == 0) {
        buildErrorMessage($ErrMsg,'Owner is required');
        goto sqlerror;
      }
      if(!$stmt=$msi->prepare(
         'insert into phones (phone_type_id,owner_id,'.
         'number,formatted) values (?,?,?,?)')) {
        buildErrorMessage($ErrMsg,'prep add phone: ',$msi->error);
        goto sqlerror;
      }
      if(isset($_POST['EditFormatted'])) {
        $phone_number=$_POST['EditNumber'];
        $phone_formatted=true;
      }
      else {
        /* strip non-numeric characters */
        $phone_number=preg_replace('/[^0-9]/','',$_POST['EditNumber']);
        $phone_formatted=false;
      }
      if(!$stmt->bind_param('iisi',$_POST['EditPhoneType'],
         $_POST['EditOwner'],$phone_number,$phone_formatted)) {
        buildErrorMessage($ErrMsg,'bind add phone: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,'exec add phone: ',$msi->error);
        goto sqlerror;
      }
      if(!addassoc($msi,'phone',$_POST['EditOwner'],$msi->insert_id)) {
        buildErrorMessage($ErrMsg,'add phone association: ',$msi->error);
        goto sqlerror;
      }
      break;
    case 'Email':
      if(!isset($_POST['EditOwner']) ||
            $_POST['EditOwner'] == 0) {
        buildErrorMessage($ErrMsg,'Owner is required');
        goto sqlerror;
      }
      if(!$stmt=$msi->prepare(
         'insert into emails (email_type_id,owner_id,email) '.
         'values (?,?,?)')) {
        buildErrorMessage($ErrMsg,'prep add email: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iis',$_POST['EditEmailType'],
         $_POST['EditOwner'],$_POST['EditEmail'])) {
        buildErrorMessage($ErrMsg,'bind add email: ',$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        buildErrorMessage($ErrMsg,'exec add email: ',$msi->error);
        goto sqlerror;
      }
      $email_id=$msi->insert_id;
      if(!addassoc($msi,'email',$_POST['EditOwner'],$email_id)) {
        buildErrorMessage($ErrMsg,'add email association: ',$msi->error);
        goto sqlerror;
      }
      if(isset($_POST['EditPreferred'])) {
        if(!$msi->query("insert into preferred_emails ".
           "values (null,$hid,$email_id)")) {
          buildErrorMessage($ErrMsg,
             "set preferred email: ",$msi->error);
        }
      }
      break;
    }
  }
  else if($buttonaction == 'MarkDone') {
    //echo "MarkDone fy,recno, hid: $fy, $recno, $hid<br>";
    if(!$msi->query('update donationimport set done=1 '.
       "where fy='$fy' and recno=$recno")) {
      buildErrorMessage($ErrMsg,'mark done: ',$msi->error);
      goto sqlerror;
    }
  }
  else if($buttonaction == 'ClearList') {
    //echo "Clear Import List";
    if(!$msi->query('delete from donationimport')) {
      buildErrorMessage($ErrMsg,'clear donation imports: ',$msi->error);
      goto sqlerror;
    }
  }
}
sqlerror:

/* retrieve updated target household info */
$house=new HouseData($msi,$smarty,$hid,$ErrMsg);
/* only for Address Update dropdown */
foreach($house->addresses as $tx) {
  if($tx['preferred']) {
    $smarty->assign('preferred_address_owner',$tx['owner_id']);
    break;
  }
}
$smarty->assign('house',$house);

/* if current salutation is reverse (around and) of import */
$di['salutationmatch']=0;
if($di['salutation']==$house->hd['salutation']) {
  $di['salutationmatch']=1;
}
else {
  if(($diand=strpos($di['salutation'],' and ')) &&
    $hdand=strpos($house->hd['salutation'],' and ')) {
    /* check if names are reversed */
    if(substr($di['salutation'],$diand+5).' and '.
       substr($di['salutation'],0,$diand)==$house->hd['salutation']) {
      $di['salutationmatch']=1;
    }
  }
}

/* if donation on import rec is similar to existing one */
$di['donationmatch']=0;
foreach($house->donations as $tx) {
  if($tx['ddate']==$di['ddate'] && $tx['amount']==$di['amount']) {
    $di['donationmatch']=1;
    break;
  }
}

/* only offer to add phone numbers that aren't already there */
$phones=uSelect($msi,
   "select number,replace(number,'-','') ufnum ".
   'from di_phones where di_id='.$di['di_id'],
   'phones on import rec',$ErrMsg);
foreach($phones as &$px) {
  /* only offer to add phone numbers that aren't already there */
  $px['ok']=
     array_matchfield($px['ufnum'],$house->phones,'number') ? 1 : 0;
}
unset($px);
$smarty->assign('phones',$phones);
$emails=array('email' => $di['email'],
  'ok'=> array_matchfield($di['email'],$house->emails,'email') ? 1 : 0);
$smarty->assign('emails',$emails);

/* explanation for formatted checkbox for phone numbers */
$smarty->assign('phone_formatted','If this is checked, the program assumes the number should be presented as entered. If not, the program presents the number as a Canadian/US number: (123) 456-7890');

$smarty->assign('phone_type_list',uSelect($msi,
   'select phone_type_id,phone_type from phone_types',
   'phone types',$ErrMsg));
$smarty->assign('email_type_list',uSelect($msi,
   'select email_type_id,email_type from email_types',
   'email types',$ErrMsg));
$smarty->assign('address_type_list',uSelect($msi,
   'select address_type_id,address_type from address_types',
   'address types',$ErrMsg));
$smarty->assign('fund_list',
   uSelect($msi,'select fund_id,fund from funds',
   'fund list query',$ErrMsg));

displayFooter($smarty,$ErrMsg);
$smarty->assign('di',$di);
$smarty->assign('hid',$hid);
$smarty->assign('eof',$eof);  // if past last donationimport rec
$smarty->display('import.tpl');

function addassoc($msi,$coordinate,$cid,$coordinate_id) {
  if($msi->query("insert into $coordinate"."_associations ".
     "(contact_id,$coordinate"."_id) values ($cid,$coordinate_id)")) {
    return true;
  }
  buildErrorMessage($ErrMsg,
     "add $coordinate association failed: ",$msi->error);
  return false;
}
?>
