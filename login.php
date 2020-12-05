<?php

$login=true;
require_once 'libe.php';

if(isset($_POST['username']) && isset($_POST['password'])) {
  /* check username & pw in db */
  if($stmt=$msi->prepare("select contact_id,password,first_name,".   
      "password_reset from contacts where lower(username)=?")) {
    $stmt->bind_param('s',
       $msi->real_escape_string(strtolower($_POST['username'])));
    $stmt->execute();
    $stmt->bind_result($user_id, $pwhash, $HelloName,$password_reset);
    $stmt->fetch();
    $stmt->close();

    if(password_verify($_POST['password'],$pwhash)) {
      $_SESSION['user_id'] = $user_id;
      $_SESSION['HelloName'] = $HelloName;
      $_SESSION['username']=$_POST['username'];
      if(!$password_reset) {
        header("Location: pwreset.php");
      }
      else {
        header("Location: index.php");
      }
      exit;
    }
  }
  else {
    displayFooter($smarty,
     "Login: unable to create mysql statement object: ".$msi->error);
  }
}

/* if we didn't have a good login re-display form */
$smarty->display('login.tpl');
?>
