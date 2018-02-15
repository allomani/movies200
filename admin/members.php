<?php
/**
 *  Allomani Movies v2.0
 * 
 * @package Allomani.Movies
 * @version 2.0
 * @copyright (c) 2006-2018 Allomani , All rights reserved.
 * @author Ali Allomani <info@allomani.com>
 * @link http://allomani.com
 * @license GNU General Public License version 3.0 (GPLv3)
 * 
 */

 if(!defined('IS_ADMIN')){die('No Access');}  



//------------------------------- Email Members -----------------------------------
if($action=="members_mailing"){
if_admin("members");
$username = htmlspecialchars($username) ; 
print "<p align=center class=title> $phrases[members_mailing] </p><br>" ;

 print "<center><iframe src='mailing.php?username=$username' width=95% height=800  border=0 frameborder=0></iframe></center>";
        }

//---------------- Members Search  ------------------------------
 if($action == "members_search"){

if_admin("members");

$limit = intval($limit);
$start  = intval($start);



 print "<p align=center class=title> $phrases[the_members] </p>
             ";

if($date_y || $date_m || $date_d){

   $birth_struct =  iif($date_y,$date_y."-","0000-").iif($date_m,$date_m."-","01-").iif($date_d,$date_d,"01");
  // print $birth_struct;

$birth = connector_get_date($birth_struct,'member_birth_date');
//print $birth;
    }else{
$birth = "";
}

$cond = members_fields_replace("username")." like '%".db_escape($username)."%' and ".members_fields_replace("email")." like '%".db_escape($email)."%' ";


$cond .= "and ".members_fields_replace('birth')." like '%$birth%' and country like '%".db_escape($country)."%'";

$c_custom = 0 ;

//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){

   for($i=0;$i<=count($custom_id);$i++){
   if($custom_id[$i] & $custom[$i] ){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
if(trim($m_custom_id) && trim($m_custom_name)){
    $c_custom++;
$cond .= " and field_".$m_custom_id." like '%".db_escape($m_custom_name)."%'";
}

       }
       }
  $cond .= " ";
   }



$cond2 =  " group by ".members_fields_replace("username");

/*
if((!$members_connector['enable'] || $members_connector['same_connection']) && $c_custom >0){
$sql= "select ".$srch_remote_db.".".members_table_replace("movies_members").".* from ".$srch_remote_db.".".members_table_replace("movies_members").",".$srch_local_db.".movies_members_fields where ".$cond ." limit $start,$limit";
$page_result_sql =  "select ".$srch_remote_db.".".members_table_replace("movies_members").".".members_fields_replace('id')." from ".$srch_remote_db.".".members_table_replace("movies_members").",".$srch_local_db.".movies_members_fields where ".$cond ;

}else{ */
$sql= "select * from ".members_table_replace('movies_members')." where ".$cond.$cond2 ." limit $start,$limit";
$page_result_sql = "select count(*) as count from ".members_table_replace('movies_members')." where ".$cond;

//}
//   print $sql;
//   print $page_result_sql;
$qr = db_query($sql,MEMBER_SQL);


 if(db_num($qr)){
 $page_result = db_qr_fetch($page_result_sql,MEMBER_SQL);
//$page_result['count'] = db_qr_num($page_result_sql,MEMBER_SQL);
 print "<b> $phrases[view]  </b>".($start+1)." - ".($start+$limit) . "<b> $phrases[from] </b> $page_result[count]<br><br>";



$page_string = "index.php?".substr($_SERVER['QUERY_STRING'],0,strpos($_SERVER['QUERY_STRING'],"&start="))."&start={start}";

 print " <center>


      <table width=100% class=grid><tr>
      <td><b>$phrases[username]</b></td><td><b>$phrases[email]</b></td>
 <td><b>$phrases[birth]</b></td>
 <td><b>$phrases[register_date]</b></td><td><b>$phrases[last_login]</b></td></tr>";
 while($data = db_fetch($qr)){
 print "<tr><td><a href='index.php?action=member_edit&id=".$data[members_fields_replace("id")]."'>$data[username]</td>
 </td><td>".$data[members_fields_replace("email")]."</td>
 <td>".$data[members_fields_replace("birth")]."</td>
 <td>".member_time_replace($data[members_fields_replace("date")])."</td>
 <td>".member_time_replace($data[members_fields_replace("last_login")])."</td>
 </tr>";

         }
         print "</table>";

//-------------------- pages system ------------------------
print_pages_links($start,$page_result['count'],$limit,$page_string);
//------------ end pages system -------------
         }else{

                 print " <center><table width=50% class=grid><tr>
                 <tr><td align=center> $phrases[no_results] </td></tr>";
                   print "</table></center>";
                 }



        }

//------------------------- Memebers Operations ---------------------------------
if($action=="members" || $action=="member_add_ok" || $action=="member_del"){
if_admin("members");

if($action=="member_add_ok"){

    $all_ok = 1;
 if(check_email_address($email)){
$email = db_escape($email);

$exsists = db_qr_num("select ".members_fields_replace('id')." from ".members_table_replace('movies_members')." where ".members_fields_replace('email')."='$email'",MEMBER_SQL);
      //------------- check email exists ------------
       if($exsists){
                         print "<li>$phrases[register_email_exists]<br>$phrases[register_email_exists2] <a href='index.php?action=forget_pass'>$phrases[click_here] </a></li>";
              $all_ok = 0 ;
           }
      }else{
       print_admin_table("$phrases[err_email_not_valid]");
      $all_ok = 0;
      }
       $username = db_escape($username);

        //------- username min letters ----------
       if(strlen($username) >= $settings['register_username_min_letters']){
       $exclude_list = explode(",",$settings['register_username_exclude_list']) ;

         if(!in_array($username,$exclude_list)){

     $exsists2 = db_qr_num("select ".members_fields_replace('id')." from ".members_table_replace('movies_members')." where binary ".members_fields_replace('username')."='$username'",MEMBER_SQL);

       //-------------- check username exists -------------
            if($exsists2){
                         print(str_replace("{username}",$username,"<li>$phrases[register_user_exists]</li>"));
                $all_ok = 0 ;
           }
           }else{
           print_admin_table("$phrases[err_username_not_allowed]");
         $all_ok= 0;
               }
          }else{
         print_admin_table("$phrases[err_username_min_letters]");
         $all_ok= 0;
          }
if($all_ok){
if($username && $email && $password){


 db_query("insert into ".members_table_replace('movies_members')." (".members_fields_replace('username').",".
 members_fields_replace('email').",".members_fields_replace('country').",".members_fields_replace('birth').",".
 members_fields_replace('usr_group').",".members_fields_replace('date').",gender)
 values('$username','$email','".db_escape($country)."','".connector_get_date("$date_y-$date_m-$date_d",'member_birth_date')."','$usr_group','".connector_get_date(time(),'member_reg_date')."','".db_escape($gender)."')",MEMBER_SQL);


 $member_id=mysql_insert_id();

//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
   db_query("update ".members_table_replace('movies_members')." set field_".$m_custom_id."='".db_escape($m_custom_name,false)."' where ".members_fields_replace('id')."='$member_id'",MEMBER_SQL);
 
       }
   }
   }
//-----------------------------------------------


connector_member_pwd($member_id,$password,'update');

 print "<center><table width=50% class=grid><tr><td align=center>
    $phrases[member_added_successfully]
    </td></tr></table></center><br>";

}else{
 print "<center><table width=50% class=grid><tr><td align=center>
   $phrases[please_fill_all_fields]
    </td></tr></table></center><br>";
}
}
        }

//------ delete memeber query --------
if($action == "member_del"){
db_query("delete from ".members_table_replace('movies_members')." where ".members_fields_replace('id')."='$id'",MEMBER_SQL);

print_admin_table( "<center>$phrases[member_deleted_successfully]</center>");
        }




//---------- show members search form ---------
print "<p align=center class=title> $phrases[the_members] </p>
        <p align=$global_align><a href='index.php?action=member_add'><img src='images/add.gif' border=0> $phrases[add_member] </a></p>
              <center>
     <form action=index.php method=get>
      <fieldset style=\"width:80%;padding: 2\">
      <table width=100%>
   <input type=hidden name='action' value='members_search'>

   <tr><td> $phrases[username] : </td><td><input type=text name=username size=30></td></tr>
   <tr><td> $phrases[email]  : </td><td><input type=text name=email size=30></td></tr>";
    print "</table>
</fieldset>";

      print "<br><br><fieldset style=\"width:80%;padding: 2\">
<table width=100%>
    <tr><td><b> $phrases[birth] </b> </td><td>
    <input type=text size=1 name='date_d'> - <input type=text size=1 name='date_m'> - <input type=text size=4 name='date_y'></td></tr>

            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''></option>";
            $c_qr = db_query("select * from movies_countries order by binary name asc");
   while($c_data = db_fetch($c_qr)){


        print "<option value='$c_data[name]'>$c_data[name]</option>";
           }
           print "</select></td>   </tr></table></fieldset>";

   $cf = 0 ;

   //------------ custom fields -----
  // if(!$members_connector['enable'] || $members_connector['same_connection']){
$qr = db_query("select * from movies_members_sets order by required,ord");
   if(db_num($qr)){
    print "<br><br><fieldset style=\"width:80%;padding: 2\">
    <legend>$phrases[addition_fields] </legend>
<br><table width=100%>";

while($data = db_fetch($qr)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$data[id]\">
    <tr><td width=25%><b>$data[name]</b><br>$data[details]</td><td>";
    print get_member_field("custom[$cf]",$data,"",true);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}
 //  }

   print "<br><br><fieldset style=\"width:80%;padding: 2\">
      <table width=100%>

      <tr><td width=30%>$phrases[records_perpage]</td><td><input type=text name=limit size=3 value='30'></td><td align=center><input type='submit' value=' $phrases[search_do] '></td></tr>
  </table></fieldset>
   <input type=hidden name=start value=\"0\">
   </form></center>" ;
        }
 //-----------------------------------------------------
if($action=="member_edit" || $action == "member_edit_ok"){
   if_admin("members");

   
     //---- delete img -----
  if($op=="img_del"){
   $data = db_qr_fetch("select img,thumb from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='$id'",MEMBER_SQL);
   delete_file($data['img']);
   delete_file($data['thumb']);
   db_query("update ".members_table_replace("movies_members")." set img='',thumb='' where ".members_fields_replace("id")."='$id'",MEMBER_SQL); 
     
  }
  
  
 //------------------------------------------------------------------ 
   if($action == "member_edit_ok"){


          //----- update profile picture ----------
          if($settings['members_profile_pictures'] && $_FILES['img']['name']){ 
              require_once(CWD. "/includes/class_save_file.php");   
    
    $upload_folder = $settings['uploader_path']."/profiles" ;
    $allowed_types = array("jpg","png","gif","bmp");
    $imtype = file_extension($_FILES['img']['name']);  
    
    if($_FILES['img']['error']==UPLOAD_ERR_OK){  
    if(in_array($imtype,$allowed_types)){ 
        
$fl = new save_file($_FILES['img']['tmp_name'],$upload_folder,rand_string(20).".".$imtype);
         
if($fl->status){
$img_saved =  create_thumb($fl->saved_filename,$settings['profile_pic_width'],$settings['profile_pic_width'],1,'',1,basename($fl->saved_filename)); 
$thumb_saved =  create_thumb($img_saved,$settings['profile_pic_thumb_width'],$settings['profile_pic_thumb_height'],1,'thumb');
            
if(file_exists($img_saved) && file_exists($thumb_saved)){
$old_data = db_qr_fetch("select img,thumb from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='$id'",MEMBER_SQL); 

if($old_data['img']){delete_file($old_data['img']);}
if($old_data['thumb']){delete_file($old_data['thumb']);}

db_query("update ".members_table_replace("movies_members")." set img='".db_escape($img_saved,false)."',thumb='".db_escape($thumb_saved,false)."' where ".members_fields_replace("id")."='$id'",MEMBER_SQL);
  
}else{
      
    print_admin_table("<center>$phrases[err] : $phrases[photo_not_saved]</center>"); 
   
}

}else{  
print_admin_table("<center><b>$phrases[err] : $phrases[photo_not_saved]</center>");  
}


    }else{
     
        print_admin_table("<center><b>$phrases[the_photo] : </b> $phrases[this_filetype_not_allowed]</center>"); 
     
    }
    }else{
    $upload_max = convert_number_format(ini_get('upload_max_filesize'));
    $post_max = (convert_number_format(ini_get('post_max_size'))/2) ;
    $max_size = iif($upload_max < $post_max,convert_number_format($upload_max,2,true),convert_number_format($post_max,2,ture));
    
     
    print_admin_table("<center><b>$phrases[the_photo] : </b> $phrases[err_upload_max_size] $max_size</center>");  
       
    } 
    
        
          }
          
//------ update query ------//

db_query("update ".members_table_replace('movies_members')." set ".members_fields_replace('username').
"='".db_escape($username)."',".members_fields_replace('email')."='$email',".members_fields_replace('country')."='".db_escape($country)."',".
members_fields_replace('birth')."='".connector_get_date("$date_y-$date_m-$date_d",'member_birth_date')."',".
members_fields_replace('usr_group')."='$usr_group',gender='".db_escape($gender)."'  where ".members_fields_replace('id')."='$id'",MEMBER_SQL);

 //-------- if change password --------------
          if ($password){
              if($password == $re_password){
               connector_member_pwd($id,$password,'update');
              }else{

              print_admin_table("<center>$phrases[err_passwords_not_match]</center>");

              }
           }

//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
 
 db_query("update ".members_table_replace('movies_members')." set field_".$m_custom_id."='".db_escape($m_custom_name,false)."' where ".members_fields_replace('id')."='$id'",MEMBER_SQL);
 

       }
   }
   }

   print_admin_table("<center>$phrases[member_edited_successfully]</center>");
         }
         
 //----------------------------------------------------------
  
  $qr = db_query("select * from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='$id'",MEMBER_SQL);

    if(db_num($qr)){
                   $data = db_fetch($qr);
          $birth_data = connector_get_date($data[members_fields_replace('birth')],"member_birth_array");
           print "
                   <script type=\"text/javascript\" language=\"javascript\">
<!--
function pass_ver(theForm){
 if (theForm.elements['password'].value == theForm.elements['re_password'].value){

        if(theForm.elements['email'].value && theForm.elements['username'].value){
        return true ;
        }else{
       alert (\"$phrases[err_fileds_not_complete]\");
return false ;
}
}else{
alert (\"$phrases[err_passwords_not_match]\");
return false ;
}
}
//-->
</script>

           <center>  <p class=title>  $phrases[member_edit] </p>

           <form action=index.php method=post onsubmit=\"return pass_ver(this)\" enctype=\"multipart/form-data\">
          <input type=hidden name=action value=member_edit_ok>
          <input type=hidden name=id value='".intval($id)."'>";

          
           if($settings['members_profile_pictures']){     
           print "
           <fieldset style=\"width:70%;padding: 2\">
           <legend>$phrases[profile_picture]</legend>
          <table width=100%><tr>
          <td width='".($settings['profile_pic_width']+10)."' align=center><img src=\"$scripturl/".get_image($data['img'],$style['images']."/profile_no_pic".iif($data['gender'],"_".$data['gender']).".gif")."\">
          <br><a href='index.php?action=member_edit&op=img_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete_picture]</a></td>
          <td><input type='file' name='img'></td>
          </tr>
          </table>
          </fieldset><br>";
          }
          
          print "<fieldset style=\"width:70%;padding: 2\"><table width=100%>

     <tr>
          <td width=20%>
         $phrases[username] :
          </td><td ><input type=text name=username value='".$data[members_fields_replace("username")]."'></td>  </tr>
           <td width=20%>
          $phrases[email] :
          </td><td ><input type=text name=email value='".$data[members_fields_replace("email")]."' size=30></td>  </tr>
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>
         <tr><td colspan=2><font color=#D90000>*  $phrases[leave_blank_for_no_change] </font></td></tr>
             <tr><td colspan=2>&nbsp;</td></tr>




 <tr>   <td>$phrases[member_acc_type] : </td><td>";
                print_select_row("usr_group",get_members_groups_array(),$data[members_fields_replace('usr_group')]);
                    /*
             if($data[members_fields_replace('usr_group')]==member_group_replace(1)){$chk2 = "selected" ; $chk1="";$chk3="";}
             elseif($data[members_fields_replace('usr_group')]==member_group_replace(2)){$chk2 = "" ; $chk1="";$chk3="selected";}
             elseif($data[members_fields_replace('usr_group')]==member_group_replace(0)){$chk2 = "" ; $chk1="selected";$chk3="";}

            print " <select name=usr_group><option value=0 $chk1>غير منشط</option>
            <option value=1 $chk2>مفعل</option>
            <option value=2 $chk3>مغلق</option>
            </select>";
            */
            print "</td>     </tr>
</table></fieldset>";

 $cf = 0 ;

$qrf = db_query("select * from movies_members_sets where required=1 order by ord");
   if(db_num($qrf)){
    print "<br><fieldset style=\"width:70%;padding: 2\">
    <legend>$phrases[req_addition_info]</legend>
<br><table width=100%>";

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf,$data["field_".$dataf['id']]);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}

            print "<br><fieldset style=\"width:70%;padding: 2\">
    <legend>$phrases[not_req_addition_info]</legend>
<br><table width=100%>

<tr>  <td><b>$phrases[gender] </b> </td><td>";
   print_select_row("gender",array(""=>"","male"=>"$phrases[male]","female"=>"$phrases[female]"),$data['gender']);
   print "</td></tr>
   
   
    <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'>";
    for($i=1;$i<=31;$i++){
             if(strlen($i) < 2){$i="0".$i;}
                 if($birth_data['day'] == $i){$chk="selected" ; }else{$chk="";}
           print "<option value=$i $chk>$i</option>";
           }
           print "</select>
           - <select name=date_m>";
            for($i=1;$i<=12;$i++){
                    if(strlen($i) < 2){$i="0".$i;}
                    if($birth_data['month'] == $i){$chk="selected" ; }else{$chk="";}
           print "<option value=$i $chk>$i</option>";
           }
           print "</select>
           - <input type=text size=3 name='date_y' value='$birth_data[year]'></td></tr>
            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''></option>";
            $c_qr = db_query("select * from movies_countries order by binary name asc");
   while($c_data = db_fetch($c_qr)){

           if($data['country']==$c_data['name']){$chk="selected";}else{$chk="";}
        print "<option value='$c_data[name]' $chk>$c_data[name]</option>";
           }
           print "</select></td>   </tr>";

           $qrf = db_query("select * from movies_members_sets where required=0 order by ord");
   if(db_num($qrf)){

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf,$data["field_".$dataf['id']]);
        print "</td></tr>";
$cf++;
}
}

           print "</table>
           </fieldset>";


          print "<br><br><fieldset style=\"width:70%;padding: 2\"><table width=100%>

           <tr><td align=center><input type=submit value=' $phrases[edit] '></td></tr>
                     <tr><td align=left><a href='index.php?action=members_mailing&username=".$data[members_fields_replace("username")]."'>$phrases[send_msg_to_member] </a> - <a href='index.php?action=member_del&id=$id' onclick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a></td></tr>
          </tr></table></fieldset>
         </form> ";
         }else{
                 print "<center>  $phrases[this_member_not_exists] </center>";
                 }
        }
 //------------------------- add member --------
 if($action=="member_add"){
   if_admin("members");

           print "
                   <script type=\"text/javascript\" language=\"javascript\">
<!--
function pass_ver(theForm){
 if (theForm.elements['password'].value == theForm.elements['re_password'].value){

        if(theForm.elements['email'].value && theForm.elements['username'].value){
        return true ;
        }else{
       alert (\"$phrases[err_fileds_not_complete]\");
return false ;
}
}else{
alert (\"$phrases[err_passwords_not_match]\");
return false ;
}
}
//-->
</script>

           <center><p class=title>  $phrases[add_member] </p> <table width=70% class=grid>

           <form action=index.php method=post onsubmit=\"return pass_ver(this)\">
          <input type=hidden name=action value=member_add_ok>

     <tr>
          <td width=20%>
         $phrases[username] :
          </td><td ><input type=text name=username></td>  </tr>
           <td width=20%>
          $phrases[email] :
          </td><td ><input type=text name=email size=30></td>  </tr>
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>

             <tr><td colspan=2>&nbsp;</td></tr>

             <tr>   <td>$phrases[member_acc_type] : </td><td>";
              print_select_row("usr_group",get_members_groups_array());


            print "
            </td>     </tr>
            </table>";

   $cf = 0 ;

$qrf = db_query("select * from movies_members_sets where required=1 order by ord");
   if(db_num($qrf)){
    print "<br><fieldset style=\"width:70%;padding: 2\">
    <legend>$phrases[req_addition_info]</legend>
<br><table width=100%>";

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf);
        print "</td></tr>";
$cf++;
}
print "</table>
</fieldset>";
}

            print "<br><fieldset style=\"width:70%;padding: 2\">
    <legend>$phrases[not_req_addition_info]</legend>
<br><table width=100%>

<tr>  <td><b>$phrases[gender] </b> </td><td>";
   print_select_row("gender",array(""=>"","male"=>"$phrases[male]","female"=>"$phrases[female]"),"");
   print "</td></tr>
   
   
    <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'>";
    for($i=1;$i<=31;$i++){
             if(strlen($i) < 2){$i="0".$i;}

           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <select name=date_m>";
            for($i=1;$i<=12;$i++){
                    if(strlen($i) < 2){$i="0".$i;}

           print "<option value=$i>$i</option>";
           }
           print "</select>
           - <input type=text size=3 name='date_y' value='0000'></td></tr>
            <tr>  <td><b>$phrases[country] </b> </td><td><select name=country><option value=''></option>";
            $c_qr = db_query("select * from movies_countries order by binary name asc");
   while($c_data = db_fetch($c_qr)){


        print "<option value='$c_data[name]'>$c_data[name]</option>";
           }
           print "</select></td>   </tr>";

           $qrf = db_query("select * from movies_members_sets where required=0 order by ord");
   if(db_num($qrf)){

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf);
        print "</td></tr>";
$cf++;
}
}

           print "</table>
           </fieldset>";


          print "<br><br><fieldset style=\"width:70%;padding: 2\"><table width=100%>


           <tr><td align=center><input type=submit value=' $phrases[add_button] '></td></tr>
                </table></fieldset>
         </form> ";
        }
?>
