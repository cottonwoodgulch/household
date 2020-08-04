<?php

require_once 'libe.php';
require_once 'objects.php';

if(!isset($_SESSION['household_id'])) {
  header("Location: lookup.php");
}

$house=new HouseData($msi,$smarty,$_SESSION['household_id']);

if(isset($_POST['saveChange'])) {
  // update household info and preferred address from $_POST
  $house->updateHouse($msi, $smarty);
}

$smarty->assign('house',$house);
$smarty->display('details.tpl');

?>
