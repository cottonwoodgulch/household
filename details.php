<?php

require_once 'libe.php';
require_once 'objects.php';

$ErrMsg="";

if(isset($_POST['SelectedHouseID'])) {
  $hid=$_POST['SelectedHouseID'];
  $_SESSION['household_id']=$hid;
}
else if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}

/* buttonAction defaults to "" */
if($_POST['buttonAction']=='SaveNewHouse') {
  if(!$stmt=$msi->prepare('insert into household
    (name,salutation,mailname,modified) values(?,?,?,now())')) {
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to prep add household query: '.$msi->error);
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
  $house=new HouseData($msi,$smarty,$hid);
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
  if(!$stmt=$msi->prepare('delete from household where household_id=?')) {
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
}
//else if(isset($_POST['saveChange'])) {
else if($_POST['buttonAction']=='saveChange') {
  // update household info and preferred address from $_POST
  $house=new HouseData($msi,$smarty,$hid);
  $house->updateHouse($msi, $smarty);
}
else {
  $house=new HouseData($msi,$smarty,$hid);
}

displayFooter($smarty,$ErrMsg);
$smarty->assign('house',$house);
$smarty->display('details.tpl');

?>
