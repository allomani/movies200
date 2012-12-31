<?
 if(!defined('IS_ADMIN')){die('No Access');} 
 
//---------------------------------- movies Cats -----------------------------
if($action=="movies" || $action=="movies_cats" || $action=="cats_del" || $action=="cats_edit_ok" ||
$action=="cats_add_ok" || $action=="cats_enable" || $action=="cats_disable" || $action=="movie_add_ok" || 
 $action=="movie_del" || $action=="movie_move_ok" || $action=="cats_move_ok"){


 $cat = (int) $cat;
 if(!$cat){$cat=0;}

//---------- cat enable ----------- 
 if($action=="cats_enable"){
     if_cat_admin($id);
     db_query("update movies_cats set active=1 where id='$id'");
 }
 
 //----------- cat disable --------------
  if($action=="cats_disable"){
      if_cat_admin($id); 
     db_query("update movies_cats set active=0 where id='$id'");
 }
 
 //------- cat add ----------
  if($action =="cats_add_ok"){
      
      if_cat_admin($cat); 
      
$name = trim($name);
if($name){
    $ord_data = db_qr_fetch("select max(ord) as ord from movies_cats where cat='$cat'");
  db_query("insert into movies_cats (name,img,cat,download_for_members,watch_for_members,active,ord) values('".db_escape($name)."','".db_escape($img)."','$cat','".intval($download_for_members)."','".intval($watch_for_members)."',1,'".(intval($ord_data['ord'])+1)."')");
 
  $new_id = mysql_insert_id();
  $path = get_cat_path_str($new_id);  
  db_query("update movies_cats set path='$path' where id='$new_id'"); 
  
  }
  
        }
 //------- cats move ------
if($action=="cats_move_ok"){
 if_cat_admin($cat,false);
 
$qr_to =  db_qr_num("select id from movies_cats where id='$cat'");
if($cat==0){$qr_to=1;}

 if($qr_to > 0){
     if(is_array($id)){
     
      
    foreach($id as $idx){
            db_query("update movies_cats set cat='$cat' where id='$idx'");
            
            //---- update paths -----//
            $subcats = get_cats($idx);
            foreach($subcats as $sub_id){
            $path = get_cat_path_str($sub_id);
            db_query("update movies_cats set path='$path' where id='$sub_id'"); 
            } 
    }  
           
     }else{
          print_admin_table("$phrases[err_cats_not_selected]");   
     }
      
         }else{
       print_admin_table("$phrases[err_invalid_cat_id]");
        }
    }
//-------------------- cat del ----------------------
 if($action=="cats_del"){
 if(!is_array($id)){$id=array($id);}

if(count($id)){
    foreach($id as $idx){
        
    if_cat_admin($idx);
    
            $delete_array = get_cats($idx);
  foreach($delete_array as $id_del){
     db_query("delete from movies_cats where id='$id_del'");

     $qr = db_query("select id from movies_data where cat='$id_del'");
     while($data = db_fetch($qr)){
     delete_movie($data['id']);
     }
     }
    }

         }
 }
//------------------- cat edit -----------------------------
 if($action=="cats_edit_ok"){
 
 if_cat_admin($id);
 
  if(if_admin("",true)){
 $users_str = @implode(',',(array) $user_id);
 $update_cat_users=true;  
 }else{
     $users_str= "";
     $update_cat_users=false; 
 }
 
   
 db_query("update movies_cats set name='".db_escape($name)."',img='".db_escape($img)."',download_for_members='".intval($download_for_members)."',watch_for_members='".intval($watch_for_members)."'".iif($update_cat_users,",`users`='".db_escape($users_str)."'").",page_title='".db_escape($page_title)."',page_description='".db_escape($page_description)."',page_keywords='".db_escape($page_keywords)."' where id='$id'");

  
         }
//-----------------------------------------------------------
if($action=="movie_move_ok"){
$qr_to =  db_qr_num("select id from movies_cats where id='$cat'");

 if($qr_to > 0){
     $movie_id = (array) $movie_id;
    
    foreach($movie_id as $idx){
            db_query("update movies_data set cat='$cat' where id='$idx'");
    }
     

         }else{
       print_admin_table("<center><b>$phrases[err_invalid_cat_id] </center>");
        }
   }
     
        
 //---------- add --------------
 if($action=="movie_add_ok"){
     
 if_cat_admin($cat);
 
$name = trim($name);
     
if($name){

db_query("insert into movies_data (name,cat,date) values('".db_escape($name)."','$cat','".time()."')");
$movie_id = mysql_insert_id();
 
js_redirect("index.php?action=movie_edit&id=$movie_id");

}else{
  js_redirect("index.php?action=movie_add&cat=$cat");
}
}

//------------- del ----------------------
if($action=="movie_del"){
   $movie_id = (array) $movie_id;
   
   for($i=0;$i<count($movie_id);$i++){
       if_movie_admin($movie_id[$i]);
        delete_movie($movie_id[$i]);
   }

 }


 //-------------- dirs header ----------------
print_admin_path_links($cat);
//if_cat_admin($cat);

 print "<img src='images/add.gif'>&nbsp;<a href='index.php?action=cats_add&cat=$cat'>$phrases[add_cat]</a><br>";   
   
   
    $cat_title = db_qr_fetch("select name from movies_cats where id='$cat'");







$qr = db_query("select * from movies_cats where cat='$cat' order by ord asc");


if(db_num($qr)){
      print "<p align=center class=title>  $phrases[the_cats] </p> ";    
        print "<form action='index.php' method=post name='cats_form'>
        <input type='hidden' name='cat' value='$cat'>
        <center><table width=80% class=grid><tr><td>
<div id=\"cats_list\" >";

       $c=0 ;


 while($data = db_fetch($qr)){
      print "
      <div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\"
     onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
      <table width=100%><tr>
      <td width=2>
      <input type=checkbox name=id[] value='$data[id]'>
      </td>
      <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img title='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td>
      
      <td><a href='index.php?action=movies&cat=$data[id]'>$data[name]</a></td>
      <td align=$global_align_x>".iif($data['active'],"<a href='index.php?action=cats_disable&cat=$cat&id=$data[id]'>$phrases[disable]</a>","<a href='index.php?action=cats_enable&cat=$cat&id=$data[id]'>$phrases[enable]</a>")." -
       <a href='index.php?action=cats_edit&id=$data[id]&cat=$cat'>$phrases[edit]</a> - <a href=\"index.php?action=cats_del&id=$data[id]&cat=$cat\" onClick=\"return confirm('$phrases[cat_del_warn]');\">$phrases[delete]</a></td>
      </tr></table></div>";
         }

                print "</div>
       
       
       <table width=100%><tr>
          <td width=2><img src='images/arrow_".$global_dir.".gif'></td>   
          <td>

          <a href='#' onclick=\"CheckAll('cats_form'); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll('cats_form'); return false;\">$phrases[select_none] </a> 
          &nbsp;&nbsp; 
          <select name=action>
         
          <option value='cats_move'>$phrases[move]</option>
           <option value='cats_del'>$phrases[delete]</option>  
          </select>
           &nbsp;&nbsp;
           <input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('".$phrases['are_you_sure']."');\">
          </td></tr></table>
          
          </td></tr>
          
          </table>
          
          
      
       
        
          </center><br>
          </form>
       
       <script type=\"text/javascript\">
        init_sortlist('cats_list','set_cats_sort');
</script>" ;
 print "<br><hr width=90% class='separate_line' size=\"1\"><br>";  
    }else{
        if(!$cat){
          print "<p align=center class=title>  $phrases[the_cats] </p> ";   
            print "
            <center>
            <table width=50% class=grid><tr><td align=center> $phrases[no_cats]</td></tr></table></center>";  
        }
            }

      

        

//---------------------------------- movies -----------------------------------

if($cat){
    
      
       print "<p class=title align=center>$phrases[the_movies]</p><br>" ;


       
         print "<img src='images/add.gif'>&nbsp;<a href='index.php?action=movie_add&cat=$cat'>$phrases[add_movie]</a><br><br>";
   

      $qr=db_query("select * from movies_data where cat='$cat' order by id desc");
       print "<center>
       <table class=grid width=90%>
        <form action=index.php method=post name=submit_form>
        <input type=hidden name=cat value='$cat'> " ;
      if(db_num($qr)){
           while($data = db_fetch($qr)){
                print "<tr>
                   <td width=2><input name='movie_id[]' type='checkbox' value='$data[id]' ></td>
                   <td><a href='index.php?action=movie_edit&id=$data[id]'>$data[name]</a></td>
                <td align=$global_align_x><a href='index.php?action=movie_edit&id=$data[id]'>$phrases[edit] </a> -

                <a href='index.php?action=movie_del&movie_id=$data[id]&cat=$cat' onClick=\"return confirm('{$phrases['movie_del_warn']}');\"> $phrases[delete]</a></td></tr>";

                   }

               print "<tr><td width=2><img src='images/arrow_rtl.gif'></td>
          <td colspan=4><a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;&nbsp;  ";


          print "<select name=action>
          <option value='new_menu_add'>$phrases[add_to_new_movies_list]</option> 
          <option value='movie_move'>$phrases[move]</option>
          <option value='movie_del'>$phrases[delete]</option>
          </select>


          ";


          print "&nbsp;&nbsp;<input type=submit value=' $phrases[do_button] ' onClick=\"return confirm('$phrases[are_you_sure]');\"></td></tr> </form> ";

              }else{
                      print "<tr><td align=center> $phrases[no_movies] </td></tr>";
                      }


       print "</form></table>";

                }

        }

 //----------------- Movie Move -------
if($action == "movie_move"){
$cat = intval($cat);


   $movie_id = (array) $movie_id;
   
 if(count($movie_id)){

 print "<form action=index.php method=post name=sender>
 <input type=hidden name=action value='movie_move_ok'>
 <input type=hidden name=cat_from value='$cat'>
 <input type=hidden name=confirm value='1'>
 <center><table width=60% class=grid><tr><td colspan=2><b> $phrases[move_from] : </b>";

//-----------------------------------------
$data_from['cat'] = $cat ;
while($data_from['cat']>0){
   $data_from = db_qr_fetch("select name,id,cat from movies_cats where id='$data_from[cat]'");

        $data_from_txt = "$data_from[name] / ". $data_from_txt  ;

        }
   print "$data_from_txt";
//------------------------------------------

 print "</td></tr>";
 $c = 1 ;
foreach($movie_id as $movie_idx){
    $movie_idx = (int) $movie_idx;
$data_movie=db_qr_fetch("select name from movies_data where id='$movie_idx'");
  print "<input type=hidden name=movie_id[] value='$movie_idx'>";
        print "<tr><td width=2><b>$c</b></td><td>$data_movie[name]</td></tr>"  ;
        ++$c;
        }
  print "<tr><td colspan=2><b>$phrases[move_to] : </b><select name=cat>";
       $qr = db_query("select * from movies_cats where id !='$cat' order by cat,ord,binary name asc");
   
    while($data=db_fetch($qr)){
    
   
        //-------------------------------
        $dir_content = "";
        $dir_data['cat'] = $data['cat'] ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select name,id,cat from movies_cats where id=$dir_data[cat]");

        $dir_content = "$dir_data[name] -> ". $dir_content  ;
        }
      $data['full_name'] = $dir_content .$data['name'];      
     //---------------------------------------
        
       print "<option value='$data[id]'>$data[full_name]</option>";   
    
    }
   
    
  print "</select>
  </td></tr>
 <tr><td colspan=2 align=center><input type=submit value=' $phrases[move] '></td></tr>
 </table>";
        }else{
                print "<center>  $phrases[please_select_movies] </center>";
                }
        }
//-----------------------------------------------------------------------------
if($action == "movie_edit" || $action=="movie_edit_ok" || $action=="movie_cover_del"){
  if_movie_admin($id); 
 
 

        
//-------- edit -----------------
 if($action=="movie_edit_ok"){
$name = trim($name);
if($name){
 
 
 //----- filter XSS Tages -------
/*include_once(CWD . "/includes/class_inputfilter.php");
$Filter = new InputFilter(array(),array(),1,1);
$details = $Filter->process($details);*/
//------------------------------
 
$director = (array) $director;

                
   
db_query("update movies_data set name='".db_escape($name)."',year='".intval($year)."',details='".db_escape($details,false)."',director='".db_escape($director[0])."' where id='$id'");


  //------------- Custom Fields  ------------------
   if(is_array($custom) && is_array($custom_id)){
   for($i=0;$i<=count($custom);$i++){
   if($custom_id[$i]){
   $m_custom_id=intval($custom_id[$i]);
   $m_custom_name =$custom[$i] ;
 
 db_query("update movies_data set field_".$m_custom_id."='".db_escape($m_custom_name,false)."' where id='$id'");


       }
   }
   }
  //------------------------------------------------
  
  
print_admin_table("<center> $phrases[edit_done] </center>");

}

//------ update img -----------
if($_FILES['img']['name']){
    require_once(CWD. "/includes/class_save_file.php");   
    
    $upload_folder = $settings['uploader_path']."/covers" ;
    $allowed_types = array("jpg","png","gif","bmp");
    $imtype = file_extension($_FILES['img']['name']);  
    
    if($_FILES['img']['error']==UPLOAD_ERR_OK){  
    if(in_array($imtype,$allowed_types)){ 
        
$fl = new save_file($_FILES['img']['tmp_name'],$upload_folder,$_FILES['img']['name']);

if($fl->status){
$img_saved =  $fl->saved_filename;
$thumb_saved =  create_thumb($img_saved,$settings['movie_cover_thumb_width'],$settings['movie_cover_thumb_height'],$settings['cover_thumb_fixed'],'thumb');

if(file_exists($img_saved) && file_exists($thumb_saved)){
$old_data = db_qr_fetch("select img,thumb from movies_data where id='$id'"); 

delete_file($old_data['img']);
delete_file($old_data['thumb']);


db_query("update movies_data set img='".db_escape($img_saved,false)."',thumb='".db_escape($thumb_saved,false)."' where id='$id'");
  
   
   
}else{
    print_admin_table("<center>$phrases[cover_photo_save_error]</center>"); 
}

}else{
print_admin_table("<center><b>$phrases[cover_photo] : </b> ".$fl->last_error_description."</center>");  
}


    }else{
        print_admin_table("<center><b>$phrases[cover_photo] : </b> $phrases[this_filetype_not_allowed]</center>");   
    }
    }else{
        
    $upload_max = convert_number_format(ini_get('upload_max_filesize'));
    $post_max = (convert_number_format(ini_get('post_max_size'))/2) ;
    $max_size = iif($upload_max < $post_max,convert_number_format($upload_max,2,true),convert_number_format($post_max,2,ture));
    
    
    print_admin_table("<center><b>$phrases[cover_photo] : </b> $phrases[err_upload_max_size] $max_size </center>");  

    } 
    
      
    
}




        }
  //---------------movie_cover_del------------------
if($action=="movie_cover_del"){

$qr = db_query("select img,thumb from movies_data where id='$id'");
if(db_num($qr)){
        $data = db_fetch($qr);

 delete_file($data['img']);
 delete_file($data['thumb']);
 
 db_query("update movies_data set img='',thumb='' where id='$id'");
 }
 }       



       
//--------------------------
     $qr=db_query("select * from movies_data where id='$id'");
    if(db_num($qr)){
            $data = db_fetch($qr);
            
print_admin_path_links($data['cat'],$data['name']); 


         print "<script src=\"$scripturl/js/jquery.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"$scripturl/js/jquery.fcbkcomplete.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    
    <center>
                                                                                            

       <table class=grid width=60%><tr>
       <td align=center><a href='index.php?action=movie_files&id=$id'><img src='images/movie_files_manage.png' border=0><br>$phrases[manage_files]</a></td>     
       <td align=center><a href='index.php?action=movie_actors&id=$id'><img src='images/movie_actors_manage.png' border=0><br>$phrases[manage_actors]</a></td>
        <td align=center><a href='index.php?action=movie_subtitles&id=$id'><img src='images/movie_subtitles_manage.png' border=0><br>$phrases[manage_subtitles]</a></td>  
         <td align=center><a href='index.php?action=movie_photos&id=$id'><img src='images/movie_photos_manage.png' border=0><br>$phrases[manage_photos]</a></td> 
            <td align=center><a href='index.php?action=movie_meta&id=$id'><img src='images/movie_meta.png' border=0><br>$phrases[manage_movie_meta]</a></td> 
       </tr></table><br>

       <table class=grid width=90%>

       <tr><td align=center valign=top>
      ".iif($data['img'],"<a href=\"javascript:enlarge_pic('$scripturl/".$data['img']."','".htmlspecialchars(str_replace(array("?","'"),"",$data['name']))."')\">")."
      <img border=0 src=\"$scripturl/".get_image($data['thumb'])."\" title=\"$data[name]\">".iif($data['img'],"</a>")."
                           
                           <br>
                            
                            
       <a href='index.php?action=movie_cover_del&id=$data[id]' onclick=\"return confirm('$phrases[are_you_sure]')\"> $phrases[delete_cover] </a></td>
       
       <td>
       <table>
       <form action=index.php method=post enctype=\"multipart/form-data\" name=sender>
       <input type=hidden name=action value='movie_edit_ok'>

       <input type=hidden name=id value='$id'>

     
       <tr><td><b> $phrases[the_name] : </b></td><td><input type=text name='name' size=30 value=\"$data[name]\"></td></tr>
       
        <tr><td><b> $phrases[release_year] : </b></td><td><input type=text name='year' size='4' value=\"".iif($data['year'],$data['year'],"")."\"></td></tr> 
        
        
       <tr><td><b> $phrases[cover_photo] : </b></td><td>
        <input type=file size=30 name='img'>
                                 </td></tr>
                                 
          <tr><td><b> $phrases[the_director] : </b></td><td><select name='director' id='director'>";
          if($data['director']){
              if(is_numeric($data['director'])){
              $datad = db_qr_fetch("select id,name from movies_actors where id='$data[director]'");
               print "<option value=\"$datad[id]\" class='selected'>$datad[name]</option>"; 
                   
              }else{
                  print "<option value=\"$data[director]\" class='selected'>$data[director]</option>";
              }
          }
          
          print "</select></td></tr>";
          
           $cf = 0 ;

$qrf = db_query("select * from movies_fields_sets order by ord");
   if(db_num($qrf)){
    
while($dataf = db_fetch($qrf)){
    print "
    <input type=hidden name=\"custom_id[$cf]\" value=\"$dataf[id]\">
    <tr><td width=25%><b>$dataf[name] : </b><br>$dataf[details]</td><td>";
    print get_movie_field("custom[$cf]",$dataf,$data["field_".$dataf['id']]);
        print "</td></tr>";
$cf++;
}

}
 
        
        
        
        print "</table>
        
        </td><td valign=top align='$global_align_x'>
        <a href='index.php?action=movie_del&movie_id=$data[id]&cat=$data[cat]' onClick=\"return confirm('{$phrases['movie_del_warn']}');\"><img src=\"images/delete.gif\" border=0 title=\"$phrases[delete]\">  <br> $phrases[delete]</a>
        
        </td></tr></table>
        
                    <br>
        <fieldset style=\"width:90%\">
        <legend><b>$phrases[movie_description]</b></legend>";
                editor_print_form("details",600,300,$data['details']);    
          print "
          </fieldset>
          
          
            <br>
      <center> <input type=submit value=' $phrases[edit] ' style=\"width:100;height:30;\"></center>
       
         
       
         </form>
      
      ";
       
         
       print "<script language=\"JavaScript\">
  jQuery.noConflict();

        jQuery(document).ready(function() 
        {
          jQuery(\"#director\").fcbkcomplete({
            json_url: \"ajax.php?action=get_actors_json\",
            cache: false,
            filter_case: false,
            filter_hide: true,
            firstselected: false,
            filter_selected: true,
            newel: true,
            maxitems: 1        
          });
       });  
    </script>"; 

     
         
       }else{
   print_admin_table("<center>$phrases[err_wrong_url]</center>");
               }
        }

//----------- Movie Meta ---------------
if($action=="movie_meta" || $action=="movie_meta_edit_ok"){

if_movie_admin($id);

//---- edit ----
if($action=="movie_meta_edit_ok"){
    db_query("update movies_data set page_title='".db_escape($page_title)."',page_description='".db_escape($page_description)."',page_keywords='".db_escape($page_keywords)."' where id='$id'");

}

 
   $qr=db_query("select * from movies_data where id='$id'");
    if(db_num($qr)){
            $data = db_fetch($qr);



            
print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$id'>$data[name]</a> / $phrases[manage_movie_meta]"); 


print " <center>
<form action='index.php' method='post'>
<input type='hidden' name='action' value='movie_meta_edit_ok'>
<input type='hidden' name='id' value='$id'>

<table width=70% class=grid>
                              
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=30 name=page_title value=\"$data[page_title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <input type=text size=30 name=page_description value=\"$data[page_description]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                              <input type=text size=30 name=page_keywords value=\"$data[page_keywords]\"></td></tr>
                              <tr><td colspan=2> <font color='#808080'>* $phrases[leave_blank_to_use_default_settings] </font> </td></tr>
                              <tr><td colspan=2 align='center'><input type=submit value=\"$phrases[edit]\"></td></tr>
                              </table>
                           </form> 
                           </center>  
                              ";
                              
    }else{
   print_admin_table("<center>$phrases[err_wrong_url]</center>");     
    }
      
      
}

//------------------ Add Movie Form ---------------------
if($action=="movie_add"){

  if_cat_admin($cat); 
          print_admin_path_links($cat);

     
       print "<p align=center class=title> $phrases[add_movie] </p>
      <br><center>
      <form action='index.php' method=post name=sender>
      <input type=hidden name=action value='movie_add_ok'>
         <input type=hidden name=cat value='$cat'>
       <table width=50% class=grid>
       <tr><td> <b>$phrases[the_name] </b> </td><td>
       <input type=text name=name size=30>
       </td></tr>
       
       <tr><td colspan=2 align=center><input type=submit value=\"$phrases[add]\"> </td></tr></table>
       </form>";



        }

//---------- cat edit -----------------------
if($action=="cats_edit"){
if_cat_admin($id); 

    $qr = db_query("select * from movies_cats where id='$id'");
  if(db_num($qr)){ 
  $data=db_fetch($qr); 
   print_admin_path_links($data['id']); 
               print "<center>

                 <form method=\"POST\" action=\"index.php\" name='sender'>

                      <input type=hidden name=\"id\" value='$id'>

                      <input type=hidden name=\"action\" value='cats_edit_ok'>
                       <input type=hidden name=\"cat\" value='$cat'>
                       
                       <table border=0 width=\"90%\"  style=\"border-collapse: collapse\" class=grid> ";


                  print "  <tr>
                <td width=\"50\">
                <b>$phrases[the_name]</b></td><td>
                <input type=\"text\" name=\"name\" value='$data[name]' size=\"29\"></td>
                        </tr>
                         <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>                 
                                <table><tr><td>
                                <input type=\"text\" name=\"img\" size=\"50\" value=\"$data[img]\" dir=ltr>  </td><td> <a href=\"javascript:uploader('cats','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>
                                 </td></tr>
               <tr>
                <td colspan=2>
                <input type=\"checkbox\" name=\"watch_for_members\" value=\"1\"".iif($data['watch_for_members']," checked")."> $phrases[watch_for_members] 
                <br>
                <input type=\"checkbox\" name=\"download_for_members\" value=\"1\"".iif($data['download_for_members']," checked")."> $phrases[download_for_members] 
                </td>
                
               
                        </tr></table> <br>
                        ";
                      //-------------- Moderators --------------//                      
                       if(if_admin("",true)){
                       
                       print "
                        <table border=0 width=\"90%\"  style=\"border-collapse: collapse\" class=grid>
                        <tr><td><b>$phrases[the_moderators]</b></td>
                       <td>";
                       $users_array = get_cat_users($id);
                           // print_r($users_array);
                    
                       $qro=db_query("select * from movies_user where group_id=2 order by id");
                        if(db_num($qro)){
                       print "<table width=100%><tr>";
                       $c=0;
                       while($datao=db_fetch($qro)){
   if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }
    
                           print "<td><input type=\"checkbox\" name=\"user_id[]\" value=\"$datao[id]\"".iif($users_array[$datao['id']],' checked').iif($users_array[$datao['id']] && $users_array[$datao['id']] !=$id,' disabled').">$datao[username]</td>";
                           $c++;
                       }
                       print "</tr></table>";
                        }else{
                              print " $phrases[no_moderators]";
                        }
                       print "</td></tr>
                       </table><br>";
                       }
    //-------------- Tags ------------//                 
                              print "
                              <fieldset style=\"width:90%;text-align:$global_align\">
                              <legend><b>$phrases[page_custom_info]</b></legend>
                              <table width=100%>
                              
                              <tr><td><b>$phrases[the_title] : </td><td>
                              <input type=text size=30 name=page_title value=\"$data[page_title]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_description] : </td><td>
                              <input type=text size=30 name=page_description value=\"$data[page_description]\"></td></tr>
                              
                              <tr><td><b>$phrases[the_page_keywords] : </td><td>
                              <input type=text size=30 name=page_keywords value=\"$data[page_keywords]\"></td></tr>
                              
                              </table>
                              <br>
                              <font color='#808080'>* $phrases[leave_blank_to_use_default_settings] </font>
                              </fieldset><br><br> ";
                              print " <table><tr>
                                <td colspan=2>
                <center><input type=\"submit\" value=\"$phrases[edit]\">
                        </td>
                        </tr>





                </table>

</form>    </center>\n";
  }else{
      print_admin_table("<center>$phrases[err_wrong_url]</center>");
  }
}

//--------- cat add ----------
if($action=="cats_add"){

if_cat_admin($cat,true); 

 
print_admin_path_links($cat,$phrases["add_cat"]); 
   
      print "<center>
   <form method=\"POST\" action=\"index.php\" name='sender'>

   <table width=45% class=grid><tr>
   <td> <b> $phrases[the_name] : </b> </td><td>
    <input type=hidden name='action' value='cats_add_ok'>
    <input type=hidden value='$cat' name=cat>
   <input type=text name=name size=20>
    </td>
    </tr>
     <tr> <td width=\"100\">
                <b>$phrases[the_image]</b></td>
                                <td>
                                <table><tr><td>
                                <input type=\"text\" name=\"img\" size=\"50\" dir=ltr>  </td><td> <a href=\"javascript:uploader('cats','img');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>
                                 </td></tr>
                                 
                                 
    <tr>
                                <td colspan=2>
                <input type=\"checkbox\" name=\"watch_for_members\" value=\"1\"> $phrases[watch_for_members] 
                <br>
                <input type=\"checkbox\" name=\"download_for_members\" value=\"1\"> $phrases[download_for_members] 
                </td>
                        </tr>
    <tr><td align=center colspan=2><input type=submit value=' $phrases[add] '></td>
    </tr></table>
                 </form>

   </center><br>";
}


   //----------------- Cats Move -------
if($action == "cats_move"){ 

$cat = intval($cat);

//if_products_cat_admin($cat,false);  

$id = (array) $id;
 if(count($id)){

 print "<form action=index.php method=post name=sender>
 <input type=hidden name=action value='cats_move_ok'>
 <input type=hidden name=from_cat value='$cat'>
 <center><table width=60% class=grid><tr><td colspan=2><b> $phrases[move_from] : </b>";

//-----------------------------------------
$data_from['cat'] = $cat ;
while($data_from['cat']>0){
   $data_from = db_qr_fetch("select name,id,cat from movies_cats where id='$data_from[cat]'");

  
        $data_from_txt = "$data_from[name] / ". $data_from_txt  ;
 
        }
   print "$data_from_txt";
//------------------------------------------

 print "</td></tr>";
 $c = 1 ;
foreach($id as $idx){
 
$data=db_qr_fetch("select name from movies_cats where id='$idx'");
  print "<input type=hidden name=id[] value='$idx'>";
        print "<tr><td width=2><b>$c</b></td><td>$data[name]</td></tr>"  ;
        ++$c;
        $sql_ids[] = $idx;
        }
 print "<tr><td colspan=2><b>$phrases[move_to] : </b><select name=cat>".
 iif($cat != 0,"<option value='0'>$phrases[without_main_cat]</option>");
       $qr = db_query("select * from movies_cats where id !='$cat' and id not IN(".implode($sql_ids).") order by cat,ord,binary name asc");
   
    while($data=db_fetch($qr)){
    
    $skip=0;    
    foreach($sql_ids as $par_id){
    $paths = explode(",",$data['path']);
    $indx = array_search($par_id,$paths);
    if($indx){
    $skip=1; 
    }
    }
    
    if(!$skip){
        //-------------------------------
        $dir_content = "";
        $dir_data['cat'] = $data['cat'] ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select name,id,cat from movies_cats where id=$dir_data[cat]");

        $dir_content = "$dir_data[name] -> ". $dir_content  ;
        }
      $data['full_name'] = $dir_content .$data['name'];      
     //---------------------------------------
        
       print "<option value='$data[id]'>$data[full_name]</option>";   
    } 
    }
   
    
  print "</select>
  </td></tr>
 <tr><td colspan=2 align=center><input type=submit value=' $phrases[move_the_cats] '></td></tr>
 </table>";
 }else{
                print "<center>  $phrases[please_select_cats_first] </center>";
                }
        } 
?>