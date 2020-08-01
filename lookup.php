<?php

require_once 'libe.php';

/*
if(isset($_POST['buttonAction'])) {

  echo 'action: '.$_POST['buttonAction'].', HouseholdName: '.
     $_POST['HouseholdName'];
  echo 'salut: '.$_POST['Salutation'].', mailname: '.$_POST['MailName'];

}
*/


displayFooter($smarty,$ErrMsg);
$smarty->display('lookup.tpl');

?>
