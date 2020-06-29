<?php

require_once 'libe.php';
require_once 'objects.php';
require_once 'updateHold.php';

if(isset($_POST['contact_id'])) {
  $contact_id=$_POST['contact_id'];

  /* If ButtonAction is supplied, this is a page reset,
       which calls for data update.
       updateHold.php places changes in hold_xxx table
       for review before posting to live database. */
  if(isset($_POST['buttonAction'])) {
    updateHold($smarty,$msi,$user_id,$contact_id);
  }
  /* retrieve user's data */
  $user_data=new UserData($msi,$smarty,$user_id,$contact_id);
  $smarty->assign('user',$user_data);
  $contact_data=new ContactData($msi,$smarty,$user_id,$contact_id);
  $smarty->assign('contact',$contact_data);

  /* retrieve titles, degrees, address-, phone-,
     and e-mail types for drop-downs */
  getTypes($msi,$smarty);
  
  if(isset($_POST['referrer']) && $_POST['referrer'] == 'release') {
    /* Referred by release screen - display button to return. */
    $smarty->assign('referrer','release');
  }
  else {
    $smarty->assign('referrer','edit_contact');
  }
}
else {
  $smarty->assign('footer','No contact specified');
}

$smarty->assign("localmenu",1);
$smarty->assign("changeclasses",1);
$smarty->display('edit_contact.tpl');

?>
