<?php
/* utility.php
   - Address Lookup */

require_once 'libe.php';
if(!$rbac->Users->hasRole('Contact Information Viewer',
     $_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
$ErrMsg=array();
require_once 'merge.php';

/* if output is to a csv file, don't re-display the page,
    because for some reason, the smarty output gets added to the csv */
$is_csv=false;
if(isset($_POST['buttonAction'])) {
  //echo 'button action: '.$_POST['buttonAction'].'<br>';
  $buttonaction=$_POST['buttonAction'];
  if($buttonaction == 'AddressLookup') {
    $is_csv=true;
    //echo 'address text: '.$_POST['InputText'].'<br>';
    $namelist=str_replace("\r","",$_POST['InputText']);
    $namelist=str_replace("\t"," ",$namelist);
    //if($name=strtok($_POST['AddressText'],PHP_EOL)) {
    if($name=strtok($namelist,"\n")) {
      $csv_list=array();
      while($name) {
        //echo "name: $name";
        $query="select c.first_name,c.primary_name,d.degree,h.salutation,".
          "h.mailname,h.household_id,a.street_address_1,a.street_address_2,".
          "a.city,a.state,a.postal_code,a.country from contacts c ".
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
          buildErrorMessage($ErrMsg,
            'unable to execute look up query',$msi->error);
          goto sqlerror;
        }
        if($result->num_rows) {
          while($cx=$result->fetch_assoc()) {
            $cx['emails']=email_list($msi,$cx['household_id'],$ErrMsg);
            $cx['phones']=phone_list($msi,$cx['household_id'],$ErrMsg);
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
  else if($buttonaction == 'Contributions') {
    if(isset($_POST['InputText'])) {
      $dontext=str_replace("\r","",$_POST['InputText']);
      $lines=explode("\n",$dontext);
      foreach($lines as $lx) {
        /* if there is an extra newline at the end, skip it */
        if(!strlen($lx))break;
        $fields=explode("\t",$lx);
        getdates($fields[1],$fy,$ddate);
        $ddata=array(
           'recno' => $fields[0],
           'amount' =>
              str_replace(',','',str_replace('$','',$fields[3])),
           'last' => $fields[4],
           'first' => $fields[5],
           'salutation' => $fields[6],
           'fund' => $fields[8],
           'donornote' => $fields[11],
           'dedication' => $fields[12],
           'addr' => $fields[23],
           'city' => $fields[24],
           'state' => $fields[25],
           'zip' => $fields[26],
           'email' => $fields[27],
           'contnote' => $fields[28]
        );
        if(!$result=$msi->query('select di_id from donationimport '.
           "where fy='$fy' and recno=".$ddata['recno'])) {
            buildErrorMessage($ErrMsg,
              'donationimport dupe check',$msi->error);
            goto sqlerror;
        }
        if($result->num_rows > 0) {
          /* delete and replace if this one is already there */
          $rx=$result->fetch_assoc();
          $old_di_id=$rx['di_id'];
          if(!$msi->query(
             "delete from donationimport where di_id=$old_di_id")) {
            buildErrorMessage($ErrMsg,
              'donationimport delete previous version',$msi->error);
            goto sqlerror;
          }
          if(!$msi->query(
            "delete from di_names where di_id=$old_di_id")) {
            buildErrorMessage($ErrMsg,
              'di_names delete previous version',$msi->error);
            goto sqlerror;
          }
          if(!$msi->query(
            "delete from di_phones where di_id=$old_di_id")) {
            buildErrorMessage($ErrMsg,
              'di_phones delete previous version',$msi->error);
            goto sqlerror;
          }
        }
        $result->free();
        // table has country between zip and email - not used here
        if(!$stmt=$msi->prepare('insert into donationimport '.
           "values(null,0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'',?,?)")) {
            buildErrorMessage($ErrMsg,'donationimport prep',$msi->error);
          goto sqlerror;
        }
        // table has country between zip and email
        if(!$stmt->bind_param('sisdssssssssssss',$fy,
           $ddata['recno'],$ddate,$ddata['amount'],
           $ddata['last'],$ddata['first'],$ddata['salutation'],   
           $ddata['fund'],$ddata['donornote'],
           $ddata['dedication'],$ddata['addr'],
           $ddata['city'],$ddata['state'],$ddata['zip'],
           $ddata['email'],$ddata['contnote'])) {
          buildErrorMessage($ErrMsg,'donationimport bind',$msi->error);
          goto sqlerror;
        }
        if(!$stmt->execute()) {
          buildErrorMessage($ErrMsg,'donationimport exec',$msi->error);
          goto sqlerror;
        }
        // associate this donationimport rec with member names
        $di_id=$msi->insert_id;
        getnames($msi,$ddata['first'],$ddata['last'],$di_id);
        // try to find phone numbers in the note field
        getphones($msi,$ddata['contnote'],$di_id);
        //getemails($msi,$ddata['email'],$di_id);
      }
    }
  }   // buttonAction = Contributions
  else if($buttonaction == 'Merge') {
    $fromcid=$_POST['MergeFrom'];
    $tocid=$_POST['MergeTo'];
    mergeCoordinate($msi,$fromcid,$tocid,'addresses',
       'address_associations','address_id',$ErrMsg);
    mergeCoordinate($msi,$fromcid,$tocid,'phones',
       'phone_associations','phone_id',$ErrMsg);
    mergeCoordinate($msi,$fromcid,$tocid,'emails',
       'email_associations','email_id',$ErrMsg);
    mergeRelation($msi,$fromcid,$tocid,$ErrMsg);
    mergeItem($msi,$fromcid,$tocid,'contact_id','notes','note_id',$ErrMsg);
    mergeItem($msi,$fromcid,$tocid,'contact_id','roster_memberships',
       'roster_membership_id',$ErrMsg);
    mergeItem($msi,$fromcid,$tocid,'primary_donor_id','hdonations',
       'donation_id',$ErrMsg);
  }
  else if($buttonaction == 'RedRocks') {
    $is_csv=true;
    /* this uses household address & preferred_emails */
    if(!$result=$msi->query(
       "select h.mailname,c.first_name,c.primary_name,d.degree,e.email,".
       "a.street_address_1,a.street_address_2,a.city,a.state,".
       "a.postal_code,a.country from contacts c ".
       "left join degrees d on d.degree_id=c.degree_id ".
       "left join household_members hm ".
       "on hm.contact_id=c.contact_id ".
       "left join households h on h.household_id=hm.household_id ".
       "left join addresses a on a.address_id=h.address_id ".
       "left join (select cx.contact_id,pe.email_id ".
         "from contacts cx inner join email_associations ea ".
         "on ea.contact_id=cx.contact_id ".
         "inner join preferred_emails pe ".
         "on pe.email_id=ea.email_id) pex ".
         "on pex.contact_id=c.contact_id ".
       "left join emails e on e.email_id=pex.email_id ".
       "where c.redrocks order by 2")) {
      buildErrorMessage($ErrMsg,'RedRocks query',$msi->error);
      goto sqlerror;
    }
    else {
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      foreach($result as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
      $result->free();
    }
  }
  else if($buttonaction == 'Donors') {
    $is_csv=true;
    //echo 'DDate: '.$_POST['DDate'].'<br>';
    $d1end=new DateTime($_POST['DDate']);
    $d1end->sub(new DateInterval('P4Y'));
    $d1beg=new DateTime($_POST['DDate']);
    $d1beg->sub(new DateInterval('P5Y'))->
       add(new DateInterval('P1D'));
    $bdate="'".$d1beg->format('Y-m-d')."'";
    $edate="'".$d1end->format('Y-m-d')."'";
    if(!$result=$msi->query(
    "select h.mailname,".
       "ifnull(hd1.total,0) d1, ifnull(hd2.total,0) d2,".
       "ifnull(hd3.total,0) d3, ifnull(hd4.total,0) d4,".
       "hd5.total d5,".
       "if(ifnull(hd1.total,0)>0 && ifnull(hd2.total,0)>0 && ".
       "ifnull(hd3.total,0)>0 && ifnull(hd4.total,0)>0,'*','') ".
       "elmorro,".
       "case hd5.ncat when 1 then 'Cottonwood' ".
          "when 2 then 'Ponderosa' when 3 then 'Douglas Fir' ".
          "when 4 then 'Piñon' when 5 then 'Juniper' ".
          "else 'Aspen' end dcat ".
  "from households h ".
 "inner join (select distinct hmd.household_id ".
               "from household_members hmd ".
              "inner join contacts cd ".
                 "on cd.contact_id=hmd.contact_id ".
              "where cd.deceased=0) hdx ".
       "on hdx.household_id=h.household_id ".
  "left join (select hm0.household_id,".
     "floor(sum(d0.amount)) total ".
               "from household_members hm0 ".
              "inner join hdonations d0 ".
                 "on d0.primary_donor_id=hm0.contact_id ".
              "where d0.ddate between $bdate and $edate".
              "group by 1) hd1 ".
       "on hd1.household_id=h.household_id ".
  "left join (select hm1.household_id,".
     "floor(sum(d1.amount)) total ".
               "from household_members hm1 ".
              "inner join hdonations d1 ".
                 "on d1.primary_donor_id=hm1.contact_id ".
              "where d1.ddate between ".
              "$bdate + INTERVAL 1 YEAR and".
              "$edate + INTERVAL 1 YEAR ".
              "group by 1) hd2 ".
       "on hd2.household_id=h.household_id ".
  "left join (select hm1.household_id,".
     "floor(sum(d1.amount)) total ".
               "from household_members hm1 ".
              "inner join hdonations d1 ".
                 "on d1.primary_donor_id=hm1.contact_id ".
              "where d1.ddate between ".
              "$bdate + INTERVAL 2 YEAR and".
              "$edate + INTERVAL 2 YEAR ".
              "group by 1) hd3 ".
       "on hd3.household_id=h.household_id ".
  "left join (select hm1.household_id,".
     "floor(sum(d1.amount)) total ".
               "from household_members hm1 ".
              "inner join hdonations d1 ".
                 "on d1.primary_donor_id=hm1.contact_id ".
              "where d1.ddate between ".
              "$bdate + INTERVAL 3 YEAR and".
              "$edate + INTERVAL 3 YEAR ".
              "group by 1) hd4 ".
       "on hd4.household_id=h.household_id ".
 "inner join (select hm1.household_id,".
     "floor(sum(d1.amount)) total,".
     "case when sum(d1.amount)>=5000 then 1 ".
          "when sum(d1.amount)>=2500 then 2 ".
          "when sum(d1.amount)>=1000 then 3 ".
          "when sum(d1.amount)>=500 then 4 ".
          "when sum(d1.amount)>=100 then 5 ".
          "else 6 end ncat ".
               "from household_members hm1 ".
              "inner join hdonations d1 ".
                 "on d1.primary_donor_id=hm1.contact_id ".
              "where d1.ddate between ".
              "$bdate + INTERVAL 4 YEAR and".
              "$edate + INTERVAL 4 YEAR ".
              "group by 1) hd5 ".
       "on hd5.household_id=h.household_id ".
 "order by ncat,h.name")) {
      buildErrorMessage($ErrMsg,'Donor List query',$msi->error);
      goto sqlerror;
    }
    else {
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      fputcsv($output,array("Mail Name",
         "C-4","C-3","C-2","C-1","Current",
         "El Morro","Category"),"\t");
      foreach($result as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
      $result->free();
    }
  } // buttonAction == Donors
  else if($buttonaction == 'LYBUNT') {
    $is_csv=true;
    /* if household gave in any of the last 5 years except the current, which is
     *    the period since the most recent FY-end date */
    //echo 'DDate: '.$_POST['DDate'].'<br>';

    $asofdate=new DateTime($_POST['AppealDate']);
    $PrevYE=new DateTime($asofdate->format('Y').'-09-30');
/*
    echo "asofdate: ".$asofdate->format('Y-m-d')."\t";
    echo "PrevYE: ".$PrevYE->format('Y-m-d')."\t";
*/
    if($asofdate<=$PrevYE) {
      //echo "asofdate <= cutoff\t";
      $PrevYE->sub(new DateInterval('P1Y'));
    }
    $PrevYB=new DateTime($PrevYE->format('Y-m-d'));
    $PrevYB->add(new DateInterval('P1D'));
    //echo "PrevYE: ".$PrevYE->format('Y-m-d')."<br>";
    //echo "PrevYB: ".$PrevYB->format('Y-m-d')."<br>";
    $asofdate="'".$asofdate->format('Y-m-d')."'";
    $PrevYE="'".$PrevYE->format('Y-m-d')."'";
    $PrevYB="'".$PrevYB->format('Y-m-d')."'";
    /* get header line */
    if(!$result=$msi->query(
      "select 'Mail Name',$PrevYE - INTERVAL 3 YEAR c4, $PrevYE - INTERVAL 2 YEAR c3,".
         "$PrevYE - INTERVAL 1 YEAR c2, $PrevYE c1, $asofdate c0, 'Donation','Last Name',
         'Salutation','Address 1','Address 2','City','State','Zip','Country'")) {
      echo 'LYBUNT header error: '.$msi->error.'<br>';
      buildErrorMessage($ErrMsg,'LYBUNT header query',$msi->error);
      goto sqlerror;
    }
    else {
      $rheader1=array('','Year Ending','','','','','Latest');
      $rheader2=$result->fetch_assoc();
      $result->free;
    }
    if(!$result=$msi->query(
    'select h.mailname,ifnull(hd1.total,0) d1, ifnull(hd2.total,0) d2,'.
       'ifnull(hd3.total,0) d3,ifnull(hd4.total,0) d4, ifnull(hd5.total,0) d5,'.
       'rl.latest,rl.last,h.salutation,a.street_address_1 address1,'.
      "ifnull(a.street_address_2,'') address2,".
      'a.city,a.state,a.postal_code,a.country '.
  'from households h '.
 'inner join (select distinct hmd.household_id '.
               'from household_members hmd '.
              'inner join contacts cd '.
                 'on cd.contact_id=hmd.contact_id '.
              'where cd.deceased=0 and cd.contact_type_id=1) hdx '.
       'on hdx.household_id=h.household_id '.
  'left join (select hm0.household_id, '.
     'floor(sum(d0.amount)) total '.
               'from household_members hm0 '.
              'inner join hdonations d0 '.
                 'on d0.primary_donor_id=hm0.contact_id '.
              "where d0.ddate between $PrevYB - INTERVAL 4 YEAR ".
                 "and $PrevYE - INTERVAL 3 YEAR ".
              'group by 1) hd1 '.
       'on hd1.household_id=h.household_id '.
  'left join (select hm1.household_id,floor(sum(d1.amount)) total '.
               'from household_members hm1 '.
              'inner join hdonations d1 '.
                 'on d1.primary_donor_id=hm1.contact_id '.
              'where d1.ddate between '.
              "$PrevYE - INTERVAL 3 YEAR and $PrevYE - INTERVAL 2 YEAR ".
              'group by 1) hd2 '.
       'on hd2.household_id=h.household_id '.
  'left join (select hm1.household_id, '.
     'floor(sum(d1.amount)) total '.
               'from household_members hm1 '.
              'inner join hdonations d1 '.
                 'on d1.primary_donor_id=hm1.contact_id '.
              'where d1.ddate between '.
              "$PrevYE - INTERVAL 2 YEAR and $PrevYE - INTERVAL 1 YEAR ".
              'group by 1) hd3 '.
       'on hd3.household_id=h.household_id '.
  'left join (select hm1.household_id,floor(sum(d1.amount)) total '.
               'from household_members hm1 '.
              'inner join hdonations d1 '.
                 'on d1.primary_donor_id=hm1.contact_id '.
              'where d1.ddate between '.
              "$PrevYE - INTERVAL 1 YEAR and $PrevYE ".
              'group by 1) hd4 '.
       'on hd4.household_id=h.household_id '.
  'left join (select hm1.household_id,floor(sum(d1.amount)) total '.
               'from household_members hm1 '.
              'inner join hdonations d1 '.
                 'on d1.primary_donor_id=hm1.contact_id '.
              "where d1.ddate between $PrevYE and $asofdate ".
              'group by 1) hd5 '.
       'on hd5.household_id=h.household_id '.
  'left join (select h.household_id, '.
                    "concat(date_format(rdx.maxdate,'%m/%d/%y'),' ',".
                    'format(rx.amount,0)) latest,'.
                    'rdx.primary_name last '.
               'from households h '.
               'left join (select rhm.household_id,sum(rhd.amount) amount,rhd.ddate '.
                            'from household_members rhm '.
              'inner join hdonations rhd '.
                'on rhd.primary_donor_id=rhm.contact_id '.
              'group by rhm.household_id,rhd.ddate) rx '.
                 'on rx.household_id=h.household_id '.
              'inner join (select rhm.household_id,max(rhd.ddate) maxdate,'.
                  'cx.primary_name '.
               'from household_members rhm '.
              'inner join hdonations rhd '.
                'on rhd.primary_donor_id=rhm.contact_id '.
              'left join contacts cx '.
                'on cx.contact_id=rhm.contact_id '.
              'group by rhm.household_id) rdx '.
       'on rx.household_id=rdx.household_id '.
      'and rx.ddate=rdx.maxdate) rl '.
    'on rl.household_id=h.household_id '.
  'left join addresses a on a.address_id=h.address_id '.
 'where (ifnull(hd1.total,0)>0 or ifnull(hd2.total,0)>0 or ifnull(hd3.total,0)>0 '.
        'or ifnull(hd4.total,0)>0) and ifnull(hd5.total,0)<=0 '.
 'order by rl.last')) {
      echo 'error: '.$msi->error.'<br>';
      buildErrorMessage($ErrMsg,'LYBUNT query',$msi->error);
      goto sqlerror;
    }
    else {
      $rx=$result->fetch_assoc();
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      fputcsv($output,$rheader1,"\t");
      fputcsv($output,$rheader2,"\t");
      /*fputcsv($output,array("Mail Name",
         "C-4","C-3","C-2","C-1","Current","Latest"),"\t");*/
      foreach($result as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
      $result->free();
    }
  } // buttonAction == LYBUNT
  else if($buttonaction == 'Current') {
    $is_csv=true;
    /* if household gave since the most recent FY-end date */
    //echo 'DDate: '.$_POST['DDate'].'<br>';

    $asofdate=new DateTime($_POST['AppealDate']);
    $PrevYE=new DateTime($asofdate->format('Y').'-09-30');
/*
    echo "asofdate: ".$asofdate->format('Y-m-d')."\t";
    echo "PrevYE: ".$PrevYE->format('Y-m-d')."\t";
*/
    if($asofdate<=$PrevYE) {
      //echo "asofdate <= cutoff\t";
      $PrevYE->sub(new DateInterval('P1Y'));
    }
    $asofdate=$asofdate->format('Y-m-d');
    $PrevYE=$PrevYE->format('Y-m-d');
    /* get header lines */
    $rheader1=array('As of',"$asofdate",'Donated since',"$PrevYE");
    $rheader2=array('Mail Name','Salutation',
         'Address 1','Address 2','City','State','Zip','Country');
    $asofdate="'".$asofdate."'";
    $PrevYE="'".$PrevYE."'";

    if(!$result=$msi->query('select h.mailname, h.salutation,'.
      "a.street_address_1 address1,ifnull(a.street_address_2,'') address2,".
      'a.city,a.state,a.postal_code,a.country '.
      'from households h '.
     'inner join (select distinct hmd.household_id '.
                  'from household_members hmd '.
                 'inner join contacts cd on cd.contact_id=hmd.contact_id '.
                 'where cd.deceased=0 and cd.contact_type_id=1) hdx '.
        'on hdx.household_id=h.household_id '.
      'left join (select hm1.household_id,sum(d1.amount) total '.
                   'from household_members hm1 '.
                  'inner join hdonations d1 on d1.primary_donor_id=hm1.contact_id '.
                  "where d1.ddate between $PrevYE and $asofdate ".
                  'group by 1) hdd '.
        'on hdd.household_id=h.household_id '.
      'left join addresses a on a.address_id=h.address_id '.
     'where ifnull(hdd.total,0)>0')) {
      echo 'error: '.$msi->error.'<br>';
      buildErrorMessage($ErrMsg,'NON-LYBUNT query',$msi->error);
      goto sqlerror;
    }
    else {
      $rx=$result->fetch_assoc();
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      fputcsv($output,$rheader1,"\t");
      fputcsv($output,$rheader2,"\t");
      /*fputcsv($output,array("Mail Name",
         "C-4","C-3","C-2","C-1","Current","Latest"),"\t");*/
      foreach($result as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
      $result->free();
    }
  } // buttonAction == Current

  else if($buttonaction == 'Non-Donor') {
    $is_csv=true;
    /* if household has not given since previous FY end - 4 */
    //echo 'DDate: '.$_POST['DDate'].'<br>';
    $asofdate=new DateTime($_POST['AppealDate']);
    $BeginDate=new DateTime($asofdate->format('Y').'-09-30');

    if($asofdate<=$BeginDate) {
      $BeginDate->sub(new DateInterval('P5Y'));
    }
    else {
      $BeginDate->sub(new DateInterval('P4Y'));
    }
    $BeginDate=$BeginDate->format('Y-m-d');
    /* get header lines */
    $rheader1=array('No Donations Since',$BeginDate);
    $rheader2=array('Mail Name','Salutation',
         'Address 1','Address 2','City','State','Zip','Country');
    $BeginDate="'".$BeginDate."'";

    if(!$result=$msi->query('select h.mailname, h.salutation,'.
      "a.street_address_1 address1,ifnull(a.street_address_2,'') address2,".
      'a.city,a.state,a.postal_code,a.country '.
      'from households h '.
     'inner join (select distinct hmd.household_id '.
                  'from household_members hmd '.
                 'inner join contacts cd on cd.contact_id=hmd.contact_id '.
                 'where cd.deceased=0 and cd.contact_type_id=1) hdx '.
        'on hdx.household_id=h.household_id '.
      'left join (select hm1.household_id,sum(d1.amount) total '.
                   'from household_members hm1 '.
                  'inner join hdonations d1 on d1.primary_donor_id=hm1.contact_id '.
                  "where d1.ddate >$BeginDate ".
                  'group by 1) hdd '.
        'on hdd.household_id=h.household_id '.
      'inner join addresses a on a.address_id=h.address_id '.
     'where hdd.total is null')) {
      echo 'error: '.$msi->error.'<br>';
      buildErrorMessage($ErrMsg,'NON-LYBUNT query',$msi->error);
      goto sqlerror;
    }
    else {
      $rx=$result->fetch_assoc();
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      fputcsv($output,$rheader1,"\t");
      fputcsv($output,$rheader2,"\t");
      /*fputcsv($output,array("Mail Name",
         "C-4","C-3","C-2","C-1","Current","Latest"),"\t");*/
      foreach($result as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
      $result->free();
    }
  } // buttonAction == Non-Donor

  else if($buttonaction == 'MailList') {
    $is_csv=true;
    if(!$result=$msi->query('select h.mailname, h.salutation,'.
      'a.street_address_1 address1,'.
      "ifnull(a.street_address_2,'') address2,".
      'a.city,a.state,a.postal_code,a.country '.
      'from households h '.
      'inner join (select household_id from household_members hmd '.
      'inner join contacts cd on cd.contact_id=hmd.contact_id '.
      'where cd.deceased=0 and cd.contact_type_id=1 '.
      'group by hmd.household_id having count(*) > 0) hnd '.
      'on hnd.household_id=h.household_id '.
      'inner join addresses a on a.address_id=h.address_id '.
      'where a.address_type_id in (1,3,5) '.
      'and h.address_id in '.
      '(select distinct address_id from address_associations) '.
      'order by 8,7')) {
      buildErrorMessage($ErrMsg,'Mail List query',$msi->error);
      goto sqlerror;
    }
    else {
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment;');
      $output = fopen('php://output', 'w');
      fputcsv($output,array("Mail Name","Salutation","A1","A2",      
         "City","State","Zip","Country",),"\t");
      foreach($result as $cx)fputcsv($output, $cx, "\t");
      fclose($output);
      $result->free();
    }
  } // buttonAction == MailList
}      //isset buttonAction

sqlerror:
if(!$is_csv) {
  // for some reason, utility.tpl gets added to csv output
  /* default as-of date for donations 
     input type=date defaults to now, but doesn't display it */
  displayFooter($smarty,$ErrMsg);
  $defdate=new DateTime();
  $smarty->assign('DefaultDate',$defdate->format('Y-m-d'));
  $smarty->display('utility.tpl');
}

function getdates($fdate,&$fy,&$ddate) {
  /* get fiscal year and SQL-formatted date (yyyy-mm-dd */
  $dt=new DateTime($fdate);
  $month=$dt->format('m');
  $year=$dt->format('Y');
  $ddate=$year.'-'.$month.'-'.$dt->format('d');
  if($month>'09')$dt->add(new DateInterval('P1Y'));
  $fy=$dt->format('y');
}
function getnames($msi,$first,$last,$di_id) {
  $names=array();
  if($t=strpos($first,' and ')) {
    $names[]=array('first' => substr($first,0,$t),
                   'last' => $last);
    $names[]=array('first' => substr($first,$t+5),
                   'last' => $last);    
  }
  else if($t=strpos($last,' and ')) {
    $u=strpos($last,' ',$t+6)+1;
    $names[]=array('first' => $first,
                   'last' => substr($last,0,strpos($last,' ')));
    $names[]=array('first' => substr($last,$t+5,$u-$t-6),
                   'last' => substr($last,$u));
  }
  else {
    $names[]=array('first' => $first, 'last' => $last);
  }
  foreach($names as &$nx) {
    if(!$result=$msi->query(
       'select ifnull(c.contact_id,0) contact_id,'.
      'ifnull(h.household_id,0) household_id from contacts c '.
      'left join household_members hm on hm.contact_id=c.contact_id '.
      'left join households h on h.household_id=hm.household_id '.
       "where (c.first_name='".$nx['first']."' || ". 
       "nickname='".$nx['first']."') ".
       "and primary_name='".$nx['last']."'")) {
      buildErrorMessage($ErrMsg,'getnames query',$msi->error);
    }
    else {
      if($result->num_rows) {
        $rx=$result->fetch_assoc();
        $nx['contact_id']=$rx['contact_id'];
        $nx['household_id']=$rx['household_id'];
        $result->free();
        if(!$msi->query("insert into di_names values($di_id,".
           $nx['contact_id'].','.$nx['household_id'].')')) {
          buildErrorMessage($ErrMsg,'di_names insert',$msi->error);
          /*echo 'query: '."insert into di_names values($di_id,".
           $nx['contact_id'].','.$nx['household_id'].')'.'<br>';*/
        }
      }
    }
  }
}
function getphones($msi,$contnote,$di_id) {
  /* try to find phone numbers in the note field */
  $off=0;
  $m=array();
  while(preg_match('/[0-9]{3}-[0-9]{3}-[0-9]{4}/',
     $contnote,$m,PREG_OFFSET_CAPTURE,$off) ===1) {
    if(!$msi->query("insert into di_phones values ($di_id,".
      "'".$m[0][0]."')")) {
      buildErrorMessage($ErrMsg,'di_phones insert',$msi->error);
    }
    //$phones[]= ($is_first ? '' : ' ').$m[0][0];
    $off=$m[0][1]+1;
  }
}
?>




