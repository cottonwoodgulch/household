<?php
function smarty_modifier_TrekList($tl) {
  if(strlen($tl) <= 35)return $tl;
  $retval='';
  $pos=0;
  $segbegin=0;
  $tlen=strlen($tl);
  while($segbegin < $tlen) {
    if(!$pos=strpos($tl,',',$segbegin+30)) break;
    $retval.='&nbsp;&nbsp;'.substr($tl,$segbegin,$pos-$segbegin).'<br />';
    $segbegin=$pos+1;
  }
  $retval.='&nbsp;&nbsp;'.substr($tl,$segbegin);
  return $retval;
}
?>
