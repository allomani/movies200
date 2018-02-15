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
if($id){                           
                                
//---------- view photo ---------------------

$qr = db_query("select * from movies_actors_photos where id='$id'");
 if(db_num($qr)){
         $data = db_fetch($qr);
$data_actor = db_qr_fetch("select * from movies_actors where id='$data[cat]'");
    
     
   //--- update views ----//
   db_query("update movies_actors_photos set views=views+1 where id='$id'");
   //--------------------//
   
   
//--- data photos array----
 $qrc = db_query("select id,thumb from movies_actors_photos where cat='$data[cat]' order by ord");
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



 
   print "<span class='path'><img src=\"$style[images]/arrw.gif\">&nbsp; <a class='path_link' href=\"".$links['links_actors']."\">$phrases[the_actors]</a> / <a class='path_link' href=\"".str_replace("{id}",$data_actor['id'],$links['links_actor_details'])."\" title=\"$data_actor[name]\">$data_actor[name]</a> / <a class='path_link' href=\"".str_replace("{id}","$data_actor[id]",$links['links_actor_photos'])."\">$phrases[actor_photos]</a>" . " / $phrases[the_photo] ".($cur_index+1)."/".count($list)."<br><br></span>";
    
   

    
       open_table();
       
      print "<table width=100%><tr>
       <td width='33%' align='$global_align'>";
       
       if($list[$prev_index]['id']){ 
      
       print "<a href=\"".str_replace("{id}",$list[$prev_index]['id'],$links['links_actor_photo'])."\"><img src=\"images/arrw_$global_align.gif\" title=\"$phrases[prev_photo]\" border=0><br>
       $phrases[prev_photo]</a>";
       }
       
       print "</td>
       <td width='33%' align='center'><a href=\"$data[img]\" target=_blank>
       <img src=\"images/full_size.gif\" title=\"$phrases[full_photo_size]\"><br>$phrases[full_photo_size]</a></td>  
       <td width='33%' align='$global_align_x'>";
    
     if($list[$next_index]['id']){ 
      $next_url = str_replace("{id}",$list[$next_index]['id'],$links['links_actor_photo']); 
       print "<a href=\"".$next_url."\"><img src=\"images/arrw_$global_align_x.gif\" title=\"$phrases[next_photo]\" border=0><br>
       $phrases[next_photo]</a>";
     }else{
        $next_url = str_replace("{id}",$list[0]['id'],$links['links_actor_photo']);
       }
     
       print "</td>  
       </tr></table><br><br>";
       
        if($data['votes_total'] > 0 && $data['votes'] > 0){
            $rating = $data['votes']/$data['votes_total'] ;
        }else{
            $rating = 0 ;
        }
        
        
         print "<center><a href=\"javascript:;\" onClick=\"window.location=scripturl+'/$next_url';\"><img src=\"$data[img_resized]\" title=\"$data[name]\" border=0></a><br><br>";
       print_rating('actor_photo',$data['id'],$rating);
       print "</center>";
       
       
       print iif($data['name'],"<br><br> <img src='$style[images]/info.gif'> &nbsp; $data[name]");
       
       print "<br>
        <img src='$style[images]/add_date.gif'> &nbsp; <b>$phrases[add_date] : </b>".date($settings['date_format'],$data['date'])."<br>";
        print "<img src='$style[images]/views.gif'> &nbsp; <b>$phrases[views] : </b>$data[views]"; 
        
        
        
        
       $in_this_photo = "";
        $qra = db_query("select movies_actors.*  from movies_actors,movies_actors_photos_tags where movies_actors.id=movies_actors_photos_tags.actor_id and movies_actors_photos_tags.photo_id='$id' and movies_actors_photos_tags.actor_id > 0 order by movies_actors.name asc");
        while($dataa = db_fetch($qra)){
                $in_this_photo .= iif($in_this_photo," , ")."<a href=\"".str_replace("{id}",$dataa['id'],$links['links_actor_details'])."\" title=\"$dataa[name]\">$dataa[name]</a>";
        }
       
       
        $qra = db_query("select *  from movies_actors_photos_tags where photo_id='$id' and actor_id='0' order by name asc");
        while($dataa = db_fetch($qra)){
                 $in_this_photo .= iif($in_this_photo," , ")."$dataa[name]"; 
        }
        
      
         print iif($in_this_photo,"<br><br> <img src='$style[images]/in_this_photo.gif'> &nbsp; <b>$phrases[in_this_photo] : </b> $in_this_photo");     
         
         
       print "<br><br>";
       close_table();
      
      //------ Comments -------------------
if($settings['enable_actor_photo_comments']){
    open_table($phrases['members_comments']);
    get_comments_box('actor_photo',$id);
    close_table();
}

 
   }else{
          open_table();
         print "<center> $phrases[err_wrong_url]</center>";
         close_table();
         }
    
}elseif($cat){
 
      
      
      
//-------- show movies photos --------------------
  
   $qr = db_query("select * from movies_actors_photos where cat='$cat' order by ord asc");
         if(db_num($qr)){
             
             $data_actor = db_qr_fetch("select id,name from movies_actors where id='$cat'");
             
   
   print "<span class='path'><img src=\"$style[images]/arrw.gif\">&nbsp; <a class='path_link' href=\"".$links['links_actors']."\">$phrases[the_actors]</a> / <a class='path_link' href=\"".str_replace("{id}",$data_actor['id'],$links['links_actor_details'])."\" title=\"$data_actor[name]\">$data_actor[name]</a> / $phrases[actor_photos] <br><br></span>";
    
     
    
         open_table($phrases['actor_photos']);
         print "<center><table width='100%'><tr>";
         $c=0 ;
         while($data = db_fetch($qr)){
          if ($c==$settings['movie_photos_cells']) {
print "  </tr><TR>" ;
$c = 0 ;
}
 ++$c ;
  print "<td align=center><a href=\"".str_replace("{id}",$data['id'],$links['links_actor_photo'])."\"><img border=0 src=\"".get_image($data['thumb'])."\" title=\"$data[name]\"></a></td>" ;
                 }
          /*       if(($settings['movie_photos_cells']-$i) > 0){
                         print "<td colspan=".($settings['movie_photos_cells']-$i)."></td>";
                         }    */
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
