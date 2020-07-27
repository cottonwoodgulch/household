<?php
/* summary.php */

require_once 'libe.php';
require_once 'objects.php';

// Gary Lesney for testing
$_GET['cid'] = 581;

if(isset($_POST['buttonAction'])) {
  // user selected a new household by household name or member name
  if($_POST['buttonAction'] == 'house') {
    $hid=$_POST['HouseName'];
  }
  else if($_POST['buttonAction'] == 'member') {
    $hid=$_POST['MemberName'];
  }
}
else if(isset($_SESSION['household_id'])) {
  // a household was already set
  $hid=$_SESSION['household_id'];
}
else if(isset($_GET['cid'])) {
  $hid = getHouseholdFromContact($msi,$smarty,$_GET['cid']);
}

if($hid) {
  $tx=new HouseData($msi,$smarty,$hid);
  $_SESSION['household_id'] = $hid;
  $smarty->assign('house',$tx);
  $smarty->assign('address',$tx->getPreferredAddress());
}
else {
  header("Location: lookup.php");
}

$smarty->assign('referrer','home');
$smarty->display('summary.tpl');
?>
