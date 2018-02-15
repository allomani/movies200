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

chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
$is_admin =1 ;

require(CWD . "/global.php") ;
header("Content-Type: text/html;charset=$settings[site_pages_encoding]");

if(!check_admin_login()){die("<center> $phrases[access_denied] </center>");}  


//----- Set Blocks Sort ---------//
if($action=="set_blocks_sort"){
 //   file_put_contents("x.txt","d".$data[0]); 
 if_admin();
if(is_array($blocks_list_r)){
$sort_list = $blocks_list_r ;
$pos="r";
}elseif(is_array($blocks_list_c)){
$sort_list = $blocks_list_c ;
$pos="c";
}else{
$sort_list = $blocks_list_l ;
$pos="l";
}
 
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
    db_query("UPDATE movies_blocks SET ord = '$i',pos='$pos' WHERE `id` = $sort_list[$i]");
 }
}
 }
 
 //------------ Set Banners Sort ---------------
if($action=="set_banners_sort"){
    if_admin("adv");
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
    db_query("UPDATE movies_banners SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}


 

 //---------  New Movies  Sort ------------
if($action=="set_new_movies_list_sort"){
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
// if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE movies_new_menu SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

//---------  Movie Files Sort ------------
if($action=="set_movie_files_list_sort"){
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
// if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE movies_files SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

//---------  Movie subtitles Sort ------------
if($action=="set_movie_subtitles_sort"){
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
// if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE movies_subtitles SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

       
 //---------  Cats  Sort ------------
if($action=="set_cats_sort"){
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
// if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE movies_cats SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

 //---------  news cats  Sort ------------
if($action=="set_news_cats_sort"){
    if_admin("news");  
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE movies_news_cats SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}

 //---------Movie Actors Sort ------------
if($action=="set_movie_actors_sort"){
  
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE movies_actors_index SET ord = '$i' WHERE `actor_id` = '".$sort_list[$i]."'");
 }
}
}


 //---------Movie Photos Sort ------------
if($action=="set_movie_photos_sort"){
  
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE movies_photos SET ord = '$i' WHERE `id` = '".$sort_list[$i]."'");
 }
}
}


 //---------Actors Photos Sort ------------
if($action=="set_actor_photos_sort"){
  
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) {  
   
    db_query("UPDATE movies_actors_photos SET ord = '$i' WHERE `id` = '".$sort_list[$i]."'");
 }
}
}



//---------  Movie Fields Sort ------------
if($action=="set_movies_fields_sort"){
if_admin("movies_fields");

if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
// if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE movies_fields_sets SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}



//---------  members fields Sort ------------
if($action=="set_members_fields_sort"){
    if_admin("members");
    
if(is_array($sort_list)){
 for ($i = 0; $i < count($sort_list); $i++) { 
// if_products_cat_admin($sort_list[$i]);  
 
    db_query("UPDATE movies_members_sets SET ord = '$i' WHERE `id` = $sort_list[$i]");
 }
}
}



//------ actors get ------
if($action=="get_actor"){


    $qr=db_query("select id,name,thumb from movies_actors where name like '".db_escape($value)."%'");
 print "<ul>";  
    if(db_num($qr)){
     
        
   
    while ($data=db_fetch($qr)){
    print "<li id='$data[id]'><img width=20 height=20 src=\"".get_image($data['thumb'],"../")."\">$data[name]</li>";
    }

    }else{
        print "<li id='0'><span class=\"informal\">$phrases[not_available]</span></li>";
    }
     
    print "</ul>"  ;  
    
}



//------ actors get ------
if($action=="get_actors_json"){


    $qr=db_query("select id,name from movies_actors");
 
    if(db_num($qr)){
     
             //"<img src=\"$scripturl/".get_image($data['img'])."\" width=20 height=20>".
    $c=0;
    while ($data=db_fetch($qr)){
    $values[$c] = array("caption"=>$data['name'],"value"=>intval($data['id']));
    $c++;
    }
   print json_encode($values);
    }
     
 
    
}


//------ movies get json------
if($action=="get_movies_json"){


    $qr=db_query("select id,name,img from movies_data");
 
    if(db_num($qr)){
     
              //"<img src=\"$scripturl/".get_image($data['img'])."\" width=20 height=20>".
    $c=0;
    while ($data=db_fetch($qr)){
    $values[$c] = array("caption"=>$data['name'],"value"=>intval($data['id']));
    $c++;
    }
   print json_encode($values);
    }
     
 
    
}



//---------- Uplaoder ----------
if($action=="upload"){
 
 if($settings['uploader']){  
                  
     if($_FILES['datafile']['name'] && $upload_folder_suffix){
      
        $upload_folder = $settings['uploader_path']."/$upload_folder_suffix" ;

                           
     if($upload_folder && file_exists(CWD ."/$upload_folder")){
 
         require_once(CWD. "/includes/class_save_file.php");  
         
         $imtype = file_extension($_FILES['datafile']['name']);

if(in_array($imtype,$upload_types)){
                   
if($_FILES['datafile']['error']==UPLOAD_ERR_OK){
       
                  
  if(!file_exists($upload_folder."/".$_FILES['datafile']['name'])){$replace_exists=1;}    
      
$fl = new save_file($_FILES['datafile']['tmp_name'],$upload_folder,$_FILES['datafile']['name']);

if($fl->status){
$saveto_filename =  $fl->saved_filename;

$response =  array("status"=>1,"file"=>$saveto_filename); 
 
 if(in_array($imtype,array("jpg","png","gif","jpeg","bmp"))){
     $response['show_resize'] = 1;
 }else{
     $response['show_resize'] =0;  
 }
 
  
}else{
$err = $fl->last_error_description;
$response =  array("status"=>0,"msg"=>$err);    
}


  }else{
$upload_max = convert_number_format(ini_get('upload_max_filesize'));
$post_max = (convert_number_format(ini_get('post_max_size'))/2) ;
$max_size = iif($upload_max < $post_max,$upload_max,$post_max);

   $mx_rs = "Uploading Error , Make Sure that file size is under ".convert_number_format($max_size,2,ture);
  $response =  array("status"=>0,"msg"=>$mx_rs);   
  
  }
}else{
    $response =  array("status"=>0,"msg"=>$phrases['this_filetype_not_allowed']); 
}

         
         
      }else{
           $response = array("status"=>0,"msg"=>$phrases['err_wrong_uploader_folder']); 
      }
      
      
         
         
     }else{
         $response = array("status"=>0,"msg"=>"Missing Data");
     }
     
 }else{
   $response = array("status"=>0,"msg"=>$settings['uploader_msg']);
 }
 
  print json_encode($response);
     
    
}






