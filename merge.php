<?php
/* functions for merge contacts and delete contact
   merge adds info from from_contact to to_contact, but leaves
   from_contact as it was
*/
function mergeCoordinate($msi,$fromcid,$tocid,
   $table_name,$join_table_name,$coordinate_id_name,&$ErrMsg)
{
  /* address, phone, e-mail, which have owner_id */
  if(!$result=$msi->query(
     "select co.* from $join_table_name coa ".
     "inner join $table_name co ".
     "on co.$coordinate_id_name=coa.$coordinate_id_name ".
     "where coa.contact_id=$fromcid")) {
    buildErrorMessage($ErrMsg,'merge coordinate lookup',$msi->error);
    return false;
  }
  //echo 'num rows: '.$result->num_rows.'<br>';
  while($rx=$result->fetch_assoc()) {
    /* if from_contact was owner, 
          need to create new and leave the old one */
    /*echo "owner, $coordinate_id_name: ".$rx['owner_id'].', '.
       $rx[$coordinate_id_name].'<br>';*/
    if($rx['owner_id'] == $fromcid) {
      $ix=$rx;
      //echo "coordinate id name: $coordinate_id_name<br>";
      $ix[$coordinate_id_name]='null';
      //echo "coordinate id name val: ".$ix[$coordinate_id_name].'<br>';
      $ix['owner_id']=$tocid;
      if(isset($ix['recurring']))$ix['recurring']='no';
      $values=ValuesList($msi,$ix);
      //echo "$values<br>";
      if(!$msi->query("insert into $table_name values($values)")) {
         buildErrorMessage($ErrMsg,"new $table_name insert",$msi->error);
         return false;
      }
      $new_coordinate_id=$msi->insert_id;
      if(!$msi->query("insert into $join_table_name ".
       "values (null,$tocid,$new_coordinate_id)")) {
        buildErrorMessage($ErrMsg,"new $join_table_name insert A",
           $msi->error);
        return false;
      }
    }
    else {
      /* Owner is not from_contact. if there isn't already
         a join-table rec for to_contact, create one */
      $new_coordinate_id=$rx[$coordinate_id_name];
      if(!$dupes=$msi->query("select $coordinate_id_name ".
         "from $join_table_name where contact_id=$tocid and ".
         "$coordinate_id_name=$new_coordinate_id")) {
         buildErrorMessage($ErrMsg,
            "new $join_table_name insert dupe check",$msi->error);
         return false;
      }
      if($dupes->num_rows == 0) {
        if(!$msi->query("insert into $join_table_name ".
           "values (null,$tocid,$new_coordinate_id)")) {
          buildErrorMessage($ErrMsg,"new $join_table_name insert",
             $msi->error);
          return false;
        }
      }
      $dupes->free();
    }
  }
  $result->free();
  return true;
}

function mergeRelation($msi,$fromcid,$tocid,&$ErrMsg)
{
  if(!$msi->query('insert into relationships '.
     '(contact_id,relationship_type_id,relative_id,modified) '.
     "(select $tocid,rf.relationship_type_id,".
     'rf.relative_id,now() from relationships rf '.
     "where rf.contact_id=$fromcid and rf.relative_id != $tocid)")) {
    buildErrorMessage($ErrMsg,"relationships insert",
       $msi->error);
    return false;
  }
  if(!$msi->query('insert into relationships '.
     '(contact_id,relationship_type_id,relative_id,modified) '.
     "(select contact_id,rf.relationship_type_id,$tocid,now() ".
     'from relationships rf '.
     "where rf.relative_id=$fromcid and rf.contact_id != $tocid)")) {
    buildErrorMessage($ErrMsg,"inverse relationships insert",
       $msi->error);
    return false;
  }
  return true;
}

function mergeItem($msi,$fromcid,$tocid,$cidfield,$table_name,$item_id_name,&$ErrMsg)
{
  /* roster_membership, note, donation, which don't have owner
     $cidfield is contact_id for roster_memberships and notes,
        primary_donor_id for donations */
   if(!$result=$msi->query(
     "select * from $table_name it where $cidfield=$fromcid")) {
    buildErrorMessage($ErrMsg,"merge $table_name lookup",$msi->error);
    return false;
  }
  //echo 'num rows: '.$result->num_rows.'<br>';
  while($rx=$result->fetch_assoc()) {
    $ix=$rx;
    $ix[$item_id_name]='null';
    $ix[$cidfield]=$tocid;
    $values=ValuesList($msi,$ix);
    //echo "$values<br>";
    if(!$msi->query("insert into $table_name values($values)")) {
      buildErrorMessage($ErrMsg,"new $table_name insert",$msi->error);
      return false;
    }
  }
}

function valueslist($msi,$arr) {
  $values='';
  $is_first=true;
  foreach($arr as $kx => $ax) {
    if($kx == 'modified') {
      $val='now()';
    }
    else if ($ax == 'null' || $ax == '') {
      $val='null';
    }
    else {
      $val="'".$msi->real_escape_string($ax)."'";
    }
    $values.=($is_first ? '' : ',') . $val;
    //echo "$kx val: ".$val.'<br>';
    //echo 'values: '.$values.'<br>';
    $is_first=false;
  }
  return $values;
}




?>
