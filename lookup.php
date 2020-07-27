<?php

require_once 'libe.php';

/*
$ErrMsg='';
if(isset($_POST['buttonAction'])) {
  if($_POST['buttonAction']=='house') {
    $ErrMsg=buildErrorMessage($ErrMsg,'selected a household');
  }
  else if($_POST['buttonAction']=='member') {
    $ErrMsg=buildErrorMessage($ErrMsg,'selected a member');
  }
  else {
    $ErrMsg=buildErrorMessage($ErrMsg,'buttonAction: '.$_POST['buttonAction']);
  }
}
*/

displayFooter($smarty,$ErrMsg);
$smarty->display('lookup.tpl');

?>
