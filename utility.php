<?php
/* utility.php
   - Address Lookup */

require_once 'libe.php';
if(!$rbac->Users->hasRole('Contact Information Viewer',
     $_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}

/* if output is to a csv file, don't re-display the page,
    because for some reason, the smarty output gets added to the csv */
$is_csv=false;
if(isset($_POST['buttonAction'])) {
  $buttonaction=$_POST['buttonAction'];
  if($buttonaction == 'AddressLookup') {
    $is_csv=true;
    //echo 'address text: '.$_POST['AddressText'].'<br>';
    $namelist=str_replace("\r","",$_POST['AddressText']);
    $namelist=str_replace("\t"," ",$namelist);
    //if($name=strtok($_POST['AddressText'],PHP_EOL)) {
    if($name=strtok($namelist,"\n")) {
      $csv_list=array();
      while($name) {
        //echo "name: $name";
        $query="select c.first_name,c.primary_name,d.degree,h.salutation,".
          "h.mailname,a.street_address_1,a.street_address_2,a.city,".
          "a.state,a.postal_code,a.country from contacts c ".
          "inner join household_members hm on hm.contact_id=c.contact_id ". "left join households h on h.household_id=hm.household_id ".
          "left join addresses a on a.address_id=h.address_id ".
          "left join degrees d on d.degree_id=c.degree_id where ";
        $st=explode(' ',strtolower($msi->escape_string($name)));
        $is_first=true;
        foreach($st as $wx) {
          if(!$is_first) $query.=' && ';
          $wx="'".$wx."%'";
          $query.="(lower(c.first_name) like $wx || ".
          "lower(c.middle_name) like $wx || ".
          "lower(c.nickname) like $wx || ".
          "lower(c.primary_name) like $wx || ".
          "lower (d.degree) like $wx)";
          $is_first=false;
        }
        //echo "query: $query\n\n";
        if(!$result=$msi->query($query)) {
          echo 'query error: '.$msi->error.'<br>';
          $ErrMsg=buildErrorMessage($ErrMsg,
            'unable to execute look up query'.$msi->error);
          goto sqlerror;
        }
        if($result->num_rows) {
          while($cx=$result->fetch_assoc()) {
            $csv_list[]=$cx;
          }
        }
        else {
          $csv_list[]['first_name']="$name not found";
        }
        $result->free();
        $name=strtok(PHP_EOL);
      }
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      foreach($csv_list as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
    }
    else {
      displayFooter($smarty,'List is empty');
    }
  }    // buttonAction = AddressLookup
}      //isset buttonAction

sqlerror:
if(!$is_csv) {
  // for some reason, utility.tpl gets added to csv output
  $smarty->display('utility.tpl');
}
?>
