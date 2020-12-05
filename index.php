<?php

/* index.php
   if user is not logged in, libe.php redirects to login.php */

require_once 'libe.php';

if(isset($_GET['cid'])) {
  /* Arrived here from gulchdbi. I think it's ok to retrieve 
     the household even though the user may not be logged in  */
  $_SESSION['household_id']=getHouseholdFromContact($msi,$smarty,$_GET['cid']);
}

header("Location: details.php");

?>
