<?php

require_once 'libe.php';
if(!$rbac->Users->hasRole('Financial Information Editor',$_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'objects.php';

$ErrMsg="";

if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}

/* buttonAction defaults to "" - that is, isset will always be true */
if($_POST['buttonAction']=='selectHouse') {
  /* yes, I know - this will re-set what was just set to $_SESSION[household_id] */
  $hid=$_POST['TargetHouseID'];
  $_SESSION['household_id']=$hid;

  buildErrorMessage($ErrMsg, $_POST['buttonAction'].$msi->error);
}
else if($_POST['buttonAction']=='SaveNewHouse') {
  if(!$stmt=$msi->prepare('insert into households
    (name,salutation,mailname,modified) values(?,?,?,now())')) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to prep add households query: '.$msi->error);
    goto sqlerror;
  }
  if(!$stmt->bind_param('sss',$_POST['HouseholdName'],$_POST['Salutation'],
       $_POST['MailName'])) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to bind add household query params: '.$msi->error);
    goto sqlerror;
  }
  if(!$stmt->execute()) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to exec add household query: '.$msi->error);
    goto sqlerror;
  }
  $hid=$msi->insert_id;
  $_SESSION['household_id']=$hid;
sqlerror:
  $stmt->close();
}
else if($_POST['buttonAction']=='Delete') {
  $msi->autocommit(false);
  /* delete request has been confirmed in the ConfirmDialog
     Delete members from household */
  if(!$stmt=$msi->prepare('delete from household_members where household_id=?')) {
    buildErrorMessage($ErrMsg,
        'unable to prep delete household members query'.$msi->error);
    goto delerror;
  }
  /* $hid was set from $_SESSION or SelectedHouseID */
  if(!$stmt->bind_param('i',$hid)) {
    buildErrorMessage($ErrMsg,
        'unable to bind delete household members query params'.$msi->error);
    goto delerror;
  }
  if(!$stmt->execute()) {
    buildErrorMessage($ErrMsg,
        'unable to exec delete household members query'.$msi->error);
    goto delerror;
  }
  /* delete household */
  if(!$stmt=$msi->prepare('delete from households where household_id=?')) {
    buildErrorMessage($ErrMsg,
        'unable to prep delete household query'.$msi->error);
    goto delerror;
  }
  if(!$stmt->bind_param('i',$hid)) {
    buildErrorMessage($ErrMsg,
        'unable to bind delete household query param'.$msi->error);
    goto delerror;
  }
  if(!$stmt->execute()) {
    buildErrorMessage($ErrMsg,
        'unable to exec delete household query'.$msi->error);
    goto delerror;
  }
delerror:
  if(strlen($ErrMsg)) {
    if(isset($stmt))$stmt->close;
    $msi->rollback();
  }
  else {
    $msi->commit();
  }
  $msi->autocommit(true);
  $hid=0;
  $_SESSION['household_id']=0;
}
else if($_POST['buttonAction']=='saveChange') {
  // update household info and preferred address from $_POST
  $house=new HouseData($msi,$smarty,$hid);
  $house->updateHouse($msi, $smarty);
}

else if ($_POST['buttonAction']=='moveMember') {
  $cid=$_POST['ContactID'];
  $target_hid=$_POST['TargetHouseID'];

  if(!$stmt=$msi->prepare('update household_members set household_id=? where contact_id=?')){
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to prep move member query: '.$msi->error);
    goto moveerror;
  }
  if(!$stmt->bind_param('ii', $target_hid, $cid)){
    $ErrMsg=buildErrorMessage($ErrMsg,
        'unable to bind move member query params'.$msi->error);
    goto moveerror;
  }
  if(!$stmt->execute()){
    $ErrMsg=buildErrorMessage($ErrMsg,
        'unable to execute move member query'.$msi->error);
    goto moveerror;
  }
  echo "buttonAction: ".$_POST['buttonAction'];

moveerror:
    echo "moveerror";
}

else if ($_POST['buttonAction']=='addMember') {
  $cid=$_POST['ContactID'];
  $target_hid=$_POST['TargetHouseID'];
  $already_member=$_POST['CurrentHouseID']; //query in AddLookup.php will set this to 0 if null

  // the contact is already in another household
  if($already_member){
    $stmt=$msi->prepare('update household_members set household_id=? where contact_id=?');
  // the contact has no household
  } else {
    $stmt=$msi->prepare("insert into household_members (household_id, contact_id, modified) values (?, ?, now())");
  }

  if(!$stmt){
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to prep move member query: '.$msi->error);
    goto adderror;
  }
  if(!$stmt->bind_param('ii', $target_hid, $cid)){
    $ErrMsg=buildErrorMessage($ErrMsg,
        'unable to bind move member query params'.$msi->error);
    goto adderror;
  }
  if(!$stmt->execute()){
    $ErrMsg=buildErrorMessage($ErrMsg,
        'unable to execute move member query'.$msi->error);
    goto adderror;
  }

adderror:
    echo "adderror";
}

displayFooter($smarty,$ErrMsg);
$house=new HouseData($msi,$smarty,$hid);
$smarty->assign('house',$house);
$smarty->display('details.tpl');

?>
