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
 
 require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------
$is_user_cp = true ;  

                     

if(check_member_login()){


  //------------------------------------------------
  if(!$action || $action=="del_fav"){

  if($action=="del_fav"){
          db_query("delete from movies_members_favorites where id='$id' and uid='$member_data[id]'");
          }
 //---------------------------------------------------
      
                 

//----------------------  Fav ---------------------
        open_table("$phrases[the_favorite]");      
          $qr = db_query("select movies_data.*,movies_members_favorites.id as fav_id from movies_data,movies_members_favorites where movies_data.id=movies_members_favorites.fid and movies_members_favorites.uid='$member_data[id]'");
          if(db_num($qr)){
          
          
           
   run_template('browse_movies_header');

$c=0 ;


while ($data =db_fetch($qr)){

   
if ($c==$settings['movies_cells']) {
run_template('browse_movies_sep');
$c = 0 ;
}
 ++$c ;

run_template('browse_movies');
              
}
run_template('browse_movies_footer');
          
          }else{
                          print "<center>  $phrases[no_movies] </center>";
          }

         close_table();
          }


//------------------- Profile -------------------------------
  if($action=="profile" || $action=="profile_edit"){

      
  //---- delete img -----
  if($op=="img_del"){
   $data = db_qr_fetch("select img,thumb from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);
   delete_file($data['img']);
   delete_file($data['thumb']);
   db_query("update ".members_table_replace("movies_members")." set img='',thumb='' where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL); 
     
  }
  
  
//--------------------------------------------------------------------------------------
      if($action=="profile_edit"){

  //------------ update profile info ---------------------
          
           
          
          //---------- email change confirmation ----------- 
            if(check_email_address($email)){  
          if($settings['auto_email_activate']){
              $email_update_query = ", ".members_fields_replace("email")."='".db_escape($email)."'" ;
          }else{   
          $data_email = db_qr_fetch("select ".members_fields_replace('email').",".members_fields_replace('username')." from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);
          if($email != $data_email['email']){
          $val_code = md5($email.$data_email['email'].time().rand(0,100));    
          db_query("insert into movies_confirmations (type,old_value,new_value,cat,code) values ('member_email_change','".$data_email['email']."','".db_escape($email,false)."','".intval($member_data['id'])."','$val_code')");
          snd_email_chng_conf($data_email['username'],$email,$val_code);
          open_table();
          print "<center> $phrases[chng_email_conf_msg_sent] </center>";
          close_table();
          }
          $email_update_query = "";
          }
           }else{
           $email_update_query = ""; 
          open_table();
          print "<center> $phrases[err_email_not_valid] </center>";
          close_table();
          }
          //------------------
          
         //----- update profile picture ----------
          if($settings['members_profile_pictures'] && $_FILES['img']['name']){ 
if($settings['uploader']){               
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
$old_data = db_qr_fetch("select img,thumb from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL); 

if($old_data['img']){delete_file($old_data['img']);}
if($old_data['thumb']){delete_file($old_data['thumb']);}

db_query("update ".members_table_replace("movies_members")." set img='".db_escape($img_saved,false)."',thumb='".db_escape($thumb_saved,false)."' where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);
  
}else{
     open_table();     
    print("<center>$phrases[err] : $phrases[photo_not_saved]</center>"); 
     close_table();   
}

}else{
     open_table();     
print("<center><b>$phrases[err] : $phrases[photo_not_saved]</center>"); 
 close_table();    
}


    }else{
        open_table();
        print("<center><b>$phrases[the_photo] : </b> $phrases[this_filetype_not_allowed]</center>"); 
        close_table();  
    }
    }else{
    $upload_max = convert_number_format(ini_get('upload_max_filesize'));
    $post_max = (convert_number_format(ini_get('post_max_size'))/2) ;
    $max_size = iif($upload_max < $post_max,convert_number_format($upload_max,2,true),convert_number_format($post_max,2,ture));
    
     open_table();
    print("<center><b>$phrases[the_photo] : </b> $phrases[err_upload_max_size] $max_size</center>");  
     close_table();  
    } 
    
          }else{
         open_table();
    print("<center> $settings[uploader_msg]</center>");  
     close_table();      
          }
                
          }
         //---------------------------------------
         
         
       //---- update profile query -----//
    //   if(!isset($privacy_settings_array[$privacy_profile])){$privacy_profile = 0;} 
     
         $prv = array_map('intval',$prv);
         $prv_settings = serialize($prv);
         
         db_query("update ".members_table_replace("movies_members")." set ".members_fields_replace("country")."='".db_escape($country)."',".members_fields_replace("gender")."='".db_escape($gender)."',
         pm_email_notify='".intval($pm_email_notify)."',privacy_settings='".db_escape($prv_settings)."',
         ".members_fields_replace("birth")."='".db_escape(connector_get_date("$date_y-$date_m-$date_d",'member_birth_date'))."'
          $email_update_query where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);

        
          //-------- if change password --------------
          if ($password){
              if($password == $re_password){
               connector_member_pwd($member_data['id'],$password,'update');
              }else{
              open_table();
              print "<center>$phrases[err_passwords_not_match]</center>";
              close_table();
              }
           }
        
//------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i] && $custom[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;

db_query("update ".members_table_replace('movies_members')." set field_".$m_custom_id."='".db_escape($m_custom_name)."' where ".members_fields_replace('id')."='$member_data[id]'",MEMBER_SQL);
 

       }
   }
   }

         open_table(); 
          print "<center>  $phrases[your_profile_updated_successfully] </center>";

        close_table();
              }


          open_table($phrases['the_profile']);

          $data = db_qr_fetch("select * from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'",MEMBER_SQL);
          
          unset($prv_data);
          $prv_data = unserialize($data['privacy_settings']);
                  
                                          
                  $birth_data = connector_get_date($data[members_fields_replace('birth')],"member_birth_array");
             
           print "
                   <script type=\"text/javascript\" language=\"javascript\">
<!--
function pass_ver(theForm){
 if (theForm.elements['password'].value == theForm.elements['re_password'].value){

        if(theForm.elements['email'].value){
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


<div align='$global_align_x'>
<a href=\"".str_replace("{id}",$member_data['id'],$links['links_profile'])."\" target=_blank>
<img src='$style[images]/profile_preview.gif' border=0>&nbsp; $phrases[profile_preview] </a>
</div><br>


           <form action=usercp.php method=post onsubmit=\"return pass_ver(this)\" enctype=\"multipart/form-data\">
          <input type=hidden name=action value=profile_edit> ";
         
         if($settings['members_profile_pictures']){     
           print "
           <fieldset style=\"padding: 2\">
           <legend>$phrases[profile_picture]</legend>
          <table width=100%><tr>
          <td align='center' width='".($settings['profile_pic_width']+10)."'><img src=\"".get_image($data['img'],$style['images']."/profile_no_pic".iif($data['gender'],"_".$data['gender']).".gif")."\">
          <br><a href='usercp.php?action=profile&op=img_del' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete_picture]</a>
          </td>
          <td><input type='file' name='img'></td>
          </tr>
          </table>
          </fieldset><br>";
          }
          
          print "
          <fieldset style=\"padding: 2\">
          <table width=100%><tr>
          <td width=20%>
         $phrases[username] :
          </td><td>".$data[members_fields_replace('username')]."</td>  </tr>
           <td width=20%>
          $phrases[email] :
          </td><td ><input type=text name=email value='".$data[members_fields_replace('email')]."' size=30></td>  </tr>
          </tr></table>
          </fieldset>
          <br>
         <fieldset style=\"padding: 2\">
          <table width=100%><tr> 
          <tr>  <td>  $phrases[password] : </td><td><input type=password name=password></td>   </tr>
          <tr>  <td>  $phrases[password_confirm] : </td><td><input type=password name=re_password></td>   </tr>
         <tr><td colspan=2><font color=#D90000>*  $phrases[leave_blank_for_no_change] </font></td></tr>
          </tr></table></fieldset>";

          $cf = 0 ;

$qrf = db_query("select * from movies_members_sets where required=1 order by ord");
   if(db_num($qrf)){
    print "<br><fieldset style=\"padding: 2\">
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

            print "<br><fieldset style=\"padding: 2\">
    <legend>$phrases[not_req_addition_info]</legend>
<br><table width=100%>

   <tr>  <td><b>$phrases[gender] </b> </td><td>";
   print_select_row("gender",array(""=>"","male"=>"$phrases[male]","female"=>"$phrases[female]"),$data['gender']);
   print "</td></tr>
   
   
    <tr><td><b> $phrases[birth] </b> </td><td><select name='date_d'>
    ";
    for($i=1;$i<=31;$i++){
             if(strlen($i) < 2){$i="0".$i;}
                 if($birth_data['day'] == $i){$chk="selected" ; }else{$chk="";}
           print "<option value=$i $chk>$i</option>";
           }
           print "</select>
           - <select name=date_m>
           ";
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
  //--------
   $qrf = db_query("select * from movies_members_sets where required=0 order by ord");
   if(db_num($qrf)){

while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name]</b><br>$dataf[details]</td><td>";
    print get_member_field("custom[$cf]",$dataf,$data["field_".$dataf['id']],true);
        print "</td></tr>";
$cf++;
}
}
//-------

           print "</table>
           </fieldset><br>
           
           <fieldset style=\"padding: 2\">
           <input type='checkbox' name='pm_email_notify' value='1' ".iif($data['pm_email_notify'],"checked")."> $phrases[new_pm_email_notify]
           </fieldset>    <br>
           ";
  print " <fieldset style=\"padding: 2\">  
  <legend>$phrases[privacy_settings]</legend>
  <table width='100%'>
  <tr><td>$phrases[profile_view]</td><td>";
  print_select_row("prv[profile]",$privacy_settings_array,$prv_data['profile']);
  print "</td>
  </tr>
  
  <tr><td>$phrases[gender]</td><td>";
  print_select_row("prv[gender]",$privacy_settings_array,$prv_data['gender']);
  print "</td>
  </tr>
  
  
  <tr><td>$phrases[birth]</td><td>";
  print_select_row("prv[birth]",$privacy_settings_array,$prv_data['birth']);
  print "</td>
  </tr>
  
  
  <tr><td>$phrases[country]</td><td>";
  print_select_row("prv[country]",$privacy_settings_array,$prv_data['country']);
  print "</td>
  </tr>
  
   <tr><td>$phrases[last_login]</td><td>";
  print_select_row("prv[last_login]",$privacy_settings_array,$prv_data['last_login']);
  print "</td>
  </tr>
  
   <tr><td>$phrases[online_status]</td><td>";
  print_select_row("prv[online]",$privacy_settings_array,$prv_data['online']);
  print "</td>
  </tr>
  
  
  
  
  
  ";
  
  $qrfp = db_query("select * from movies_members_sets order by ord");
   if(db_num($qrfp)){
       while($datafp = db_fetch($qrfp)){
       print "
  <tr><td>$datafp[name]</td><td>";
  print_select_row("prv[field_".$datafp['id']."]",$privacy_settings_array,$prv_data["field_$datafp[id]"]);
  print "</td>
  </tr>";
       }
   }
   
  
  
  
 print "
   <tr><td>$phrases[fav_movies]</td><td>";
  print_select_row("prv[fav_movies]",$privacy_settings_array,$prv_data['fav_movies']);
  print "</td>
  </tr>
  <tr><td>$phrases[receive_pm_from]</td><td>";
  print_select_row("prv[messages]",$privacy_settings_array,$prv_data['messages']);
  print "</td>
  </tr> 
  
  
  </table>
  </fieldset><br>
  ";

          print "<br><fieldset style=\"padding: 2\"><table width=100%>
          <tr><td  align=center><input type=submit value=' $phrases[edit] '></td></tr>  </table>
          </fieldset></form> ";

          close_table();
          }
          

          
//--------------------- friends ---------------
if($action=="friends" || $action=="friends_del" || $action=="friends_add"){
    
    $id = (int) $id ;
    
//--  del --
if($action=="friends_del"){
    db_query("delete from movies_members_friends where uid2='$id' and uid1='".$member_data['id']."'");
}

//---- add -----
if($action=="friends_add"){


if($username){
$data = db_qr_fetch("select id from movies_members where username like '".db_escape($username)."'");
$id = intval($data['id']);
if(!$id){
open_table(); 
print "<center> $phrases[invalid_username] </center>";
close_table();
}
}


if($id){
open_table(); 
$qr = db_query("select uid1 from movies_members_friends where uid2='$id' and uid1='{$member_data['id']}'");
if(db_num($qr)){
    print "<center>  $phrases[this_member_exist_in_list] </center>";
}else{

    db_query("insert into movies_members_friends (uid1,uid2,date) values('{$member_data['id']}','$id','".time()."')");
    print "<center> $phrases[add_done_successfully] </center>";
}
close_table();
}
}
//-----------------


    $qr=db_query("select movies_members.* from movies_members,movies_members_friends  where movies_members.id = movies_members_friends.uid2 and movies_members_friends.uid1 ='".$member_data['id']."'");
    open_table("$phrases[friends_list]");
    print "<form action='usercp.php' method=post>
    <input type='hidden' name='action' value='friends_add'>
    <fieldset>
    <legend>$phrases[add_member]</legend>
    <input type='text' name='username' size=10><input type='submit' value='$phrases[add]'>
    </fieldset>
    </form><br><br>";
    
    if(db_num($qr)){
        print "
        <table width=100%>";
        while($data=db_fetch($qr)){
          print "<tr><td align=center><a href=\"".str_replace("{id}",$data['id'],$links['links_profile'])."\"><img title=\"$data[username]\" src=\"".get_image($data['thumb'],$style['images']."/profile_no_pic_thumb".iif($data['gender'],"_".$data['gender']).".gif")."\" border=0></a></td>
          <td width='100%'><a href=\"".str_replace("{id}",$data['id'],$links['links_profile'])."\" title=\"$data[username]\">$data[username]</a></td><td align=left><a href='usercp.php?action=friends_del&id=$data[id]' onClick=\"return confirm('{$phrases['are_you_sure']}');\"><img src='images/del.gif' border=0 title='$phrases[delete]'></a></td></tr>";  
        }
    print "</table>";
    }else{
        print "<center>  $phrases[no_friends_in_list] </center>";
    }
    
    close_table();
    
}

//-------------- Blovk List -----------
if($action=="black_list" || $action=="black_list_del" || $action=="black_list_add"){
    
    $id = (int) $id ;
    
//--  del --
if($action=="black_list_del"){
    db_query("delete from movies_members_black where uid2='$id' and uid1='".$member_data['id']."'");
}

//---- add -----
if($action=="black_list_add"){
    
if($username){
$data = db_qr_fetch("select id from movies_members where username like '".db_escape($username)."'");
$id = intval($data['id']);
if(!$id){
open_table(); 
print "<center> $phrases[invalid_username] </center>";
close_table();
}
}

if($id){ 
open_table();
$qr = db_query("select uid1 from movies_members_black where uid2='$id' and uid1='{$member_data['id']}'");
if(db_num($qr)){
    print "<center>  $phrases[this_member_exist_in_list] </center>";
}else{
   db_query("insert into movies_members_black (uid1,uid2,date) values('{$member_data['id']}','$id','".time()."')");
    print "<center>$phrases[add_done_successfully] </center>";
}
close_table();
}
}
//-----------------

    $qr=db_query("select movies_members.* from movies_members,movies_members_black  where movies_members.id = movies_members_black.uid2 and movies_members_black.uid1 ='".$member_data['id']."'");
    open_table("$phrases[black_list]");
    
    print "
    <center>$phrases[black_list_note]</center>
    <br><br>
    
    <form action='usercp.php' method=post>
    <input type='hidden' name='action' value='black_list_add'>
    <fieldset>
    <legend>$phrases[add_member]</legend>
    <input type='text' name='username' size=10><input type='submit' value='$phrases[add]'>
    </fieldset>
    </form><br><br>";
    
    
    if(db_num($qr)){
        
        print "
        <table width=100%>";
        while($data=db_fetch($qr)){
          print "<tr><td align=center><a href=\"".str_replace("{id}",$data['id'],$links['links_profile'])."\"><img title=\"$data[username]\" src=\"".get_image($data['thumb'],$style['images']."/profile_no_pic_thumb".iif($data['gender'],"_".$data['gender']).".gif")."\" border=0></a></td>
          <td width='100%'><a href=\"".str_replace("{id}",$data['id'],$links['links_profile'])."\" title=\"$data[username]\">$data[username]</a></td><td align=left><a href='usercp.php?action=black_list_del&id=$data[id]' onClick=\"return confirm('{$phrases['are_you_sure']}');\"><img src='images/del.gif' border=0 title='$phrases[delete]'></a></td></tr>";  
        }
    print "</table>";
    }else{
        print "<center>  $phrases[no_members_in_list] </center>";
    }
    
    close_table();
    
}


 }else{
 login_redirect();   

 }
 
 //---------------------------------------------
require(CWD . "/includes/framework_end.php"); 
?>
