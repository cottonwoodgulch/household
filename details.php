<?php

require_once 'libe.php';
require_once 'objects.php';

function moveMember($cid, $destination_hid){
  /* 
  UNTESTED
  parameters:
    $cid : contact_id, 
    $destination_hid : household_id of house where user is being moved
  */
  if($stmt=$msi->prepare(
  "update household_members set household_id=? where contact_id=?")) {
    $stmt->bind_param('ii', $destination_hid, $cid);
  }
  if(!$stmt->execute()) {
    $this->ErrMsg=buildErrorMessage($this->ErrMsg,
      "moveMember: unable to execute sql update: ".$msi->error);
  }
  $stmt->close();
}

function addMember($cid, $hid){
  /* 
  UNTESTED
  parameters:
    $cid : contact_id of member to be added, 
    $hid : household_id of house where user is being moved
  */

  /* if member is in another household */
  $already_member=$msi->query("select 1 from household_members where contact_id=?");
  if($already_member){
    if($stmt=$msi->prepare(
      "update household_members set household_id=? where contact_id=?)")) {
        $stmt->bind_param('ii', $hid, $cid);
    }
  /* if member is not in any household */
  } else {
    if($stmt=$msi->prepare(
      "insert into household_members values (?, ?, ?)")) {
        $stmt->bind_param('iii', $hid, $cid);
    }
  }

  if(!$stmt->execute()) {
    $this->ErrMsg=buildErrorMessage($this->ErrMsg,
      "addMember: unable to execute sql update: ".$msi->error);
  }
  $stmt->close();
}

/* Script starts here */ 

if(!isset($_SESSION['household_id'])) {
  header("Location: lookup.php");
}

if(isset($_POST['SelectedHouseID'])) {
  $hid=$_POST['SelectedHouseID'];
  $_SESSION['household_id']=$hid;
}
else if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}

if(isset($_GET['action'])){
/* house information is updated */
  if($_GET['action']=='save') {
    $house->updateHouse($msi, $smarty);

/* house members are moved or added */
  } else {
    $ErrMsg='';
    if(isset($_POST['buttonAction'])) {
      /* buttonAction (household search by house or contact) */
      if($_POST['buttonAction']=='house') {
        $hid=$_POST['NameHouseID'];

      } else if($_POST['buttonAction']=='member') {
        $hid=$_POST['MemberHouseID'];
    
      } else {
        $ErrMsg=buildErrorMessage($ErrMsg,'buttonAction: '.$_POST['buttonAction']);

      }
    
/* form action (add or move) */
      if ($_GET['action']=='move') {
        $contact_id=$_POST['selected_contact_id'];
        moveMember($contact_id, $hid);
      } else if ($_GET['action']=='add') {
        // pass

      } else {
        // pass
      }
  
    }
  }
}

if(isset($_POST['saveChange'])) {
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
  } else if($_POST['buttonAction']=='Delete') {
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
  } else if($_POST['buttonAction']=='saveChange') {
    // update household info and preferred address from $_POST
    $house=new HouseData($msi,$smarty,$hid);
    $house->updateHouse($msi, $smarty);
  } else {
    $house=new HouseData($msi,$smarty,$hid);
  }
}

displayFooter($smarty,$ErrMsg);
$smarty->assign('house',$house);
$smarty->display('details.tpl');

?>