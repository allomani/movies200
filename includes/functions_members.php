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


function init_members_connector(){
 global $member_table_tofind,$member_table_toreplace,$members_connector,$member_fields_tofind,$member_fields_toreplace,$search_fields,$required_database_fields_names,$required_database_fields_types;     

if($members_connector['enable']){
$full_connector_path = CWD."/members_connectors/".$members_connector['connector_file'];
}else{
$full_connector_path =  CWD. "/members_connectors/default.php";
}

if(file_exists($full_connector_path)){
    require($full_connector_path);
}else{
 trigger_error("Cannot Open Members Connector File");   
}

}

/*
function members_fields_replace($a,$type="value"){
	global $member_fields_tofind,$member_fields_toreplace ;
//$strfind = ($req_rep_tablename ? $db_table1.".id":"id");
//$strreplace = ($req_rep_tablename ? $db_table.".userid" : "userid");

$nr = array_replace($member_fields_tofind,$member_fields_toreplace,$a);
return $nr;
unset($nr);
}
 */

//----------- check if same connection ---------------
if($db_host ==$members_connector['db_host'] && $members_connector['db_username'] ==$db_username){
$members_connector['same_connection'] =1 ;
}else{
$members_connector['same_connection'] =0 ;
}



function members_table_replace($value){
	global $member_table_tofind,$member_table_toreplace,$members_connector;
	if($members_connector['enable']){
//        print "$member_table_tofind | $member_table_toreplace | $value <br>";
return str_replace($member_table_tofind,$member_table_toreplace,$value);
}else{
  
return $value;
	}
	}


function members_fields_replace($value){
	global $member_fields_tofind,$member_fields_toreplace,$members_connector ;
if($members_connector['enable']){
return str_replace($member_fields_tofind,$member_fields_toreplace,$value);
}else{
return $value;
	}
}

function member_time_replace($time){
global $members_connector;
if($members_connector['time_type']=="timestamp"){
	return date($members_connector['time_format'],$time);
	}else{
		return $time;
		}
		}


//--------------- remote db connect ------------------
function members_remote_db_connect(){
global $members_connector,$db_host,$db_name,$db_username,$db_charset;
if($members_connector['enable']){
if($members_connector['db_name'] != $db_name || $db_host !=$members_connector['db_host'] ||  $members_connector['db_username'] !=$db_username){

//----- connect -----
if($db_host !=$members_connector['db_host'] || $members_connector['db_username'] !=$db_username){
db_connect($members_connector['db_host'],$members_connector['db_username'],$members_connector['db_password'],$members_connector['db_name'],$members_connector['db_charset']);
}else{
if($members_connector['db_name'] != $db_name){
db_select($members_connector['db_name'],$members_connector['db_charset']);
}
}
//-------

}
}
}

function members_local_db_connect(){
global $db_name,$members_connector,$db_host,$db_username,$db_password,$db_charset;

if($members_connector['enable']){
if($members_connector['db_name'] != $db_name || $db_host !=$members_connector['db_host'] ||  $members_connector['db_username'] !=$db_username){

//----- connect -----
if($db_host !=$members_connector['db_host'] || $members_connector['db_username'] !=$db_username){
db_connect($db_host,$db_username,$db_password,$db_name,$db_charset);
}else{
if($members_connector['db_name'] != $db_name){
db_select($db_name,$db_charset);
}
}
//-------

}
}

}


//----------------------------- Members -----------------
$member_data = array();
function check_member_login(){
      global $member_data,$members_connector ;

 $member_data['id'] = get_cookie('member_data_id');
 $member_data['password'] = get_cookie('member_data_password');

   if($member_data['id']){

   $qr = db_query("select * from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='$member_data[id]'",MEMBER_SQL);

         if(db_num($qr)){
           $data = db_fetch($qr);
           if($data[members_fields_replace('password')] == $member_data['password']){

            if(in_array($data[members_fields_replace('usr_group')],$members_connector['allowed_login_groups'])){

               db_query("update ".members_table_replace('movies_members')." set ".members_fields_replace('last_login')."='".connector_get_date(time(),'member_last_login')."' where ".members_fields_replace('id')."='".$member_data['id']."'",MEMBER_SQL);
            $member_data['username'] = $data[members_fields_replace('username')];
            $member_data['email'] = $data[members_fields_replace('email')];
            $member_data['usr_group'] = $data[members_fields_replace('usr_group')];
             $member_data['vip'] = $data[members_fields_replace('vip')];
            
                   return true ;

              }else{
                    unset($member_data);
                      return false ;
                      }
                   }else{
                       unset($member_data);
                           return false ;
                           }

                 }else{
                     unset($member_data);
                         return false ;
                         }

           }else{

                   return false ;
                   }

        }

//----------- members custom fields ----------

function get_member_field($name,$data,$value="",$search=false){
      global $phrases;

    $cntx = "" ;

//----------- text ---------------
if($data['type']=="text"){

$cntx .= "<input type=text name=\"$name\" value=\"".iif($search,"",iif($value,$value,$data['value']))."\" $data[style]>";  

//---------- text area -------------
}elseif($data['type']=="textarea"){

$cntx .= "<textarea name=\"$name\" $data[style]>".iif($search,"",iif($value,$value,$data['value']))."</textarea>"; 

//-------- select -----------------
}elseif($data['type']=="select"){
  
     $cntx .= "<select name=\"$name\" $data[style]>";
        if($search){ $cntx .= "<option value=\"\">$phrases[without_selection]</option>";}

        $vx  = explode("\n",$data['value']);
        foreach($vx as $value_f){
              $value_f=trim($value_f);
        $cntx .= "<option value=\"$value_f\"".iif($value==$value_f," selected").">$value_f</option>";
            }
        $cntx .= "</select>";

//--------- radio ------------
}elseif($data['type']=="radio"){

        if($search){ $cntx .= "<input type=\"radio\" name=\"$name\" value=\"\" $data[style] checked>$phrases[without_selection]<br>";}

     
        $vx  = explode("\n",$data['value']);
        foreach($vx as $value_f){
        $cntx .= "<input type=\"radio\" name=\"$name\" value=\"$value_f\" $data[style] ".iif($value==$value_f," checked")."> $value_f<br>";
            }

//-------- checkbox -------------
}elseif($data['type']=="checkbox"){


        $vx  = explode("\n",$data['value']);
        foreach($vx as $value_f){
       
        $cntx .= "<input type=\"checkbox\" name=\"$name\" value=\"$value_f\" ".iif($value==$value_f,"checked").">$value_f<br>";
            }
        }
return $cntx;
}



//--------------- Account Activation Email --------------------
function snd_email_activation_msg($id){
               global $sitename,$mailing_email,$script_path,$settings,$siteurl,$scripturl,$phrases,$settings;

  $qr = db_query("select * from ".members_table_replace('movies_members')." where ".members_fields_replace('id')."='$id'",MEMBER_SQL);
  if(db_num($qr)){
  $data = db_fetch($qr);

  $active_code = md5(rand(0,999).time().$data[members_fields_replace('email')].rand().$id) ;

   db_query("delete from movies_confirmations where type='validate_email' and cat='".$data[members_fields_replace('id')]."'");
     db_query("insert into movies_confirmations (type,cat,code) values('validate_email','".$data[members_fields_replace('id')]."','$active_code')");

     $url = $scripturl."/index.php?action=activate_email&code=$active_code" ;

     $msg = get_template('email_activation_msg',array('{name}','{url}','{code}','{siteurl}','{sitename}'),
     array($data[members_fields_replace('username')],$url,$active_code,$siteurl,$sitename));

    send_email($sitename,$mailing_email,$data[members_fields_replace('email')],$phrases['email_activation_msg_subject'],$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
  }
  }
  
//--------------- Change Email Confirmation --------------------
function snd_email_chng_conf($username,$email,$active_code){
               global $sitename,$mailing_email,$script_path,$settings,$phrases,$sitename,$siteurl,$scripturl;

    $active_link = $scripturl."/index.php?action=confirmations&op=member_email_change&code=$active_code" ;


   $msg =  get_template("email_change_confirmation_msg",array('{username}','{active_link}','{sitename}','{siteurl}'),array($username,$active_link,$sitename,$siteurl));


    $mailResult = send_email($sitename,$mailing_email,$email,$phrases['chng_email_msg_subject'],$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);
}

//--------------- Forgot Password Message ---------------------
function snd_usr_info($email){
  global $sitename,$mailing_email,$sitename,$siteurl,$phrases;
   $msg =  get_template("forgot_pwd_msg");

   $qr=db_query("select ".members_fields_replace('username').",".members_fields_replace('password').",".members_fields_replace('last_login')." from  ".members_table_replace('movies_members')." where ".members_fields_replace('email')."='$email'",MEMBER_SQL);
       if(db_num($qr)){
     $data = db_fetch($qr);

   $msg = str_replace("{username}",$data['username'],$msg);
   $msg = str_replace("{password}",$data['password'],$msg);
   $msg = str_replace("{last_login}",$data['last_login'],$msg);
  $msg = str_replace("{sitename}",$sitename,$msg);
  $msg = str_replace("{siteurl}",$siteurl,$msg);


     $mailResult = send_email($sitename,$mailing_email,$email,$phrases['forgot_pwd_msg_subject'],$msg,$settings['mailing_default_use_html'],$settings['mailing_default_encoding']);

    return true ;
    }else{
            return false ;
            }
          }  
          
$privacy_settings_cache = array();          
//------- privacy settings ---------------
function get_privacy_settings($uid1,$uid2,$privacy_data,$msg="",$show_msg=false){
  global $privacy_settings_cache;  
/*   
if($uid1 == $uid2){
return true;
}else{ */   
if(isset($privacy_settings_cache[$uid1][$uid2]['black'])){
    $prv_data['count'] =  $privacy_settings_cache[$uid1][$uid2]['black'];
}else{
$prv_data = db_qr_fetch("select count(*) as count from movies_members_black where uid1='$uid1' and uid2='$uid2'");
$privacy_settings_cache[$uid1][$uid2]['black'] =  $prv_data['count'];
}

 if($prv_data['count']){
 if($show_msg){
 open_table();
 print "<center> $msg </center>";  
 close_table(); 
 } 
 return false;  
 }else{
  
if($privacy_data == 0){
   return true;
}elseif($privacy_data == 1){
    

if(isset($privacy_settings_cache[$uid1][$uid2]['friend'])){
    $prv_data['count'] =  $privacy_settings_cache[$uid1][$uid2]['friend'];
}else{
$prv_data = db_qr_fetch("select count(*) as count from movies_members_friends where uid1='$uid1' and uid2='$uid2'"); 
$privacy_settings_cache[$uid1][$uid2]['friend'] =  $prv_data['count'];
}


 
 if($prv_data['count']){
     
 return true;
 
 }else{
 if($show_msg){     
  open_table();
 print "<center> $msg </center>";
 close_table();
 }
  
  
  return false;
  
  
 } 
 
  
}else{
 if($show_msg){     
  open_table();
 print "<center> $msg </center>";
 close_table();
 }
  
  
  return false;    
}
}
//}
}

//--------- Login Redirection -----------
function login_redirect($head=false){
    
    global $phrases;
if($head){
    run_template('page_head');
}

print "<form action=index.php method=post name=lg_form>
<input type=hidden name=action value='login'>
 <input type=hidden name='re_link' value=\"".htmlspecialchars("http://$_SERVER[HTTP_HOST]"."$_SERVER[REQUEST_URI]")."\">
 $phrases[redirection_msg] <input type=submit value='$phrases[click_here]'> 
 </form>
 
 <script>
 document.forms['lg_form'].submit();
 </script>";
 } 
