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
 
 if($action=="movie_subtitles" || $action=="movie_subtitles_add_ok" || $action=="movie_subtitles_edit_ok" || $action=="movie_subtitles_del"){
  
      
     $id = (int) $id;
    
      $qr=db_query("select id,name,cat from movies_data where id='$id'");
    if(db_num($qr)){
            $data = db_fetch($qr);
 
 
 if_movie_admin($id);
 
            
print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$id'>$data[name]</a> / $phrases[subtitles]</a>"); 


  //----- edit ------------------
if($action=="movie_subtitles_edit_ok"){

    $sub_id = (array) $sub_id;
    
for($i=0;$i<count($sub_id);$i++){
   
 db_query("update movies_subtitles set name='".db_escape($name[$i])."',url='".db_escape($url[$i])."' where id='".intval($sub_id[$i])."'"); 
  
    } 
}
 
  //----- add ------------------
if($action=="movie_subtitles_add_ok"){

for($i=0;$i<count($url);$i++){
    if(trim($url[$i])){
 db_query("insert into movies_subtitles (name,url,cat,date) values('".db_escape($name[$i])."','".db_escape($url[$i])."','$id','".time()."')"); 
    }
    } 
}
//------- del ---------
if($action=="movie_subtitles_del"){
     $sub_id = (array) $sub_id;  
   foreach($sub_id as $iid){    
  delete_subtitle(intval($iid));
   }
        }

  //----------------------------------
  
   
  print "<p align=center class=title>  $phrases[subtitles] </p>

       <img src='images/add.gif'>&nbsp;<a href='index.php?action=movie_subtitles_add&id=$id'> $phrases[add] </a><br><br>
       ";

       $qr = db_query("select * from movies_subtitles where cat='$id' order by ord");
       if(db_num($qr)){
           print " <center>
           <form action='index.php' method='post' name='submit_form'>
           <input type='hidden' name='id' value='$id'>
           
      <table width=90% class=grid><tr><td>
<div id=\"movie_subtitles_list\">";
       
       $i=0;
       while($data = db_fetch($qr)){
        print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
      
        <table width=100%><tr>
              <td width=10><input type='checkbox' name='sub_id[$i]' value='$data[id]'>  </td>   
         <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img alt='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td>

      <td>$data[name]</td> <td align=left>
         <a href='index.php?action=movie_subtitles_edit&sub_id=$data[id]&id=$data[id]'>$phrases[edit]</a> - 
        <a href='index.php?action=movie_subtitles_del&sub_id=$data[id]&id=$id' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td>
        </tr></table></div>";
        
        $i++;
        }
        
        print "</div></td></tr>
        <tr><td width=100%>
          <img src='images/arrow_".$global_dir.".gif'>    
        
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
         
          <select name='action'>
          <option value='movie_subtitles_edit'>$phrases[edit]</option>
         <option value='movie_subtitles_del'>$phrases[delete]</option>
         </select>
        <input type=submit value=\"$phrases[do_button]\" onClick=\"return confirm('$phrases[are_you_sure]');\"> 
          
        </td></tr></table></form></center>";
        
            print "<script type=\"text/javascript\">
        init_sortlist('movie_subtitles_list','set_movie_subtitles_sort');
</script>";

        }else{
              print_admin_table("<center>$phrases[no_files]</center>");  
             
                }
  
    }else{
   print_admin_table("<center>$phrases[err_wrong_url]</center>");
               }
       } 
 // ------------------- Add Subtitles Form --------------
if($action=="movie_subtitles_add"){
     

   $qr=db_query("select id,cat,name from movies_data where id='$id'");

    if(db_num($qr)){
            $data = db_fetch($qr);
            
    if_movie_admin($id);
    
    
       print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$data[id]'>$data[name]</a> / <a href='index.php?action=movie_subtitles&id=$data[id]'>$phrases[subtitles]</a> / $phrases[add]"); 
       
   if(!$add_limit){
$add_limit = $settings['movies_add_limit'] ;
  }

print "<center><p align=center class=title>  $phrases[subtitles] </p>

          <form method=\"POST\" action=\"index.php\">

      <input type=\"hidden\" name=\"id\" value='$id'>
      
      <input type=hidden name=action value='movie_subtitles_add'>
      <table width=30% class=grid>
      <tr><td align=center> $phrases[fields_count]  : <input type=text name=add_limit value='$add_limit' size=3>
      &nbsp;&nbsp;<input type=submit value='$phrases[edit]'></td></tr></table></form>

      <br>

<form action='index.php' method=post name='sender'>
<input type=hidden name='action' value='movie_subtitles_add_ok'>
<input type=hidden name='id' value='$id'> " ;
  for ($i=0;$i<$add_limit;$i++){
        print "<table width=90% class=grid>
        <tr><td colspan=2><b> ".($i+1)." </b></td></tr>
        <tr>
        <td> $phrases[the_name] : </td><td><input type=text size=30 name=\"name[$i]\"></td>
        </tr>
        <tr>          
        <td> $phrases[the_url] : </td><td>  
                            <table><tr><td>
                                 <input type=\"text\" name=\"url[$i]\" size=\"50\" dir=ltr>   </td>

                                <td> <a href=\"javascript:uploader('subtitles','url[$i]');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td></tr></td>
        </tr></table><br>";
        }
print "<input type=submit value='$phrases[add]'>
</form></center>";
}else{
        print_admin_table("<center>  $phrases[err_wrong_url] </center>") ;
        }
}

 // ------------------- Add Subtitles Form --------------
if($action=="movie_subtitles_edit"){
     

    $sub_id = (array) $sub_id;
   
   if_movie_admin($id);
   
    
    if(count($sub_id)){
    $sub_id = array_map("intval",$sub_id);
    
    $data_movie = db_qr_fetch("select cat,name from movies_data where id='$id'");

      print_admin_path_links($data_movie['cat'],"<a href='index.php?action=movie_edit&id=$data_movie[id]'>$data_movie[name]</a> / <a href='index.php?action=movie_subtitles&id=$data_movie[id]'>$phrases[subtitles]</a> / $phrases[edit]"); 
     

    
    $qr=db_query("select * from movies_subtitles where id IN (".implode(",",$sub_id).")");
    if(db_num($qr)){

print "<center><p align=center class=title>  $phrases[subtitles] </p>


<form action='index.php' method=post name='sender'>
<input type=hidden name=action value='movie_subtitles_edit_ok'>
<input type=hidden name=id value='$id'> " ;
    $i=0;
 while($data = db_fetch($qr)){
     
        print "<input type='hidden' name='sub_id[$i]' value='$data[id]'>
        <table width=90% class=grid>
        <tr><td colspan=2><b> ".($i+1)." </b></td></tr>
        <tr>
        <td> $phrases[the_name] : </td><td><input type=text size=30 name=\"name[$i]\" value=\"$data[name]\"></td>
        </tr>
        <tr>          
        <td> $phrases[the_url] : </td><td>  
                            <table><tr><td>
                                 <input type=\"text\" name=\"url[$i]\" size=\"50\" dir=ltr value=\"$data[url]\">   </td>

                                <td> <a href=\"javascript:uploader('subtitles','url[$i]');\"><img src='images/file_up.gif' border=0 title='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td></tr></td>
        </tr></table><br>";
  $i++;     
 }
        
print "<input type=submit value='$phrases[edit]'>
</form></center>";
}else{
        print_admin_table("<center>  $phrases[err_wrong_url] </center>") ;
        }
    }else{
          print_admin_table("<center>  $phrases[no_files_selected] </center>") ; 
    }
}
