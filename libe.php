<?php

require_once 'vendor/autoload.php';

/* PHP requires setting a timezone. This will be fine,
   since the app doesn't require a time */
date_default_timezone_set('America/New_York');

$smarty = new Smarty();
$smarty->addTemplateDir(__DIR__ . '/templates');
$smarty->addPluginsDir(__DIR__ . '/plugins');
// $smarty->addPluginsDir(__DIR__ . '/plugins');
session_start();
if(isset($_SESSION['user_id'])) {
  $user_id=$_SESSION['user_id'];
  $smarty->assign('user_id',$user_id);
  $smarty->assign('HelloName',$_SESSION['HelloName']);
}
else {
  $user_id=0;
  if(!isset($login)) {
    header("Location: login.php");
  }
}

/* Set permissions.
   Viewer can see everyone's information, editor can make changes
*/
$rbac = new PhpRbac\Rbac();
$smarty->assign('is_contact_viewer',
  $rbac->Users->hasRole('Contact Information Viewer',$user_id));
$is_contact_editor=
   $rbac->Users->hasRole('Contact Information Editor',$user_id);
$smarty->assign('is_contact_editor',$is_contact_editor);
// database connection
include 'config.php';
$msi = new mysqli($db_host, $db_user, $db_pw, $db_db);

$sitemenu=array(array('d' => 'Home','t' => 'home'),
                array('d' => 'Details', 't' => 'details'),
                array('d' => 'Donations', 't' => 'donations'),
                array('d' => 'Lookup','t' => 'lookup'),
                array('d' => 'New Household', 't' => 'new'));
$smarty->assign('sitemenu',$sitemenu);

function getHouseholdFromContact($msi,$smarty,$cid) {
  /* also gets contact name in case of error */
  if($stmt=$msi->prepare(
       "select h.household_id, c.first_name, c.primary_name
          from contacts c
          left join household_members hm
            on hm.contact_id=c.contact_id
          left join household h
            on h.household_id=hm.household_id
         where c.contact_id=?")) {
    $stmt->bind_param('i',$cid);
    $stmt->execute();
    $result=$stmt->get_result();
    $hx = $result->fetch_assoc();
    $result->free();
    $stmt->close();
  }
  else {
    $smarty->assign('footer',"getHouseholdFromContact: unable to create mysql statement object: ".$msi->error);
    return FALSE;
  }
  if(is_null($hx['household_id'])) {
    $smarty->assign('footer',
      "No household for $cid ".$hx['first_name'].' '.$hx['primary_name']);
    return FALSE;
  }
  return $hx['household_id'];
}
  
function buildErrorMessage($errmsg,$newerr) {
  if(strlen($errmsg)) {
    $errmsg .= '<br />'.$newerr;
  }
  else {
    $errmsg = $newerr;
  }
  return $errmsg;
}

function displayFooter($smarty,$err_msg) {
  /* footer will display if the smarty variable footer is set */
  if(strlen(trim($err_msg)) > 0) {
    $msg='<table><tr>
      <td class="footermsg">'.$err_msg.
      '</td><td class="footermsg"><button type="button" '.
      'onClick="hideFooter();">Close</button></td></tr></table>';
    $smarty->assign('footer',$msg);
  }
}

?>
