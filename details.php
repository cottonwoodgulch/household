<?php

require_once 'libe.php';
require_once 'objects.php';

if(!isset($_SESSION['household_id'])) {
  header("Location: lookup.php");
}

$house=new HouseData($msi,$smarty,$_SESSION['household_id']);

if(isset($_GET['action']) && $_GET['action']=='save') {
  $house->updateHouse($msi, $smarty);
}

$ErrMsg='';
if(isset($_POST['buttonAction'])) {
  if($_POST['buttonAction']=='house') {
    $ErrMsg=buildErrorMessage($ErrMsg,'selected a household');
    // Do the SQL

  } else if($_POST['buttonAction']=='contact') {
    $ErrMsg=buildErrorMessage($ErrMsg,'selected a member');
    
  } else {
    $ErrMsg=buildErrorMessage($ErrMsg,'buttonAction: '.$_POST['buttonAction']);
  }

}
displayFooter($smarty,$ErrMsg);

$smarty->assign('house',$house);
$smarty->display('details.tpl');

?> 
