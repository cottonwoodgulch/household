<?php

require_once 'vendor/autoload.php';

/* PHP requires setting a timezone. This will be fine,
   since the app doesn't require a time */
date_default_timezone_set('America/New_York');

$smarty = new Smarty();
$smarty->addTemplateDir(__DIR__ . '/templates');
$smarty->addPluginsDir(__DIR__ . '/plugins');
// $smarty->addPluginsDir(__DIR__ . '/plugins');
session_start();
if(isset($_SESSION['user_id'])) {
  $user_id=$_SESSION['user_id'];
  $smarty->assign('user_id',$user_id);
  $smarty->assign('HelloName',$_SESSION['HelloName']);
}
else {
  $user_id=0;
  if(!isset($login)) {
    header("Location: login.php");
  }
}

// database connection
include 'config.php';
$msi = new mysqli($db_host, $db_user, $db_pw, $db_db);

/* Set permissions.
   Viewer can see summary, Editor can see Details and Donations pages
*/
$rbac = new PhpRbac\Rbac();
/* not needed at this point - financial viewer can only see summary
$is_financial_viewer=
  $rbac->Users->hasRole('Financial Information Viewer',$user_id);
$is_contact_editor=
   $rbac->Users->hasRole('Financial Information Editor',$user_id);*/

$sitemenu=array();
if($rbac->Users->hasRole('Financial Information Editor',$user_id)) {
  $sitemenu[]=array('d' => 'Summary','t' => 'summary', 'c' => 0);
  $sitemenu[]=array('d' => 'Details', 't' => 'details', 'c' => 0);
  $sitemenu[]=array('d' => 'Donations', 't' => 'donations', 'c' => 0);
  foreach($sitemenu as &$sm) {
    if (stripos(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH), $sm['t']))
    {
      $sm['c']=1;
    }
    else {
      $sm['c']=0;
    }
  }
  unset($sm);
}
$smarty->assign('sitemenu',$sitemenu);

function getHouseholdFromContact($msi,$smarty,$cid) {
  /* also gets contact name in case of error */
  if($stmt=$msi->prepare(
       "select h.household_id, c.first_name, c.primary_name
          from contacts c
          left join household_members hm
            on hm.contact_id=c.contact_id
          left join households h
            on h.household_id=hm.household_id
         where c.contact_id=?")) {
    $stmt->bind_param('i',$cid);
    $stmt->execute();
    $result=$stmt->get_result();
    $hx = $result->fetch_assoc();
    $result->free();
    $stmt->close();
  }
  else {
    $smarty->assign('footer',"getHouseholdFromContact: unable to create mysql statement object: ".$msi->error);
    return FALSE;
  }
  if(is_null($hx['household_id'])) {
    $smarty->assign('footer',
      "No household for $cid ".$hx['first_name'].' '.$hx['primary_name']);
    return FALSE;
  }
  return $hx['household_id'];
}

function isDupeHousehold($msi,$house_name,$notID=0) {
      // check if name is in use other than for household_id notID
  if($stmt=$msi->prepare("select 0 from households h
       where h.name=? and h.household_id!=?")) {
    $stmt->bind_param('si',$house_name,$notID);
    if($stmt->execute()) {
      $result=$stmt->get_result();
      $stmt->close;
      $retval=$result->num_rows;
      $result->free;
      if($retval) {
        return true;
      }
      else {
        return false;
      }
    }
    else {
      $this->ErrMsg=buildErrorMessage($this->ErrMsg,
         'Could not execute dupe house query: '.$msi->error);
    }
  }
  else {
    $this->ErrMsg=buildErrorMessage($this->ErrMsg,
      'Could not prep dupe house query: '.$msi->error);
  }
}
  
function buildErrorMessage($errmsg,$newerr) {
  return $errmsg . strlen($errmsg) ? '<br />' : '' . $newerr;
}

function displayFooter($smarty,$err_msg) {
  /* footer will display if the smarty variable footer is set */
  if(strlen(trim($err_msg)) > 0) {
    $msg='<table><tr>
      <td class="footermsg">'.$err_msg.
      '</td><td class="footermsg"><button type="button" '.
      'onClick="hideFooter();">Close</button></td></tr></table>';
    $smarty->assign('footer',$msg);
  }
  
}function append_wc(&$list,$element) {
  /* add comma if needed, then element */
  $list .= (strlen($list) ? ',' : '') . $element;
}

function trek_list($msi,$contact_id,&$ErrMsg) {
  if($treksresult=$msi->query(
     "select r.year,g.short_name,ifnull(ro.role,'') role
        from roster_memberships rm
        left join rosters r on r.roster_id=rm.roster_id
        left join groups g on g.group_id=r.group_id
        left join roles ro on ro.role_id=rm.role_id
       where rm.contact_id=$contact_id
         and g.group_id not in (61,77,86,58,11,25,54,62,53,55,57)
       order by g.short_name, r.year")) {
    //echo "trek count: ".$treksresult->num_rows."\n";
    if(!$trx = $treksresult->fetch_assoc())return '';
    $prev_group=$trx['short_name'];
    $prev_role=$trx['role'];
    $prev_year=$trx['year'];
    $first_year=$prev_year;
    $element='';
    $le = array();
    do {
      $last_rec=is_null($trx = $treksresult->fetch_assoc());
      $year=$trx['year'];
      $group=$trx['short_name'];
      $role=$trx['role'];
      //echo "$year $group $role $first_year $prev_year $prev_group\n";
      if($group == $prev_group && $role == $prev_role) {
        if($year != ($prev_year+1)) {
          append_wc($element,$first_year.'-'.substr($prev_year,2,2));
          $first_year=$year;
        }
      }
      else {
        // different trek, write previous one
        if($prev_year == $first_year) {
          append_wc($element,$prev_year);
        }
        else {
          if($first_year) {
            append_wc($element,$first_year.'-'.substr($prev_year,2,2));
          }
          else {
            append_wc($element,$prev_year);
          }
        }
        $element .= " $prev_group";
        if($prev_role != '')$element .= " $prev_role";
        $le[]=$element;
        $element='';
        $first_year=$year;
      }
      $prev_year=$year;
      $prev_group=$group;
      $prev_role=$role;
    } while(!$last_rec);
  }
  else {
    // query error
    $ErrMsg=buildErrorMessage($ErrMsg,'trek_list query error: '.$msi->error);
  }
  sort($le);
  foreach($le as $lx) append_wc($element,$lx);
  return $element;
}

?>
