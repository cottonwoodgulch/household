<?php
/* summary.php */
  class DonationStat {
  /* donation statistics for a household */
  public $ddate;
  public $label;
  public $amount;
  public $html='';
    function __construct($ddate,$label,$amt) {
      $this->ddate=$ddate;
      $this->label=$label;
      $this->amount=$amt;
    }
    function display() {
      return array('label' => $this->label,
         'amount' => number_format($this->amount),
         'ddate' => $this->ddate);
    }
  }

require_once 'libe.php';
if(!$rbac->Users->hasRole('Financial Information Viewer',$_SESSION['user_id'])) {
  header("Location: NotAuthorized.html");
}
$ErrMsg=array();
require_once 'objects.php';

if(isset($_GET['cid'])) {
  // contact id sent in from gulchdbi
  $hid = getHouseholdFromContact($msi,$smarty,$_GET['cid']);
}
else if(isset($_POST['TargetHouseID'])) {
  $hid=$_POST['TargetHouseID'];
}
else if(isset($_SESSION['household_id'])) {
  $hid=$_SESSION['household_id'];
}
$_SESSION['household_id'] = $hid;

if($hid) {
  $tx=new HouseData($msi,$smarty,$hid,$ErrMsg);  

  /* calculate donation statistics from $tx->donations array */
  $yr10=new DateTime();
  $yr10->sub(new DateInterval('P10Y'));
  $yr5=new DateTime();
  $yr5->sub(new DateInterval('P5Y'));
  $yr4=new DateTime();
  $yr4->sub(new DateInterval('P4Y'));
  $yr3=new DateTime();
  $yr3->sub(new DateInterval('P3Y'));
  $yr2=new DateTime();
  $yr2->sub(new DateInterval('P2Y'));
  $yr1=new DateTime();
  $yr1->sub(new DateInterval('P1Y'));
  $amt5=new DonationStat('','Last 5 years',0);
  $amt5_10=new DonationStat('','5-10 years',0);
  $largest=new DonationStat('','Largest',0);
  $total=new DonationStat('','Total',0);
  $elmorro1=false;
  $elmorro2=false;
  $elmorro3=false;
  $elmorro4=false;
  $elmorro5=false;

  $Latest=new DonationStat($tx->donations[0]['ddate'],
     'Latest',$tx->donations[0]['amount']);
  foreach($tx->donations as $dx) {
    $ddate=new DateTime($dx['ddate']);
    if($ddate > $yr10) {
      if($ddate > $yr5) {
        $amt5->amount+=$dx['amount'];
        if($ddate > $yr1) {
          $elmorro1=true;
        }
        else if($ddate > $yr2) {
          $elmorro2=true;
        }
        else if($ddate > $yr3) {
          $elmorro3=true;
        }
        else if($ddate > $yr4) {
          $elmorro4=true;
        }
        else {
          $elmorro5=true;
        }
      }
      else {
        $amt5_10->amount+=$dx['amount'];
      }
    }
    if($dx['amount'] > $largest->amount) {
      $largest->amount=$dx['amount'];
      $largest->ddate=$dx['ddate'];
    }
    $total->amount+=$dx['amount'];
    /*echo 'ddate, DT: '.$tx['ddate'].' '.$ddate->format('m/d/Y');
    echo ' amt: '.$tx['amount']."\n";*/
  }
  $stats=array();
  $stats[]=$amt5->display();
  $stats[]=$amt5_10->display();
  $stats[]=$largest->display();
  $stats[]=$Latest->display();
  $stats[]=$total->display();
  if($elmorro1 && $elmorro2 && $elmorro3 && $elmorro4 && $elmorro5) {
    $stats[]=array('label'=>'El Morro Member');
  }
  $smarty->assign('stats',$stats);


  $smarty->assign('house',$tx);
  $smarty->assign('address',$tx->getPreferredAddress());
}
/* if $hid is not set, only the Look up Household button will show */
$smarty->assign('referrer','home');
displayFooter($smarty,$ErrMsg);
$smarty->display('summary.tpl');
?>
