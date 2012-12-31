<?
 if(!defined('IS_ADMIN')){die('No Access');} 
 
if($action=="movie_photos" || $action=="movie_photos_add_ok" || $action=="movie_photos_edit_ok" || $action=="movie_photos_del"){
   
    $id = (int) $id;
    
      $qr=db_query("select id,name,cat from movies_data where id='$id'");
    if(db_num($qr)){
            $data = db_fetch($qr);
            
   if_movie_admin($id);
   
            
print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$id'>$data[name]</a> / $phrases[manage_photos]</a>"); 



 if($action=="movie_photos_add_ok"){
                    
  require_once(CWD. "/includes/class_save_file.php"); 
  
    $upload_folder = $settings['uploader_path']."/photos" ;      
  
$limit =  count($_FILES['photo_file']['name']);
  $err_cnt = 0  ;
for($i=0;$i<$limit;$i++){

if($_FILES['photo_file']['name'][$i]){

$imtype = file_extension($_FILES['photo_file']['name'][$i]);

if(in_array($imtype,$upload_types)){
                   
if($_FILES['photo_file']['error'][$i]==UPLOAD_ERR_OK){
       
                  
  if(!file_exists($upload_folder."/".$_FILES['datafile']['name'])){$replace_exists=1;}    
      
$fl = new save_file($_FILES['photo_file']['tmp_name'][$i],$upload_folder,$_FILES['photo_file']['name'][$i]);

if($fl->status){
$saveto_filename =  $fl->saved_filename;

//$resized_saved =  image_resize($saveto_filename,$settings['photo_resized_width'],$settings['photo_resized_height'],true,false,'resized');   

  $resized_saved =  create_thumb($saveto_filename,$settings['photo_resized_width'],$settings['photo_resized_height'],false,'resized');   

  $thumb_saved =  create_thumb($saveto_filename,$settings['photo_thumb_width'],$settings['photo_thumb_height'],true,'thumb');
  
      db_query("insert into movies_photos (name,img,img_resized,thumb,cat,date) values('".db_escape($photo_name[$i])."','".db_escape($saveto_filename)."','".db_escape($resized_saved)."','".db_escape($thumb_saved)."','$id','".time()."')");

     //---- tags ----//
   $inserted_id = mysql_insert_id();
   for($x=0;$x<count($tags[$i]);$x++){
            $aid = $tags[$i][$x];
            db_query("insert into movies_photos_tags (photo_id,".iif(is_numeric($aid),"actor_id","name").") values('".$inserted_id."','".db_escape($aid)."')");  
        }
 //------------//
 
  
      
}else{
      $err_msg .= "<b> $phrases[err] : </b> $phrases[the_file]  ".$_FILES['photo_file']['name'][$i]."  : ".$fl->last_error_description." <br>" ;  

}


  }else{
 $upload_max = convert_number_format(ini_get('upload_max_filesize'));
    $post_max = (convert_number_format(ini_get('post_max_size'))/2) ;
    $max_size = iif($upload_max < $post_max,convert_number_format($upload_max,2,true),convert_number_format($post_max,2,ture));
    


      $err_msg .= "<b> $phrases[err] : </b> $phrases[the_file]  ".$_FILES['photo_file']['name'][$i]."  : $phrases[err_upload_max_size] $max_size <br>" ;  


  }
}else{
      $err_msg .= "<b> $phrases[err] : </b> $phrases[the_file]  ".$_FILES['photo_file']['name'][$i]."  :  $phrases[this_filetype_not_allowed] <br>" ;  
         
}




  
        }

        }

        
//-------update Ord-----
$c=1;
$qr=db_query("select id from movies_photos where cat='$id' order by ord asc");
while($data=db_fetch($qr)){
db_query("update movies_photos set ord='$c' where id='$data[id]'");
$c++;
}
//------------


if($err_msg){
print_admin_table("<center>$err_msg</center>");
        }

        }
//----------- del ------
if($action=="movie_photos_del"){
    $photo_id = (array) $photo_id;
    
        foreach($photo_id as $iid){
            $iid = (int) $iid;
        $qr = db_query("select thumb,img,img_resized from movies_photos where id='$iid'");
        if(db_num($qr)){
        $data = db_fetch($qr);
       
        delete_file($data['thumb']);
        delete_file($data['img']);
        delete_file($data['img_resized']); 
        
        db_query("delete from movies_photos where id='$iid'");
       db_query("delete from movies_photos_tags where photo_id='$iid'");       
          
                }
        }
        }
        
//----------- edit ------
if($action=="movie_photos_edit_ok"){
    $photo_id = (array) $photo_id;
    
      for($i=0;$i<count($photo_id);$i++){
        db_query("update  movies_photos set name='".db_escape($photo_name[$i])."' where id='".intval($photo_id[$i])."'");      
      
        db_query("delete from movies_photos_tags where photo_id='".$photo_id[$i]."'");
    
        for($x=0;$x<count($tags[$i]);$x++){
            $aid = $tags[$i][$x];
            db_query("insert into movies_photos_tags (photo_id,".iif(is_numeric($aid),"actor_id","name").") values('".$photo_id[$i]."','".db_escape($aid)."')");  
        }
        
      }
        }
 //---------------------------
 
 
 print "<p align=center class=title>  $phrases[manage_photos] </p>
          <img src='images/add.gif'>&nbsp;<a href='index.php?action=movie_photos_add&cat=$id'> $phrases[movie_photos_add] </a><br><br>
         
       ";
         $qr = db_query("select * from movies_photos where cat='$id' order by ord asc");
       if(db_num($qr)){
        $c=0;
        $photos_main_div_width = ((($settings['photo_thumb_width']+60)*4)+60);
        print " <center>
        <form action='index.php' method='post' name='submit_form'>
        <input type=hidden name='id' value='$id'>    
        
         <div id=\"movie_photos_list\" style=\"width:".$photos_main_div_width.";\" >";
    //     print "<table width=80% class=grid><tr>";
        
        
       while($data = db_fetch($qr)){
           
    /*       
      if ($c==3) {
//print "  </tr><TR>" ;
print "<br><br>";
$c = 0 ;
}
 ++$c ;   
           */
//   print "<td align=center>";
   print "<div id=\"item_$data[id]\" style=\"float: $global_align;width:".($settings['photo_thumb_width']+60).";height:".($settings['photo_thumb_height']+80).";border: #CCC 1px dashed;\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
  
    <div style=\"cursor: move;text-align:right;\" class=\"handle\"><img title='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></div>  
  
    <br>
   <img src=\"$scripturl/".get_image($data['thumb'])."\" title=\"$data[name]\"><br>
   <br>
   <input type='checkbox' name='photo_id[]' value='$data[id]'><a href='index.php?action=movie_photos_edit&photo_id=$data[id]&id=$id'>$phrases[edit]</a> - <a href='index.php?action=movie_photos_del&photo_id=$data[id]&id=$id' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
   
   </div> ";
 // print "  </td> ";
       }
    
      //   print " </tr> </table>";
         print "  </div><br> 
         
         <table width=\"$photos_main_div_width\" class=grid>
         <tr><td>
         <img src='images/arrow_".$global_dir.".gif'>
         
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
          
          
         <select name='action'>
          <option value='movie_photos_edit'>$phrases[edit]</option>
         <option value='movie_photos_del'>$phrases[delete]</option>
         </select>
        <input type=submit value=\"$phrases[do_button]\" onClick=\"return confirm('$phrases[are_you_sure]');\">
         </td></tr></table></center>
         </form> ";    
          
            print "<script type=\"text/javascript\">
        init_sortlist('movie_photos_list','set_movie_photos_sort');
</script>";


       }else{
                print_admin_table("<center> $phrases[no_photos] </center>");
                }
   
    }else{
         print_admin_table("<center>$phrases[err_wrong_url]</center>");  
    }    
}

//---------- Photos Edit ---------
if($action=="movie_photos_edit"){
    $photo_id = (array) $photo_id;
    
     if_movie_admin($id);
     
     
    if(count($photo_id)){
    $photo_id = array_map("intval",$photo_id);
    
    $data_movie = db_qr_fetch("select cat,name from movies_data where id='$id'");
    print_admin_path_links($data_movie['cat'],"<a href='index.php?action=movie_edit&id=$id'>$data_movie[name]</a> / $phrases[manage_photos]</a>"); 


    
    $qr=db_query("select id,name,thumb,cat from movies_photos where id IN (".implode(",",$photo_id).")");
    if(db_num($qr)){
      
        print "<script src=\"$scripturl/js/jquery.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"$scripturl/js/jquery.fcbkcomplete.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    
    
    <center>
      <form action='index.php' method='post'>
      <input type='hidden' name='action' value='movie_photos_edit_ok'>
      <input type='hidden' name='id' value='$id'>";
      
      $i = 0;
      while($data = db_fetch($qr)){
      
      print "
      <input type='hidden' name='photo_id[$i]' value='$data[id]'>
      <table width='80%' class=grid>
      <tr><td colspan=2 align=center>
       <img src=\"$scripturl/".get_image($data['thumb'])."\"</td></tr>
          <tr>
        <td> $phrases[the_name]  </td><td><input type='text' size=30 name=\"photo_name[$i]\" value=\"$data[name]\"></td>
       </tr>
       
         <tr><td> $phrases[in_this_photo] : </td><td><select id=\"tags_$i\" name=\"tags[$i]\">";
    
       $qra = db_query("select movies_actors.* from movies_actors,movies_photos_tags where movies_actors.id=movies_photos_tags.actor_id and movies_photos_tags.photo_id='$data[id]' and movies_photos_tags.actor_id > 0 order by movies_actors.name asc");
        while($dataa = db_fetch($qra)){
                print "<option value=\"$dataa[id]\" class='selected'>$dataa[name]</option>";
        }
       
       
        $qra = db_query("select *  from movies_photos_tags where photo_id='$data[id]' and actor_id='0' order by name asc");
        while($dataa = db_fetch($qra)){
                print "<option value=\"$dataa[name]\" class='selected'>$dataa[name]</option>";
        }
        
         
         
        print "</select> </td></tr>
        
         </table><br>
       ";
        $i++;
      }
        
        print "<br>
        <input type=submit value='$phrases[edit]'>
        </form> 
        </center>";
        
          //----- tags js ------------       
  print "<script language=\"JavaScript\">
  jQuery.noConflict();

        jQuery(document).ready(function() 
        {";        
       
          for ($i=0;$i<count($photo_id);$i++){
         
          print "
          jQuery(\"#tags_$i\").fcbkcomplete({
            json_url: \"ajax.php?action=get_actors_json\",
            cache: false,
            filter_case: false,
            filter_hide: true,
            firstselected: false,
            filter_selected: true,
            newel: true        
          });"; 
        
          }
         print " });  
    </script>"; 
  //---------------------------
  
  
    }else{
        print_admin_table("<center> $phrases[err_wrong_url] </center>");
    }
    
    }else{
          print_admin_table("<center> $phrases[please_select_photos] </center>");       
    }
    
}

//-------------------- Add Photos Form ---------------
if($action=="movie_photos_add"){
     

   $qr=db_query("select id,cat,name from movies_data where id='$cat'");

    if(db_num($qr)){
            $data = db_fetch($qr);

 if_movie_admin($cat); 

  print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$cat'>$data[name]</a> / <a href='index.php?action=movie_photos&id=$cat'>$phrases[manage_photos]</a> / $phrases[movie_photos_add]"); 

    
 if(!$add_limit){
$add_limit = $settings['movies_add_limit'] ;
  }
  
  $add_limit = intval($add_limit);
  

print "<script src=\"$scripturl/js/jquery.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"$scripturl/js/jquery.fcbkcomplete.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    
    <center>

<form method=\"POST\" action=\"index.php\">

      <input type=\"hidden\" name=\"cat\" value='$cat'>
      <input type=hidden name=action value='movie_photos_add'>
      <table width=30% class=grid>
      <tr><td align=center> $phrases[fields_count]  : <input type=text name='add_limit' value='$add_limit' size=3>
      &nbsp;&nbsp;<input type=submit value='$phrases[edit]'></td></tr></table></form>

      <br>

<form action=index.php method=post enctype=\"multipart/form-data\" name='sender'>
<input type=hidden name=action value='movie_photos_add_ok'>
<input type=hidden name=id value='$cat'>   ";
          for ($i=0;$i<$add_limit;$i++){
        print "<table width=80% class=grid>  
        <tr><td colspan=2><b> ".($i+1)." </b></td>
        <tr>
        <td> $phrases[the_name]  </td><td><input type='text' size=30 name=\"photo_name[$i]\"></td>
        </tr>
        <tr>
        <td> $phrases[the_file]  </td><td><input type=file size=30 name=\"photo_file[$i]\"></td>
        </tr>
        <tr><td> $phrases[in_this_photo] : </td><td><select id=\"tags_$i\" name=\"tags[$i]\">
        </select></td></tr> 
        </table><br>";
        }
        print "<br><input type=submit value='$phrases[add]'></form></center>";
        
        
         //----- tags js ------------       
  print "<script language=\"JavaScript\">
  jQuery.noConflict();

        jQuery(document).ready(function() 
        {";        
       
          for ($i=0;$i<$add_limit;$i++){
         
          print "
          jQuery(\"#tags_$i\").fcbkcomplete({
            json_url: \"ajax.php?action=get_actors_json\",
            cache: false,
            filter_case: false,
            filter_hide: true,
            firstselected: false,
            filter_selected: true,
            newel: true        
          });"; 
        
          }
         print " });  
    </script>"; 
  //---------------------------
  
  
       }else{
        print_admin_table("<center>$phrases[err_wrong_url]</center>");
        }
        }
