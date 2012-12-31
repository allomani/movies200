<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------

$qr = db_query("select * from movies_actors where id='$id'");
if(db_num($qr)){
    
    $data = db_fetch($qr);
   
   
   //--- update views ----//
   db_query("update movies_actors set views=views+1 where id='$id'");
   //--------------------//
   
    
    print "<span class='path'><img src=\"$style[images]/arrw.gif\">&nbsp; <a class='path_link' href=\"".$links['links_actors']."\">$phrases[the_actors]</a> / $data[name] <br><br></span>";
    
    open_table($data['name']);
    run_template('actor_details');
    close_table();
  
    // ------ photos -------- //
    
         $qr = db_query("select * from movies_actors_photos where cat='$id' order by ord asc limit ".intval($settings['actor_photos_max']));
         if(db_num($qr)){

         open_table($phrases['actor_photos']);
         print "<center><table width='100%'><tr>";
         $c=0 ;
         while($data = db_fetch($qr)){
          if ($c==$settings['actor_photos_cells']) {
print "  </tr><TR>" ;
$c = 0 ;
}
 ++$c ;
  print "<td align=center><a href=\"".str_replace("{id}",$data['id'],$links['links_actor_photo'])."\"><img border=0 src='".get_image($data['thumb'])."'></a></td>" ;
                 }
              
                 print "</tr></table>
                                 <p align='$global_align_x'><a href=\"".str_replace("{id}",$id,$links['links_actor_photos'])."\">$phrases[more]</a></p>";
         close_table();
                 }
                 
                 
  //--------- movies ------------//
  
   
    
   $qr = db_query("select movies_data.* from movies_data,movies_actors_index where movies_data.id = movies_actors_index.movie_id and movies_actors_index.actor_id='$id' order by movies_data.year desc");
if(db_num($qr)){
   
     open_table($phrases['actor_movies']); 
    $c = 0;
    run_template('movie_actors_header');
    while($data=db_fetch($qr)){
    
   run_template('movie_actors_content');
   
    }
  run_template('movie_actors_footer');
  close_table(); 
}
   
   
  

  
   
    //------ Comments -------------------
if($settings['enable_actor_comments']){
    open_table($phrases['members_comments']);
    get_comments_box('actor',$id);
    close_table();
} 
}else{
      open_table();
      print "<center> $phrases[err_wrong_url]</center>";
      close_table();
}


//---------------------------------------------
require(CWD . "/includes/framework_end.php"); 