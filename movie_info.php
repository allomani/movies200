<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------

      

 $qr = db_query("select * from movies_data where id='$id'");
 if(db_num($qr)){
         $data = db_fetch($qr);

      db_query("update movies_data set views=views+1 where id='$id'");
      
      
       print_path_links($data['cat'],$data['name']);
       
         open_table($data['name']);
         
         //----- get files and subtitles count ------//
         $files_cnt = db_qr_fetch("select count(id) as count,id from movies_files where cat='$data[id]'");
         $movie_files_count = (int) $files_cnt['count'];
         $subtitles_cnt = db_qr_fetch("select count(id) as count from movies_subtitles where cat='$data[id]'");
         $movie_subtitles_count = (int) $subtitles_cnt['count']; 
         unset($subtitles_cnt);
         //----------------------------------------//
         
        
     //----- cutom -------
          unset($custom_fields);
          $qrf = db_query("select * from movies_fields_sets order by ord");
   if(db_num($qrf)){
    
       
while($dataf = db_fetch($qrf)){
    if($data["field_".$dataf['id']]){
        
    $custom_fields[] = array("id"=>$dataf['id'] ,"name"=> $dataf['name'],"value"=>  $data["field_".$dataf['id']],"search"=>$dataf['enable_search']); 
  }

}
   }
   $custom_fields = (array) $custom_fields;
//-------------------
   if($data['votes_total'] > 0 && $data['votes'] > 0){
            $rating = $data['votes']/$data['votes_total'] ;
        }else{
            $rating = 0 ;
        }
//---------------- director ------
unset($director);
          if($data['director']){
           if(is_numeric($data['director'])){
           $datad = db_qr_fetch("select id,name from movies_actors where id='$data[director]'");    
           $director['id'] = $datad['id'];
           $director['name'] = $datad['name'];   
              }else{
             $director['name'] = "$data[director]";
              }
 }
 //----------------------------------
 
   
   run_template('movie_info');
   
         close_table();
       
       print "<div id='snd2friend_loading_div' style=\"display:none;\"><img src='$style[images]/loading.gif'></div>"; 
        
        print "<div id='snd2friend_div'></div>";
        
        
       print "<div id='files_list_loading_div' style=\"display:none;\"><img src='$style[images]/loading.gif'></div>";
       print "<div id='files_list_div'>";
         if(!$settings['movie_files_list_ajax'] && $movie_files_count > 1){
        $files_limit = (int) $settings['movie_files_list_max'];
        $files_limit = iif($files_limit,$files_limit,10);
        get_movie_files_list($id,$files_limit);    
        }
        print "</div>"; 
         
        print "<div id='player_loading_div' style=\"display:none;\"><img src='$style[images]/loading.gif'></div>"; 
        print "<div id='player_div'></div>";
        
       
        print "<div id='subtitles_list_loading_div' style=\"display:none;\"><img src='$style[images]/loading.gif'></div>";
       print "<div id='subtitles_list_div'>";
         if(!$settings['movie_subtitles_list_ajax'] && $movie_subtitles_count){
        $files_limit = (int) $settings['movie_subtitles_list_max'];
        $files_limit = iif($files_limit,$files_limit,10);
        get_movie_subtitles_list($id,$files_limit);    
        }
        print "</div>"; 
        
        
         
//--------- Actors --------//
$qr = db_query("select movies_actors.* from movies_actors,movies_actors_index where movies_actors.id = movies_actors_index.actor_id and movies_actors_index.movie_id='$id' order by movies_actors_index.ord limit ".intval($settings['movie_actors_max']));
if(db_num($qr)){
    open_table("$phrases[the_actors]");
    print "<table width=\"100%\"><tr>";
    $c = 0;
    while($data=db_fetch($qr)){
    
    if($c==$settings['movie_actors_cells']){print "</tr><tr>";$c=0;}
    
        print "<td align=center>
        <a href=\"".str_replace("{id}",$data['id'],$links['links_actor_details'])."\" title=\"$data[name]\">
        <img src=\"".get_image($data['thumb'])."\" title=\"$data[name]\"><br>$data[name]</a></td>";
        
        $c++;
    }
    print "</tr></table>
      <p align='$global_align_x'><a href=\"".str_replace("{id}",$id,$links['links_movie_actors'])."\">$phrases[more]</a></p>";
    
    close_table();
}
         
// ------ photos -------- //
         $qr = db_query("select * from movies_photos where cat='$id' order by ord asc limit ".intval($settings['movie_photos_max']));
         if(db_num($qr)){

         open_table($phrases['movie_pictures']);
         print "<center><table width='100%'><tr>";
         $c=0 ;
         while($data = db_fetch($qr)){
             
          if ($c==$settings['movie_photos_cells']) {print "  </tr><tr>" ;$c = 0 ;}
 
  print "<td align=center><a href=\"".str_replace("{id}",$data['id'],$links['links_movie_photo'])."\"><img border=0 src='".get_image($data['thumb'])."'></a></td>" ;
          
     $c++ ;    
         }
           
                 print "</tr></table>
                 <p align='$global_align_x'><a href=\"".str_replace("{id}",$id,$links['links_movie_photos'])."\">$phrases[more]</a></p>";
         close_table();
                 }
  //--------- end photos ------------//

  
  //------ Comments -------------------
if($settings['enable_comments']){
    open_table($phrases['members_comments']);
    get_comments_box('movie',$id);
    close_table();
}


         }else{
          open_table();
         print "<center> $phrases[err_wrong_url]</center>";
         close_table();
         }
         
 



//---------------------------------------------
require(CWD . "/includes/framework_end.php"); 
?>