<?
if(!defined('IS_ADMIN')){die('No Access');}  

//-------------- Remote Members Database ---------------
   if($action=="members_remote_db"){
   if_admin();

print "<p align=center class=title> $phrases[cp_remote_members_db] </p>";

print_admin_table("<center>$phrases[you_can_edit_this_values_from_config_file]</center>");

print "<br>
<center><table width=60% class=grid><tr><td><b>$phrases[use_remote_db]</b></td><td>".($members_connector['enable'] ? $phrases['yes'] : $phrases['no'])."</td></tr>";
if($members_connector['enable']){
print "<tr><td><b>$phrases[db_host]</b></td><td>$members_connector[db_host]</td></tr>
<tr><td><b>$phrases[db_name]</b></td><td>$members_connector[db_name]</td></tr>
<tr><td><b>$phrases[members_table]</b></td><td>$members_connector[members_table]</td></tr>";
}
print "</table>
<br>
<fieldset style=\"padding: 2;width=400\" >
<legend>$phrases[note]</legend>
$phrases[members_remote_db_wizzard_note]
</fieldset>
<br><br>
<form action='index.php' method=get>
<input type=hidden name=action value='members_remote_db_wizzard'>
<input type=submit value=' $phrases[members_remote_db_wizzard] '>
</form></center>";

   }
 //------------ Members Remote DB Wizzard ---------------
 if($action=="members_remote_db_wizzard"){
     if_admin();
print "<p align=center class=title>$phrases[members_remote_db_wizzard]</p>";


$qrf = db_query("select id from movies_members_sets");
while($dataf = db_fetch($qrf)){
    $required_database_fields_names[] = "field_".$dataf['id'];
    $required_database_fields_types[] = "text";
   
}


if($members_connector['enable']){
$conx  = @mysql_connect($members_connector['db_host'],$members_connector['db_username'],$members_connector['db_password']);
if($conx){
if(mysql_select_db($members_connector['db_name'])){




//---------------- STEP 1 : CHECK TABLES FIELDS ---------------
  $tables_ok = 1 ;
  


 //   print_r($required_database_fields_names);
    
    
 if(is_array($required_database_fields_names)){

   //        print "SHOW FIELDS FROM ".members_table_replace("movies_members");
 $qr = db_query("SHOW FIELDS FROM ".members_table_replace("movies_members")."",MEMBER_SQL);
  $c=0;
while($data =db_fetch($qr)){

    $table_fields['name'][$c] = $data['Field'];
    $table_fields['type'][$c] = $data['Type'];
    $c++;
    }
   

print "<center><br><table width=80% class=grid>";
for($i=0;$i<count($required_database_fields_names);$i++){
    
//--------- Neme TD ------
print "<tr><td>".$required_database_fields_names[$i]."</td>";
//------- Type TD  ---------
if(is_array($required_database_fields_types[$i])){$req_type = $required_database_fields_types[$i];}else{$req_type=array($required_database_fields_types[$i]);}

print "<td>";
foreach($req_type as $value){
    print "$value &nbsp;";
    }
    print "</td><td>";
//----------------------------

$searchkey =  array_search($required_database_fields_names[$i],$table_fields['name']);
if($searchkey){
                

if(in_array($table_fields['type'][$searchkey],$req_type)){
print "<b><font color=green>Valid</font></b>";
}else{
print "<b><font color=red>Not Valid Type</font></b>";
$qrx = db_query("ALTER TABLE ".members_table_replace("movies_members")." CHANGE `".$required_database_fields_names[$i]."` `".$required_database_fields_names[$i]."` ".$req_type[0]." NOT NULL ;",MEMBER_SQL);

    if(!$qrx){
    print "<td><b><font color=red> $phrases[chng_field_type_failed] </font></b></td>";
        $tables_ok = 0;
        }else{
        print "<td><b><font color=green> $phrases[chng_field_type_success] </font></b></td>";
            }
            unset($qrx);
    }
print "</td>";
    }else{
    print "<td><b><font color=red>Not found</font></b></td>";

    $qrx = db_query("ALTER TABLE ".members_table_replace("movies_members")." ADD `".$required_database_fields_names[$i]."` ".$req_type[0]." NOT NULL ;",MEMBER_SQL);

    if(!$qrx){
    print "<td><b><font color=red> $phrases[add_field_failed] </font></b></td>";
        $tables_ok = 0;
        }else{
        print "<td><b><font color=green>$phrases[add_field_success] </font></b></td>";
            }
            unset($qrx);
        }
        }
        print "</table></center><br>";
        }
        //----------- end tables check -----------
        if($tables_ok){
        print_admin_table($phrases['members_remote_db_compatible']);
            }else{
            print_admin_table($phrases['members_remote_db_uncompatible']);
                }
        //--------- clean local db note ------------
        print "<center> <br>
<fieldset style=\"padding: 2;width=400\" >
<legend>$phrases[note]</legend>
$phrases[members_local_db_clean_note]
</fieldset>
<br><br>
<form action='index.php' method=get>
<input type=hidden name=action value='members_local_db_clean'>
<input type=submit value=' $phrases[members_local_db_clean_wizzard] '>
</form></center>";

        }else{
        print_admin_table($phrases['wrong_remote_db_name']);
            }
        }else{
            print_admin_table($phrases['wrong_remote_db_connect_info']);
            }
        }else{
        print_admin_table($phrases['members_remote_db_disabled']);
            }
 }

 //-------------- Clean Members Local DB -------------
 if($action=="members_local_db_clean"){
 print "<p align=center class=title> $phrases[members_local_db_clean_wizzard] </p>
 <center><table width=70% class=grid><tr><td>";
 if($process){
 db_query("TRUNCATE TABLE `movies_members_favorites`");
 db_query("TRUNCATE TABLE `movies_members_msgs`");
 db_query("TRUNCATE TABLE `movies_confirmations`");
 db_query("TRUNCATE TABLE `movies_comments`"); 
 db_query("TRUNCATE TABLE `movies_members_black`"); 
 db_query("TRUNCATE TABLE `movies_members_friends`"); 
 

  print "<center><b> $phrases[process_done_successfully]</b></center>";
 }else{
 print "<br> <b>$phrases[members_local_db_clean_description]
 <ul>
 <li>$phrases[members_msgs_table]</li>
 <li>$phrases[members_favorite_table]</li>
 <li>$phrases[members_confirmations_table]</li>
 <li>$phrases[members_comments]</li> 
 <li>$phrases[friends_list]</li> 
 <li>$phrases[black_list]</li> 
 
 </ul></b>
 <center>
 <form action='index.php' method=post>
 <input type=hidden name=action value='members_local_db_clean'>
 <input type=hidden name=process value='1'>
 <input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('$phrases[are_you_sure]');\">
 </form>
 </center>";
 }
 print "</td></tr></table></center>";


 }
 ?>