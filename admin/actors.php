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

if($action=="actors" ||  $action=="actors_add_ok" || $action=="actors_del"){
  
  if_admin("actors");
print "<img src=\"images/add.gif\"> &nbsp; <a href='index.php?action=actors_add'>$phrases[add_actor]</a><br><br>";
 
   
print "<p align=center class=title>$phrases[the_actors]</p>";



  
//--- del ----
if($action=="actors_del"){
    db_query("delete from movies_actors where id='$id'");
}
 


//---- add -----
if($action=="actors_add_ok"){
$name = trim($name);
     
if($name){
   db_query("insert into movies_actors (name) values ('".db_escape($name)."')");    
$actor_id = mysql_insert_id();

print "<SCRIPT>window.location=\"index.php?action=actors_edit&id=$actor_id\";</script>";

}else{
    print "<SCRIPT>window.location=\"index.php?action=actors_add\";</script>"; 
}
}

//-------------------------------


      //----------------- start pages system ----------------------
 $start=(int) $start;
   $page_string= "index.php?action=actors&start={start}";
   $actors_perpage = intval($settings['actors_per_page']);
   //----------------------------------------------------------
   
   
    
        $qr=db_query("select * from movies_actors order by name asc limit $start,$actors_perpage");
    if(db_num($qr)){
        
           //-------------------
   $page_result = db_qr_fetch("select count(*) as count from movies_actors");  
   //--------------------------------------------------------------
   
   
   
        print "<center><table width=90% class=grid><tr> ";
        $c=0;
        while($data=db_fetch($qr)){
            
            if($c==4){
                print "</tr><tr>";
                $c=0;
            }
            
            $c++;
            print "
            <td align=center>
            <a href='index.php?action=actors_edit&id=$data[id]'><img border=0 src=\"$scripturl/".get_image($data['thumb'])."\">
            <br>$data[name]</a>
            </td>";
        }
        print "</tr></table>";
        
        
//-------------------- pages system ------------------------
print_pages_links($start,$page_result['count'],$actors_perpage,$page_string);
//------------ end pages system ------------- 


    }else{
        print_admin_table("<center> $phrases[no_actors]  </center>");
        
    }
    
    
}

//---- actors edit ------------
if($action=="actors_edit" || $action=="actor_photo_del" || $action=="actors_edit_ok"){
    
  if_admin("actors");
  
  
//----- photo del ------
if($action=="actor_photo_del"){

$qr = db_query("select img,thumb from movies_actors where id='$id'");
if(db_num($qr)){
        $data = db_fetch($qr);

 delete_file($data['img']);
 delete_file($data['thumb']);
 
 db_query("update movies_actors set img='',thumb='' where id='$id'");
 }
 }
 
 //--- edit ----
if($action=="actors_edit_ok"){
    
 $name = trim($name);
 
 if($name){
     
  //----- filter XSS Tages -------
 /*
include_once(CWD . "/includes/class_inputfilter.php");
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);
*/
//------------------------------

   
    db_query("update movies_actors set name='".db_escape($name)."',details='".db_escape($details,false)."' where id='$id'");
    
    print_admin_table("<center> $phrases[edit_done] </center>");

 }
 
 
 //------ update img -----------
if($_FILES['img']['name']){
    require_once(CWD. "/includes/class_save_file.php");   
    
    $upload_folder = $settings['uploader_path']."/actors" ;
    $allowed_types = array("jpg","png","gif","bmp");
    $imtype = file_extension($_FILES['img']['name']);  
    
    if($_FILES['img']['error']==UPLOAD_ERR_OK){  
    if(in_array($imtype,$allowed_types)){ 
        
$fl = new save_file($_FILES['img']['tmp_name'],$upload_folder,$_FILES['img']['name']);

if($fl->status){
$img_saved =  create_thumb($fl->saved_filename,$settings['actor_img_width'],$settings['actor_img_height'],$settings['actor_img_fixed'],'',1,basename($fl->saved_filename)); 
$thumb_saved =  create_thumb($img_saved,$settings['actor_thumb_width'],$settings['actor_thumb_height'],$settings['actor_thumb_fixed'],'thumb');

if(file_exists($img_saved) && file_exists($thumb_saved)){
$old_data = db_qr_fetch("select img,thumb from movies_actors where id='$id'"); 

delete_file($old_data['img']);
delete_file($old_data['thumb']);

db_query("update movies_actors set img='".db_escape($img_saved,false)."',thumb='".db_escape($thumb_saved,false)."' where id='$id'");
  
}else{
    print_admin_table("<center>$phrases[actor_photo_save_error]</center>"); 
}

}else{
print_admin_table("<center><b>$phrases[the_photo] : </b> ".$fl->last_error_description."</center>");  
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

}
 
 //---------------------------------------------
 
$qr = db_query("select * from movies_actors where id='$id'");
if(db_num($qr)){
    $data=db_fetch($qr);
 print "<img src=\"images/arrw.gif\">&nbsp; <a href='index.php?action=actors'>$phrases[the_actors]</a> / $data[name] <br><br>";
 
 
    print "<center>
    
        <table class=grid><tr>
      <td align=center><a href='index.php?action=actor_photos&id=$id'><img src='images/movie_photos_manage.png' border=0><br>$phrases[manage_photos]</a></td> 
      </table><br>
    
    <form action=index.php method=post enctype=\"multipart/form-data\" name=sender>
       <input type=hidden name=action value='actors_edit_ok'>
    <input type=hidden name='id' value='$id'>
  
  
       <table class=grid width=98%>

       <tr><td><b> $phrases[the_name] : </b></td><td><input type=text name=name size=30 value=\"$data[name]\"></td>
       
       <td rowspan=2 align=center>
       ".iif($data['img'],"<a href=\"javascript:enlarge_pic('$scripturl/".$data['img']."','".htmlspecialchars(str_replace(array("?","'"),"",$data['name']))."')\">")."
      <img border=0 src=\"$scripturl/".get_image($data['thumb'])."\" title=\"$data[name]\">".iif($data['img'],"</a>")."
                           
                           <br>
                            
                            
       <a href='index.php?action=actor_photo_del&id=$data[id]' onclick=\"return confirm('$phrases[are_you_sure]')\"> $phrases[delete_cover] </a>
       
       </td>
       </tr>
       
       <tr><td><b> $phrases[the_photo] : </b></td><td>
        <input type=file size=30 name='img'>
         </td></tr>
         
         
          <tr><td colspan=3><b> $phrases[the_details] : </b></td><tr>
          <tr><td colspan=3>";
                editor_print_form("details",600,300,$data['details']);    
          print "</td></tr>
          
       
       <tr><td colspan=3 align=center> <input type=submit value=' $phrases[edit] '></td></tr>
       
         <tr><td colspan=3 align='$global_align_x'><a href='index.php?action=actors_del&id=$data[id]' onClick=\"return confirm('{$phrases['are_you_sure']}');\"> <img src=\"images/delete.gif\" border=0 title=\"$phrases[delete]\"><br>$phrases[delete]</a></td></tr>
       
       </table>
       </form>
      
       <br>";
       
         
         
         
   
}else{
       print_admin_table("<center> $phrases[err_wrong_url] </center>");       
}   
}


//---- actors edit ------------
if($action=="actors_add"){
    
  if_admin("actors");

   print "<img src=\"images/arrw.gif\">&nbsp; <a href='index.php?action=actors'>$phrases[the_actors]</a> / $phrases[add_actor] <br>";
 
 print "<p align=center class=title>$phrases[add_actor]</p>";
    
    
    print "<center>
    <form action='index.php' method=post>
    <input type=hidden name='action' value='actors_add_ok'>

    
    <table width=90% class=grid>
    <tr><td>$phrases[the_name]</td><td><input type=text name='name' size=30></td></tr>
    
    <tr><td colspan=2 align=center><input type=submit value='$phrases[add]'></td></tr>
    </table></center>";
  
}

