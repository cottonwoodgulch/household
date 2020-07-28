<?php
/* summary.php */

require_once 'libe.php';
require_once 'objects.php';

// Gary Lesney for testing
$_GET['cid'] = 581;

if(isset($_POST['buttonAction'])) {
  // user selected a new household by household name or member name
  if($_POST['buttonAction'] == 'house') {
    $hid=$_POST['NameHouseID'];
  }
  else if($_POST['buttonAction'] == 'member') {
    $hid=$_POST['MemberHouseID'];
  }
}
else if(isset($_SESSION['household_id'])) {
  // a household was already set
  $hid=$_SESSION['household_id'];
}
else if(isset($_GET['cid'])) {
  // contact id sent in from gulchdbi
  $hid = getHouseholdFromContact($msi,$smarty,$_GET['cid']);
}
/*
echo 'buttonAction: '.$_POST['buttonAction'].' NameHouseID: '.$_POST['NameHouseID'];
echo 'MemberHouseID: '.$_POST['MemberHouseID'].' hid: '.$hid.' cid: '.$cid;
exit;
*/
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
