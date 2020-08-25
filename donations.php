<?php

require_once 'libe.php';
if(!$rbac->Users->hasRole('Financial Information Editor',$_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'objects.php';

$ErrMsg='';
if(isset($_POST['SelectedHouseID'])) {
  $hid=$_POST['SelectedHouseID'];
  $_SESSION['household_id']=$hid;
}
else if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}
else {
  header("Location: lookup.php");
}

if(isset($_POST['buttonAction'])) {
  if($_POST['buttonAction'] == 'Add') {
    /* donation_id, donor_id, date, amount, [check_number, check_id, fund_id,
       anonymous, share_count, share_value, share_company,] purpose, modified */
    /* this only adds a donation_associations rec for the primary donor */
    // first, add donation rec
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
    
    // then add donation_associations rec
    /*$new_donation_id=$msi->insert_id;
    if(!$stmt=$msi->prepare('insert into donation_associations values(null,?,?)')) {
      $ErrMsg=buildErrorMessage($ErrMsg,
         'unable to prep donation assn query: '.$msi->error);
      goto sqlerror;
    }
    $stmt->bind_param('ii',$_POST['EditPrimaryDonor'],$new_donation_id);
    if(!$stmt->execute()) {
      $ErrMsg=buildErrorMessage($ErrMsg,
        'unable to exec donation assn query: '.$msi->error);
      goto sqlerror;
    }
    $stmt->free_result();*/
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
    // update donation_associations rec if nec
    /*if($_POST['EditPrimaryDonor'] != $_POST['OldPrimaryDonorID']) {
      if(!$stmt=$msi->prepare('update donation_associations set contact_id=?
         where donation_id=? and contact_id=?')) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to prep update donation assn query'.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->bind_param('iii',$_POST['EditPrimaryDonor'],
         $_POST['EditDonationID'],$_POST['OldPrimaryDonorID'])) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to bind update donation assn query params'.$msi->error);
        goto sqlerror;
      }
      if(!$stmt->execute()) {
        $ErrMsg=buildErrorMessage($ErrMsg,
           'unable to exec update donation assn query'.$msi->error);
        goto sqlerror;
      }
      $stmt->free_result();
    } */
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
    
    /*if(!$stmt=$msi->prepare('delete from donation_associations where
       donation_id=? and contact_id=?')) {
      buildErrorMessage($ErrMsg,
          'unable to exec update donation assn query'.$msi->error);
      goto sqlerror;
      }
    if(!$stmt->bind_param('ii',$_POST['EditDonationID'],
        $_POST['OldPrimaryDonorID'])) {
      buildErrorMessage($ErrMsg,
          'unable to bind update donation assn query params'.$msi->error);
      goto sqlerror;
    }
    if(!$stmt->execute()) {
      buildErrorMessage($ErrMsg,
          'unable to exec update donation assn query'.$msi->error);
      goto sqlerror;
    }*/
  }
  sqlerror:
  $stmt->free_result;
}

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

displayFooter($smarty,$ErrMsg);
$smarty->assign('house',$house);
$smarty->display('donations.tpl');

?>
