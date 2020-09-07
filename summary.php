<?php
/* summary.php */

require_once 'libe.php';
if(!$rbac->Users->hasRole('Financial Information Viewer',$_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'objects.php';

// Gary Lesney for testing
//$_GET['cid'] = 581;

if(isset($_GET['cid'])) {
  // contact id sent in from gulchdbi
  $hid = getHouseholdFromContact($msi,$smarty,$_GET['cid']);
}
else if(isset($_POST['TargetHouseID'])) {
  $hid=$_POST['TargetHouseID'];
}
else if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}

if($hid) {
  $tx=new HouseData($msi,$smarty,$hid);
  $_SESSION['household_id'] = $hid;
  $smarty->assign('house',$tx);
  $smarty->assign('address',$tx->getPreferredAddress());
}
/* if $hid is not set, only the Look up Household button will show */

$smarty->assign('referrer','home');
$smarty->display('summary.tpl');
?>
