<?php

require_once 'libe.php';
require_once 'objects.php';

if(!isset($_SESSION['household_id'])) {
  header("Location: lookup.php");
}
$house=new HouseData($msi,$smarty,$_SESSION['household_id']);

if(isset($_GET['action']) && $_GET['action']=='save') {
  // update member info from $_POST
  $house->updateMembers($msi, $smarty);
}

$smarty->assign('house',$house);
$smarty->display('details.tpl');


?>
