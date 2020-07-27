/* ajax_details.php
   php code to check that household name is not empty
     and isn't already used */

<?php

require_once "../libe.php";

if($stmt=$msi->prepare(
   "select 0 from household where name=? and household_id !=?")) {
  $stmt->bind_param('si',$_GET['hname'], $_GET['hid']);
  $stmt->execute();
  $result=$stmt->get_result();
  if($result->num_rows > 0) {
    echo '1';  // error - name is in use
  }
  else {
    echo '0';
  }
  $stmt->close();
  $result->free();
}

?>
