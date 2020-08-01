<?php

require_once 'libe.php';
require_once 'objects.php';

if(!isset($_SESSION['household_id'])) {
  header("Location: lookup.php");
}

$house=new HouseData($msi,$smarty,$_SESSION['household_id']);

if(isset($_GET['action'])){
  if($_GET['action']=='save') {
    $house->updateHouse($msi, $smarty);
  } else {
    $ErrMsg='';
    if(isset($_POST['buttonAction'])) {
      /* buttonAction (search param) */
      if($_POST['buttonAction']=='house') {
        $ErrMsg=buildErrorMessage($ErrMsg,'selected a household');

      } else if($_POST['buttonAction']=='contact') {
        $ErrMsg=buildErrorMessage($ErrMsg,'selected a member');
    
      } else {
        $ErrMsg=buildErrorMessage($ErrMsg,'buttonAction: '.$_POST['buttonAction']);
      }
    
      /* action (add or move form) */
      if ($_GET['action']=='move'){

      } else if ($_GET['action']=='add') {

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
