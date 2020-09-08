<?php

require_once 'libe.php';

if(!$rbac->Users->hasRole('Financial Information Editor',
  $_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'objects.php';

$ErrMsg='';
if(isset($_POST['TargetHouseID'])) {
  $hid=$_POST['TargetHouseID'];
  $_SESSION['household_id']=$hid;
}
else if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}

if(isset($_POST['buttonAction'])) {
  if($_POST['buttonAction'] == 'Add') {
    if(!$stmt=$msi->prepare('insert into hdonations 
      (primary_donor_id, ddate, amount, fund_id, anonymous, purpose, modified)
      values(?,str_to_date(?,\'%Y-%m-%d\'),?,?,?,?,now())')) {
      $ErrMsg=buildErrorMessage($ErrMsg,
         'unable to prep add donation query: '.$msi->error);
      goto sqlerror;
    }
    $anon=isset($_POST['EditAnonymous']) ? 1 : 0;
    if(!$stmt->bind_param('isdiis',$_POST['EditPrimaryDonor'],$_POST['EditDate'],
         $_POST['EditAmount'],$_POST['EditFund'],$anon,$_POST['EditPurpose'])) {
      $ErrMsg=buildErrorMessage($ErrMsg,
         'unable to bind add donation query params: '.$msi->error);
      goto sqlerror;
    }
    if(!$stmt->execute()) {
      $ErrMsg=buildErrorMessage($ErrMsg,
         'unable to exec add donation query: '.$msi->error);
      goto sqlerror;
    }
  }
  else if($_POST['buttonAction'] == 'Edit') {
    // update donations rec
    if(!$stmt=$msi->prepare('update hdonations set primary_donor_id=?,
        ddate=str_to_date(?,\'%Y-%m-%d\'),
        amount=?,fund_id=?,anonymous=?,purpose=?,modified=now()
        where donation_id=?')) {
      $ErrMsg=buildErrorMessage($ErrMsg,
         'unable to prep update donation query: '.$msi->error);
      goto sqlerror;
    }
    $anon=isset($_POST['EditAnonymous']) ? 1 : 0;
    if(!$stmt->bind_param(isdiisi,$_POST['EditPrimaryDonor'],$_POST['EditDate'],
         $_POST['EditAmount'],$_POST['EditFund'],$anon,$_POST['EditPurpose'],
        $_POST['EditDonationID'])) {
       $ErrMsg=buildErrorMessage($ErrMsg,
          'unable to bind update donation query params: '.$msi->error);
      goto sqlerror;
    }
    if(!$stmt->execute()) {
      $ErrMsg=buildErrorMessage($ErrMsg,
         'unable to exec update donation query: '.$msi->error);
      goto sqlerror;
    }
  }
  else if($_POST['buttonAction'] == 'Delete') {
    /* delete request has been confirmed in the ConfirmDialog */
    if(!$stmt=$msi->prepare('delete from hdonations where donation_id=?')) {
      buildErrorMessage($ErrMsg,
          'unable to prep delete hdonation query'.$msi->error);
      goto sqlerror;
      }
    if(!$stmt->bind_param('i',$_POST['EditDonationID'])) {
      buildErrorMessage($ErrMsg,
          'unable to bind delete hdonation query param'.$msi->error);
      goto sqlerror;
    }
    if(!$stmt->execute()) {
      buildErrorMessage($ErrMsg,
          'unable to exec delete donation query'.$msi->error);
      goto sqlerror;
    }
  }
  sqlerror:
  $stmt->free_result;
}

if(isset($hid)) {
  $house=new HouseData($msi,$smarty,$hid);

  /* fund list for Add and Edit Donation dialogs */
  if($result=$msi->query('select fund_id,fund from funds')) {
    while($tx = $result->fetch_assoc()) {
      $fund_list[]=$tx;
    }  
    $smarty->assign('fund_list',$fund_list);
  }
  else {
    buildErrorMessage($ErrMsg,'fund query failed: '.$msi->error);
  }
  /* list of members for primary donor */
  $smarty->assign('members',$house->members);
}

displayFooter($smarty,$ErrMsg);
$smarty->assign('house',$house);
$smarty->display('donations.tpl');

?>
