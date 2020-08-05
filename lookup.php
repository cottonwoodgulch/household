<?php

require_once 'libe.php';

if(isset($_POST['SelectedHouseID'])) {
  $ErrMsg='SelectedHouseID: '.$_POST['SelectedHouseID'];
}
else {
  $ErrMsg='SelectedHouseID not set';
}

displayFooter($smarty,$ErrMsg);
$smarty->display('lookup.tpl');
?>
