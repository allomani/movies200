<?
include "global.php";
if(!$action){
$qr=db_query("select id,url,url_watch,cat from movies_files where id='$id'");

          if (db_num($qr)){
                   $data=db_fetch($qr);
    $data_cat = db_qr_fetch("select movies_cats.download_for_members,movies_cats.watch_for_members from movies_cats,movies_data where movies_data.id='$data[cat]' and movies_cats.id=movies_data.cat");

//------- Watch ---------------
 if ($op == "view"){
 
 //-------------------------
 if($data_cat['watch_for_members']){
 if(check_member_login()){ 
     $continue = 1;
 }else{
     $continue = 0;
    login_redirect();
 }
 }else{
     $continue= 1;
 }
 
 //---------------------
 
 
if($continue){     
     $url = iif($data['url_watch'],$data['url_watch'],$data['url']);
    $url =iif(strchr($url,"://"),$url,"$scripturl/$url");
 
    $player_data = get_player_by_id($player_id);
    if(is_array($player_data)){ 
    
         db_query("update movies_files set views=views+1 where id='$id'");
        
header("Content-type: $player_data[ext_mime]");
header("Content-Disposition:  filename=$player_data[ext_filename]");
header("Content-Description: PHP Generated Data");

    compile_template(str_replace("{url}",$url,$player_data['ext_content']));
    }else{
        print "<center> Error : No Player </center>";
    }
}
 //--------- Download ------------
  }else{
      
  
      //-------------------------
      
 if($data_cat['download_for_members']){
 if(check_member_login()){ 
     $continue = 1;
 }else{
     $continue = 0;
    login_redirect();
 }
 }else{
     $continue= 1;
 }
 
 //---------------------
 
 
  if($continue){                    
         db_query("update movies_files set downloads=downloads+1 where id='$id'");
         $url =iif(strchr($data['url'],"://"),$data['url'],"$scripturl/{$data['url']}");  
        header("Location: $url");
  }       

          }
//----------------------------------------

        }else{

                print "<center> $phrases[err_wrong_url] </center>";
                }
//------------------- Subtitles Download ----------------
}elseif($action=="subtitle"){
 $qr=db_query("select id,url from movies_subtitles where id='$id'");

          if (db_num($qr)){
          $data=db_fetch($qr);
          
            db_query("update movies_subtitles set downloads=downloads+1 where id='$id'");   
            
                                                       
         $url =iif(strchr($data['url'],"://"),$data['url'],"$scripturl/{$data['url']}");  
         header("Location: $url");
        }else{

                print "<center> $phrases[err_wrong_url] </center>";
                }
}
                ?>
