<?php
/* summary.php */

require_once 'libe.php';
require_once 'objects.php';

// Gary Lesney for testing
//$_GET['cid'] = 581;

if(isset($_POST['SelectedHouseID'])) {
  $hid=$_POST['SelectedHouseID'];
}
else if(isset($_GET['cid'])) {
  // contact id sent in from gulchdbi
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
