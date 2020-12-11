<?php
function smarty_modifier_Phone($number,$formatted) {
  if($formatted)return $number;
  return substr($number,0,3).'-'.substr($number,3,3).'-'.substr($number,6);
}
?>
