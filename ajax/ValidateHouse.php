<?php

/* ajax response to check that household name isn't in use */

require_once "../libe.php";

if($stmt=$msi->prepare(
   "select 0 from household where name=? and household_id !=?")) {
  $stmt->bind_param('si',$_GET['hname'], $_GET['hid']);
  $stmt->execute();
  $result=$stmt->get_result();
  // true if name is in use
  echo ($result->num_rows > 0);
  $stmt->close();
  $result->free();
}

?>
