/* ajax_details.php
   php code to check that household name is not empty
     and isn't already used */

<?php

require_once "../libe.php";

if(isset($_GET['hname'])) {
  $household_name=$_GET['hname'];
  if(strlen($household_name) <= 0) {
    echo 'Household name is required';
  }
  else {
    $hid=$_GET['hid'];
    if(is_null($hid) || $hid == 0) {
      echo 'Missing household_id';
    }
    else {
      if($stmt=$msi->prepare(
         "select count(*) hc
            from household
           where name=?
             and household_id !=?")) {
        $stmt->bind_param('si',$hname, $hid);
        $stmt->execute();
        $result=$stmt->get_result();
        if($result->num_rows > 0) {
          echo 'Household name already in use';
        }
        else {
          echo '';
        }
        $stmt->close();
        $result->free();
      }
    }
  }
}

?>
