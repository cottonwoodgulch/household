<?php
function smarty_modifier_NickName($name) {
  if(is_null($name) || (trim($name)==''))return ' ';
  return '"'.$name.'"';
}
?>
