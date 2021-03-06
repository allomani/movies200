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

require(CWD . '/includes/class_security_img.php');
$sec_img = new sec_img_verification();


require(CWD . "/includes/framework_start.php");   
//----------------------------------------------------
compile_hook("contactus_start");   

  if($action=="send"){
      
     if($sec_img->verify_string($sec_string)){ 
         
         
  $email_name = htmlspecialchars(trim($email_name));
  $email_email = htmlspecialchars(trim($email_email));
  $email_msg = htmlspecialchars(trim($email_msg));
  $email_subject = htmlspecialchars(trim($email_subject)); 
  
  if(!$email_subject){$email_subject = $phrases['without_subject'];}   
     
if($email_name && $email_email && $email_msg){
    
    if(check_email_address($email_email)){
        
    
 $email_msg = nl2br($email_msg);       
 $ip = getenv("REMOTE_ADDR");
 $msg = get_template('contactus_msg');
 $msg = str_replace(array('{name}','{email}','{message}','{ip}'),array($email_name,$email_email,$email_msg,$ip),$msg);
    
    
    $mailResult = send_email($email_name,$mailing_email,$settings['admin_email'],$email_subject ,$msg,true,'',$email_email);
    open_table();
    if($mailResult){
            print "<center>  $phrases[contactus_send_successfully] </center>";
            }else{
                    print "<center>  $phrases[contactus_send_failed] </center>";
           }
    close_table();
    
    }else{
     open_table();
     print "<center> $phrases[err_email_not_valid] </center>";
     close_table();
    }
    }else{
     open_table();
     print "<center> $phrases[err_fileds_not_complete] </center>";
     close_table();   
    }
            }else{
           open_table(); 
             print  "<center>$phrases[err_sec_code_not_valid]</center>"; 
           close_table();           
            }
    }else{
        
        
run_template("contactus");
        
        
    }
       
compile_hook("contactus_end");   
//---------------------------------------------------
require(CWD . "/includes/framework_end.php"); 

?>
