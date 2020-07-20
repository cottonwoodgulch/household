<?php
/* home.php */

require_once 'libe.php';
require_once 'objects.php';

// Gary Lesney for testing
$_GET['cid'] = 140;

if(isset($_GET['cid'])) {
  $hid = getHouseholdFromContact($msi,$smarty,$_GET['cid']);
  if($hid) {
    $tx=new HouseData($msi,$smarty,$hid);
    $_SESSION['household_id'] = $hid;
    $smarty->assign('house',$tx);
    $smarty->assign('address',$tx->getPreferredAddress());
  }
}
else {
  header("Location: lookup.php");
}

$smarty->assign('referrer','home');
$smarty->display('home.tpl');
?>
