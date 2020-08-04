<?php

require_once 'libe.php';
require_once 'objects.php';

function moveMember($cid, $destination_hid){
  /* 
  NOT TESTED
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
  NOT TESTED
  parameters:
    $cid : contact_id of member to be added, 
    $hid : household_id of house where user is being moved
  */

  /* fetch email_id */
  if($email_req=$msi->prepare(
    "select email_id from email_assocations where contact_id=?"
    )){
    $email_req->bind_param('i', $cid);

    if($email_req->execute()) {
      $result=$email_req->get_result();
      $vars=$result->fetch_assoc();
      $email_id=$vars['email_id'];
      $stmt->close;
      $result->free;
    }
  }
  if(!$email_req->execute()) {
    //set email_id = null
  }
  /* if member is in another household */

  /* if member is not in any household */
  if($stmt=$msi->prepare(
    "insert into household_members values (?, ?, ?)")) {
      $stmt->bind_param('iii', $hid, $cid, $email_id);
  }
  if(!$stmt->execute()) {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
        "addMember: unable to execute sql update: ".$msi->error);
  }
  $stmt->close();
}

if(!isset($_SESSION['household_id'])) {
  header("Location: lookup.php");
}

$house=new HouseData($msi,$smarty,$_SESSION['household_id']);

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
displayFooter($smarty,$ErrMsg);

$smarty->assign('house',$house);
$smarty->display('details.tpl');

?>