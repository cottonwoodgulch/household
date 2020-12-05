<?php

require_once 'libe.php';
/* if password_reset=0,  password is new or has been reset, and 
   the user needs to change it
   if not, user requested to change password */

$err_msg='';
$is_error=false;
$user_id = $_SESSION['user_id'];

$result=$msi->query('select password,password_reset from contacts '.
   "where contact_id=$user_id");
if(!$result) {
  $is_error=true;
  $err_msg.='Retrieve password query failed: '.$msi->error.' ';
}
if(!$is_error) {
  $row=$result->fetch_row();
  $result->close();
  $oldpass=$row[0];
  /* the db value of password_reset is:
       1 for "password has been reset"
       0 for "password must be reset"
     for this app, $password_reset is "password reset is req'd" */
  $password_reset=$row[1];
  unset($row);
}

if($password_reset) {
  $smarty->assign('heading',
     'Changing password for '.$_SESSION['HelloName']);
}
else {
  $smarty->assign('heading',
     $_SESSION['HelloName'].', please reset your password');
}

if(!$is_error && $_POST['buttonAction'] == 'Cancel') {
  if($password_reset) {
    header("Location: index.php");
  }
  else {
    // reset is required, user cancelled, so log 'em out
    header("Location: logout.php");
  }
}

if(!$is_error && isset($_POST['newpass'])) {
  $newpass=$_POST['newpass'];
  
  if(strlen($newpass) < 6) {
    $err_msg.='Password is too short';
    $is_error=true;
  }
  
  if(!$is_error) {
    if(!$password_reset) {
      // user is required to change password
      if(password_verify($newpass,$oldpass)) {
        $err_msg.='Please CHANGE password ';
        $is_error=true;
      }
    }
  }

  if(!$is_error) {
    if($_POST['newpass'] == $_POST['retype']) {
      $pwhash=password_hash($_POST['newpass'],PASSWORD_DEFAULT);
      if(!$msi->query("update contacts set password='$pwhash',".
          "password_reset=1 where contact_id=$user_id")) {
        $is_err=true;
        $err_msg.='Set password query failed: '.$msi->error.' ';
      }
    }
    else {
      $is_error=true;
      $err_msg.="Passwords don't match ";
    }
  }
  if(!$is_error) {
    header("Location: index.php");
  }
}

displayFooter($smarty,$err_msg);
$smarty->display('pwreset.tpl');
?>
