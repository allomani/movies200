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

function set_meta_values(){
       
global $sitename,$phrases,$start,$settings,$keyword,$action,$id,$op,$cat,$section_name,$sec_name,$meta_description,$meta_keywords,$title_sub;
 
                 
//------ Movie Info ---------------
if(CFN == "movie_info.php" && $id){
$qr = db_query("select movies_data.name,movies_data.page_title,movies_data.page_description,movies_data.page_keywords,movies_data.year,movies_cats.name as cat from movies_data,movies_cats where movies_cats.id=movies_data.cat and movies_data.id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$data['year'] = iif($data['year'] > 0 , $data['year'] , ""); 


$meta = get_meta_values('movie_info');
$to_find = array("{name}","{cat}","{year}");
$to_replace = array($data['name'],$data['cat'],$data['year']);


$title_sub = iif($data['page_title'],$data['page_title'],str_replace($to_find,$to_replace,$meta['title'])) ;
$meta_description = iif($data['page_description'],$data['page_description'],str_replace($to_find,$to_replace,$meta['description']));
$meta_keywords = iif($data['page_keywords'],$data['page_keywords'],str_replace($to_find,$to_replace,$meta['keywords']));

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }
 
 
 //------ Movie Info ---------------
if((CFN == "movie_watch.php" || CFN == "watch.php") && $id){
    
$qr = db_query("select movies_files.name ,movies_files.url,movies_data.name as movie_name,movies_data.year,movies_cats.name as cat_name from movies_data,movies_cats,movies_files where movies_data.id=movies_files.cat and movies_cats.id=movies_data.cat and movies_files.id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;


$data['year'] = iif($data['year'] > 0 , $data['year'] , ""); 


$meta = get_meta_values('movie_watch');
$to_find = array("{file}","{movie}","{cat}","{year}");
$name = iif($data['name'],$data['name'],basename($data['url']));
$to_replace = array($name,$data['movie_name'],$data['cat_name'],$data['year']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }
 
 
//------ Movie Photos ---------------
if(CFN == "movie_photos.php"){
if($id){
    
//-------------- movie photo -------------------
$qr = db_query("select movies_photos.name,movies_data.name as movie_name,movies_data.year,movies_cats.name as cat_name from movies_data,movies_cats,movies_photos where movies_data.id=movies_photos.cat and movies_cats.id=movies_data.cat and movies_photos.id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$data['year'] = iif($data['year'] > 0 , $data['year'] , ""); 


$meta = get_meta_values('movie_photo');
$to_find = array("{id}","{name}","{movie}","{cat}","{year}");
$name = iif($data['name'],$data['name'],$id);
$to_replace = array($id,$name,$data['movie_name'],$data['cat_name'],$data['year']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }
}else{
    if($cat){
 //---------- movie photos -------------------
 $qr = db_query("select movies_data.name as movie_name,movies_data.year,movies_cats.name as cat_name from movies_data,movies_cats where  movies_cats.id=movies_data.cat and movies_data.id='$cat'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$data['year'] = iif($data['year'] > 0 , $data['year'] , "");

$meta = get_meta_values('movie_photos');
$to_find = array("{movie}","{cat}","{year}");
$to_replace = array($data['movie_name'],$data['cat_name'],$data['year']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 //-------------------------------
    }else{
    $title_sub = "$phrases[movie_photos]" ;
 $meta_description = "";
 $meta_keywords = "";       
    }
}
 }
 
//------ Movie Actors ---------------
if(CFN == "movie_actors.php"){

 $qr = db_query("select movies_data.name as movie_name,movies_data.year,movies_cats.name as cat_name from movies_data,movies_cats where movies_cats.id=movies_data.cat and movies_data.id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$data['year'] = iif($data['year'] > 0 , $data['year'] , "");

$meta = get_meta_values('movie_actors');
$to_find = array("{movie}","{cat}","{year}");
$to_replace = array($data['movie_name'],$data['cat_name'],$data['year']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }

}
 
 
 
 //------ Movie Files ---------------
if(CFN == "movie_files.php"){

 $qr = db_query("select movies_data.name as movie_name,movies_data.year,movies_cats.name as cat_name from movies_data,movies_cats where movies_cats.id=movies_data.cat and movies_data.id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$data['year'] = iif($data['year'] > 0 , $data['year'] , "");

$meta = get_meta_values('movie_files');
$to_find = array("{movie}","{cat}","{year}");
$to_replace = array($data['movie_name'],$data['cat_name'],$data['year']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }

}


 //------ Movie Subtitles ---------------
if(CFN == "movie_subtitles.php"){

 $qr = db_query("select movies_data.name as movie_name,movies_data.year,movies_cats.name as cat_name from movies_data,movies_cats where movies_cats.id=movies_data.cat and movies_data.id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$data['year'] = iif($data['year'] > 0 , $data['year'] , "");

$meta = get_meta_values('movie_subtitles');
$to_find = array("{movie}","{cat}","{year}");
$to_replace = array($data['movie_name'],$data['cat_name'],$data['year']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);

 }else{
  $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }

}

//------ Categories ---------------
if(CFN == "browse.php"){

if($cat){    
$qr = db_query("select name,page_title,page_description,page_keywords from movies_cats where id='$cat'");

if(db_num($qr)){
$data = db_fetch($qr) ;

    $start =  (int) $start;
    $movies_perpage = intval($settings['movies_perpage']);    
    
    
if($start){
    $page_number = str_replace("{x}",intval(($start / $movies_perpage) + 1),$phrases['page_number_x']);
    $sp = " - ";
}else{
    $page_number = "";
    $sp = "";
}


$meta = get_meta_values('cats'); 
$to_find = array("{name}","{page}","{sp}");
$to_replace = array($data['name'],$page_number,$sp);


$title_sub = iif($data['page_title'],$data['page_title'],str_replace($to_find,$to_replace,$meta['title'])) ;
$meta_description = iif($data['page_description'],$data['page_description'],str_replace($to_find,$to_replace,$meta['description']));
$meta_keywords = iif($data['page_keywords'],$data['page_keywords'],str_replace($to_find,$to_replace,$meta['keywords']));
        }else{
 $title_sub = "" ;
 $meta_description = "";
 $meta_keywords = "";
 }
}else{
    $title_sub = $phrases['the_movies'] ;
 $meta_description = "";
 $meta_keywords = "";
}
 }

 //------ News Title ---------------
if(CFN == "news.php"){
if($id){
$qr = db_query("select title from movies_news where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$meta = get_meta_values('news'); 
$to_find = array("{name}");
$to_replace = array($data['title']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);


        }else{
 $title_sub = "$phrases[the_news]" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }else{
     

    $start =  (int) $start;
    $news_perpage = intval($settings['news_perpage']);    
    
    
if($start){
    $page_number = str_replace("{x}",intval(($start / $news_perpage) + 1),$phrases['page_number_x']);
    $sp = " - ";
}else{
    $page_number = "";
    $sp = "";
}

if($cat){$data  = db_qr_fetch("select name from movies_news_cats where id='$cat'");}
if(!$data['name']){$data['name'] = $phrases['the_news'];}

$meta = get_meta_values('news_cats'); 
$to_find = array("{name}","{page}","{sp}");
$to_replace = array($data['name'],$page_number,$sp);    


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']); 
     
 }
 
}
 
 
  //------ Profile ---------------
if(CFN == "profile.php"){
$qr = db_query("select username from movies_members where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$meta = get_meta_values('member_profile'); 
$to_find = array("{name}");
$to_replace = array($data['username']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);


        }else{
 $title_sub = "$phrases[the_profile]" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }
 
 
  //------ Actor Details ---------------
if(CFN == "actor_details.php"){
$qr = db_query("select name from movies_actors where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr) ;

$meta = get_meta_values('actor_details'); 
$to_find = array("{name}");
$to_replace = array($data['name']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);


        }else{
 $title_sub = "$phrases[the_actors]" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }
      
   //------ Actor Photo ---------------
if(CFN == "actor_photos.php"){   
    
if($cat){    
$qr = db_query("select name from movies_actors where id='$cat'");
}else{
 $qr = db_query("select movies_actors.name from movies_actors,movies_actors_photos where movies_actors.id=movies_actors_photos.cat and movies_actors_photos.id='$id'");
   
}


if(db_num($qr)){
$data = db_fetch($qr) ;
if($cat){  
$meta = get_meta_values('actor_photos'); 
}else{
    $meta = get_meta_values('actor_photo');  
}

$to_find = array("{name}","{id}");
$to_replace = array($data['name'],$id);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);


        }else{
 $title_sub = "$phrases[actor_photos]" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }
 
 
//------ pages ---------------
if(CFN == "index.php" && $action=="pages"){   
    

$qr = db_query("select title from movies_pages where id='$id'");



if(db_num($qr)){
$data = db_fetch($qr) ;

    $meta = get_meta_values('pages');  


$to_find = array("{name}");
$to_replace = array($data['title']);


$title_sub = str_replace($to_find,$to_replace,$meta['title']) ;
$meta_description = str_replace($to_find,$to_replace,$meta['description']);
$meta_keywords = str_replace($to_find,$to_replace,$meta['keywords']);


        }else{
 $title_sub = "$phrases[the_pages]" ;
 $meta_description = "";
 $meta_keywords = "";
 }
 }

//-------- Statistics -----------
if(CFN == "statistics.php"){
    $title_sub = $phrases['the_statics'] ;  
     $meta_description = "";
 $meta_keywords = "";
}


//-------- Profile -----------
if(CFN == "usercp.php" && $action=="profile"){
    $title_sub =   "$phrases[the_profile]";
}

    
//-------- Messages -----------
if(CFN == "messages.php"){
if($action=="new"){
    $title_sub =  $phrases['send_new_msg'];
}elseif($action=="sent"){
      $title_sub = $phrases['sent_messages'] ;
}else{
    $title_sub = $phrases['received_msgs'] ;  
}
     $meta_description = "";
 $meta_keywords = "";
}


 
   //------ Search Title ---------------
if(CFN == "search.php"){

$title_sub = $phrases['the_search'] ;
$meta_description = "";
$meta_keywords = "";
 }
 
//------ Actors ---------------
if(CFN == "actors.php"){

    $start =  (int) $start;
    $actors_perpage = intval($settings['actors_per_page']);    
    
    
if($start){
    $page_number = str_replace("{x}",intval(($start / $actors_perpage) + 1),$phrases['page_number_x']);
    $sp = " - ";
}else{
    $page_number = "";
    $sp = "";
}

$meta = get_meta_values('actors'); 
$to_find = array("{page}","{sp}");
$to_replace = array($page_number,$sp);


$title_sub = iif($data['page_title'],$data['page_title'],str_replace($to_find,$to_replace,$meta['title'])) ;
$meta_description = iif($data['page_description'],$data['page_description'],str_replace($to_find,$to_replace,$meta['description']));
$meta_keywords = iif($data['page_keywords'],$data['page_keywords'],str_replace($to_find,$to_replace,$meta['keywords']));


 }
 
 
 //------ Contact us ---------------
if(CFN == "contactus.php"){

$title_sub = $phrases['contact_us'] ;
$meta_description = "";
$meta_keywords = "";
 }
 
 
  //------ Search Title ---------------
if(CFN == "votes.php"){

$title_sub = $phrases['the_votes'] ;
$meta_description = "";
$meta_keywords = "";
 }
 
 
//-------------------------------------
//if($section_name){
//$sec_name = " -  $section_name" ;
       // }
if(!$meta_description){ $meta_description= $settings['header_description']." , ".iif($title_sub,$title_sub,$sitename);}
if(!$meta_keywords){$meta_keywords = iif($settings['header_keywords'],$settings['header_keywords'],$settings['header_description'])." , ".iif($title_sub,$title_sub,$sitename); }  

}



function get_meta_values($name){
    $data = db_qr_fetch("select * from movies_meta where name like '".db_escape($name)."'");
    return $data;
}

?>
