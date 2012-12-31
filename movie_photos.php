<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------
if($id){

//---------- view photo ---------------------

$qr = db_query("select * from movies_photos where id='$id'");
 if(db_num($qr)){
         $data = db_fetch($qr);
$data_movie = db_qr_fetch("select * from movies_data where id='$data[cat]'");
    
     
 
 //--- update views ----//
   db_query("update movies_photos set views=views+1 where id='$id'");
   //--------------------//
   
   
//--- data photos array----
 $qrc = db_query("select id,thumb from movies_photos where cat='$data[cat]' order by ord");
 $i=0;
 unset($list,$cur_index);
while($datac=db_fetch($qrc)){
    $list[$i]['thumb'] = $datac['thumb']; 
    $list[$i]['id'] = $datac['id']; 
    if($datac['id']==$id){$cur_index = $i;}
    $i++;
}
$prev_index = $cur_index - 1;
$next_index = $cur_index + 1;
//-------------------------


  print_path_links($data_movie['cat'],"<a class='path_link' href=\"".str_replace("{id}","$data_movie[id]",$links['links_movie_info'])."\">$data_movie[name]</a>" ." / ". "<a class='path_link' href=\"".str_replace("{id}","$data_movie[id]",$links['links_movie_photos'])."\">$phrases[movie_photos]</a>" . " / $phrases[the_photo] ".($cur_index+1)."/".count($list));
 
 
      
       
       open_table();
       
      print "<table width=100%><tr>
       <td width='33%' align='$global_align'>";
       
       if($list[$prev_index]['id']){ 
       print "<a href=\"".str_replace("{id}",$list[$prev_index]['id'],$links['links_movie_photo'])."\"><img src=\"images/arrw_$global_align.gif\" title=\"$phrases[prev_photo]\" border=0><br>
       $phrases[prev_photo]</a>";
       }
       
       print "</td>
       <td width='33%' align='center'><a href=\"$data[img]\" target=_blank>
       <img src=\"images/full_size.gif\" title=\"$phrases[full_photo_size]\"><br>$phrases[full_photo_size]</a></td>  
       <td width='33%' align='$global_align_x'>";
    
     if($list[$next_index]['id']){ 
         $next_url = str_replace("{id}",$list[$next_index]['id'],$links['links_movie_photo']);
       print "<a href=\"".$next_url."\"><img src=\"images/arrw_$global_align_x.gif\" title=\"$phrases[next_photo]\" border=0><br>
       $phrases[next_photo]</a>";
     }else{
      $next_url = str_replace("{id}",$list[0]['id'],$links['links_movie_photo']); 
     }
     
     
       print "</td>  
       </tr></table><br><br>";
       
       if($data['votes_total'] > 0 && $data['votes'] > 0){
            $rating = $data['votes']/$data['votes_total'] ;
        }else{
            $rating = 0 ;
        }
        
         
       print "<center><a href=\"javascript:;\" onClick=\"window.location=scripturl+'/$next_url';\"><img src=\"$data[img_resized]\" title=\"$data[name]\" border=0></a><br><br>";
       print_rating('movie_photo',$data['id'],$rating);
       print "</center>";
       
       
       print iif($data['name'],"<br><br> <img src='$style[images]/info.gif'> &nbsp; $data[name]");
       
        print "<br>
        <img src='$style[images]/add_date.gif'> &nbsp; <b>$phrases[add_date] : </b>".date($settings['date_format'],$data['date'])."<br>";
        print "<img src='$style[images]/views.gif'> &nbsp; <b>$phrases[views] : </b>$data[views]"; 
        
        
        
       $in_this_photo = "";
        $qra = db_query("select movies_actors.*  from movies_actors,movies_photos_tags where movies_actors.id=movies_photos_tags.actor_id and movies_photos_tags.photo_id='$id' and movies_photos_tags.actor_id > 0 order by movies_actors.name asc");
        while($dataa = db_fetch($qra)){
                $in_this_photo .= iif($in_this_photo," , ")."<a href=\"".str_replace("{id}",$dataa['id'],$links['links_actor_details'])."\" title=\"$dataa[name]\">$dataa[name]</a>";
        }
       
       
        $qra = db_query("select *  from movies_photos_tags where photo_id='$id' and actor_id='0' order by name asc");
        while($dataa = db_fetch($qra)){
                 $in_this_photo .= iif($in_this_photo," , ")."$dataa[name]"; 
        }
        
      
         print iif($in_this_photo,"<br><br> <img src='$style[images]/in_this_photo.gif'> &nbsp; <b>$phrases[in_this_photo] : </b> $in_this_photo");     
         
       
         
       print "<br><br>";
       close_table();
      
      //------ Comments -------------------
if($settings['enable_photo_comments']){
    open_table($phrases['members_comments']);
    get_comments_box('photo',$id);
    close_table();
}

 
   }else{
          open_table();
         print "<center> $phrases[err_wrong_url]</center>";
         close_table();
         }
    
}elseif($cat){
 
      
      
      
//-------- show movies photos --------------------
  
   $qr = db_query("select * from movies_photos where cat='$cat' order by ord asc");
         if(db_num($qr)){
             
            $data_movie = db_qr_fetch("select id,name,cat from movies_data where id='$cat'");
             
   print_path_links($data_movie['cat'],"<a class='path_link' href=\"".str_replace("{id}","$data_movie[id]",$links['links_movie_info'])."\">$data_movie[name]</a>" ." / ". "$phrases[movie_photos]");
    
    
    
         open_table($phrases['movie_pictures']);
         print "<center><table width='100%'><tr>";
         $c=0 ;
         while($data = db_fetch($qr)){
             
          if ($c==$settings['movie_photos_cells']) {print "  </tr><tr>" ;$c = 0 ;}

  print "<td align=center><a href=\"".str_replace("{id}",$data['id'],$links['links_movie_photo'])."\"><img border=0 src=\"".get_image($data['thumb'])."\" title=\"$data[name]\"></a></td>" ;
      
      $c++ ;          
         }
   
                 print "</tr></table>";
         close_table();
                 } 
    
}else{
    open_table();
    print "<center>$phrases[err_wrong_url]</center>";
    close_table();
}
    

//---------------------------------------------
require(CWD . "/includes/framework_end.php"); 
?>