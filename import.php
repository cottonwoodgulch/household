<?php
/* utility.php
   - Address Lookup */

require_once 'libe.php';
if(!$rbac->Users->hasRole('Contact Information Viewer',
     $_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
require_once 'objects.php';

$fy=isset($_POST['fy']) ? $_POST['fy'] : '';
$recno=isset($_POST['recno']) ? $_POST['recno'] : 0;
$hid=isset($_POST['hid']) ? $_POST['hid'] : 0;

if(isset($_POST['buttonAction'])) {
  //echo 'button action: '.$_POST['buttonAction'].'<br>';
  $buttonaction=$_POST['buttonAction'];
  if($buttonaction == 'Save') {
    // process update, set done=1
    // redisplay, $hid, $fy, $recno stay the same
    $qwhere="where fy='$fy' && recno=$recno";
  }
  else if($buttonaction == 'Next') {
    // don't need to do anything - query is for > recno
    $hid=0;
    $qwhere="where !done && fy>='$fy' && recno>$recno";
  }
  else if($buttonaction == "SwitchHouse") {
    /* same donation rec, different household */
    $qwhere="where fy='$fy' && recno=$recno";
  }
  else {
    // reload - get first not-done import rec
    $fy='';
    $recno=0;
    $hid=0;
    $qwhere="where !done";
    // query just !done
  }
}

//$qwhere="where !done && fy>='$fy' && recno>$recno";
//get household info from import
if(!$result=$msi->query(
   'select di_id,fy,recno,ddate,amount,lname,fname,salutation,'.
   'fund,donornote,dedication,street,city,state,zip,country,'.
   'emails,contactnote from donationimport '.$qwhere.       
   ' order by fy,recno limit 1')) {
  echo 'donationimport query error: '.$msi->error.'<br>';
  $ErrMsg=buildErrorMessage($ErrMsg,
     'unable to retrieve donationimport'.$msi->error);
  goto sqlerror;
}
if($result->num_rows) {
  $di=$result->fetch_assoc();
}
else {
  $di=array('di_id' => 0);
}
$result->free();

if(!$hid) {
  /* current - target household */
  if(!$result=$msi->query('select distinct n.household_id,'.
     'h.mailname from di_names n '.
     'left join household_members hm on hm.contact_id=n.contact_id '.
     'left join households h on h.household_id=hm.household_id '.
     'where n.di_id='.$di['di_id'])) {
    echo 'household query error: '.$msi->error.'<br>';
    $ErrMsg=buildErrorMessage($ErrMsg,
       'unable to retrieve households'.$msi->error);
    goto sqlerror;
  }
  $currh=array();
  while($tx=$result->fetch_assoc()) {
    $currh[]=$tx;
  }
  if(count($currh) > 0) {
    $hid=$currh[0]['household_id'];
    $smarty->assign('current',$currh);
  }
  else {
    $hid=0;
  }
} // if !$hid

if($hid) {
  /* if $hid from $_POST or by looking up member names */     
  $house=new HouseData($msi,$smarty,$hid);
  $smarty->assign('house',$house);
}

sqlerror:
$smarty->assign('di',$di);
$smarty->assign('hid',$hid);
$smarty->display('import.tpl');
?>
