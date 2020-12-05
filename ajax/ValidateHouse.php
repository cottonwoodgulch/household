<?php

/* ajax response to check that household name isn't in use */

require_once "../libe.php";

/*
$_GET['hname']='Hyder';
$_GET['hid']=0;
*/

if($stmt=$msi->prepare(
   "select 0 from households where name=? and household_id !=?")) {
  $stmt->bind_param('si',$_GET['hname'], $_GET['hid']);
  $stmt->execute();
  $result=$stmt->get_result();
  if($result->num_rows > 0) {
    // true if name is in use
    echo "1";
  }
  else {
    echo "0";
  }
  $stmt->close();
  $result->free();
}

?>
