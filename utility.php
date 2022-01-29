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
          echo 'query error: '.$msi->error.'<br>';
          $ErrMsg=buildErrorMessage($ErrMsg,
            'unable to execute look up query'.$msi->error);
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
            echo 'donationimport dupe check error: '.
               $msi->error.'<br>';
            goto sqlerror;
        }
        if($result->num_rows > 0) {
          /* delete and replace if this one is already there */
          $rx=$result->fetch_assoc();
          $old_di_id=$rx['di_id'];
          if(!$msi->query(
            "delete from donationimport where di_id=$old_di_id")) {
            echo 'donationimport delete previous version error: '.
               $msi->error.'<br>';
            goto sqlerror;
          }
          if(!$msi->query(
            "delete from di_names where di_id=$old_di_id")) {
            echo 'di_names delete previous version error: '.
               $msi->error.'<br>';
            goto sqlerror;
          }
          if(!$msi->query(
            "delete from di_phones where di_id=$old_di_id")) {
            echo 'di_phones delete previous version error: '.
               $msi->error.'<br>';
            goto sqlerror;
          }
        }
        $result->free();
        // table has country between zip and email - not used here
        if(!$stmt=$msi->prepare('insert into donationimport '.
           "values(null,0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'',?,?)")) {
          echo 'donationimport prep error: '.$msi->error.'<br>';
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
          echo
             'donationimport bind error: '.$msi->error.'<br>';
          goto sqlerror;
        }
        if(!$stmt->execute()) {
          echo
             'donationimport exec error: '.$msi->error.'<br>';
          goto sqlerror;
        }
        // associate this donationimport rec with member names
        $di_id=$msi->insert_id;
        getnames($msi,$ddata['first'],$ddata['last'],$di_id);
        // try to find phone numbers in the note field
        getphones($msi,$ddata['contnote'],$di_id);
      }
    }
  }   // buttonAction = Contributions
  else if($buttonaction == 'RedRocks') {
    $is_csv=true;
    /* this uses household address & preferred_emails */
    if(!$result=$msi->query(
       "select c.first_name,c.primary_name,d.degree,e.email,".
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
      echo 'RedRocks query error: '.$msi->error.'<br>';
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
    /*echo 'begin, end: '.$d1beg->format('m/d/Y').', '.
       $d1end->format('m/d/Y').'<br>';
    echo "begin, end: $bdate, $edate<br>"; */
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
      echo 'Donor List query error: '.$msi->error.'<br>';
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
}      //isset buttonAction

sqlerror:
if(!$is_csv) {
  // for some reason, utility.tpl gets added to csv output
  /* default as-of date for donations */
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
      echo 'getnames query error: '.$msi->error.'<br>';
    }
    else {
      if($result->num_rows) {
        $rx=$result->fetch_assoc();
        $nx['contact_id']=$rx['contact_id'];
        $nx['household_id']=$rx['household_id'];
        $result->free();
        if(!$msi->query("insert into di_names values($di_id,".
           $nx['contact_id'].','.$nx['household_id'].')')) {
          echo 'di_names insert error'.$msi->error.'<br>';
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
      echo 'di_phones insert error'.$msi->error;
    }
    //$phones[]= ($is_first ? '' : ' ').$m[0][0];
    $off=$m[0][1]+1;
  }
}
?>
