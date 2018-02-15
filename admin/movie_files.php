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
           
 if($action=="movie_files" || $action=="movie_files_add_ok" || $action == "movie_files_edit_ok"){
  
    $id = (int) $id;
    
      $qr=db_query("select id,name,cat from movies_data where id='$id'");
    if(db_num($qr)){
            $data = db_fetch($qr);

 if_movie_admin($id); 
 
             
print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$id'>$data[name]</a> / $phrases[manage_files]</a>"); 



 
   //-------- add files --------//
 if($action=="movie_files_add_ok"){
     
 for($i=0;$i<count($url);$i++){
 if(trim($url[$i]) || trim($name[$i]) || trim($url_watch[$i])){

 db_query("insert into movies_files(name,url,url_watch,cat,date) values('".db_escape($name[$i])."','".db_escape($url[$i],false)."','".db_escape($url_watch[$i],false)."','$id','".time()."')");
}
 }
}
//------- edit files -------//
if($action == "movie_files_edit_ok"){
                         
        for ($i = 0; $i < count($file_id); $i++)
        {
            $file_id[$i] = (int) $file_id[$i];
if($file_del[$i]){
  db_query("delete from movies_files where id='{$file_id[$i]}'");
  }else{  
if(!$custom_watch[$i]){$url_watch[$i] = "";}
    
       db_query("update movies_files set name='".db_escape($name[$i])."',url='".db_escape($url[$i],false)."',url_watch='".db_escape($url_watch[$i],false)."' where id='{$file_id[$i]}'");
}
        }

       }

  
 print " <p align=center class=title> $phrases[manage_files] </p>
       <img src=\"images/add.gif\">&nbsp;<a href='index.php?action=movie_files_add&id=$id'> $phrases[movie_files_add]  </a><br><br>";
       
  
       $qr = db_query("select * from movies_files where cat='$data[id]' order by ord asc");
        if(db_num($qr)){
            
       print "<form action=index.php method=post>
       <input type=hidden name=action value='movie_files_edit_ok'>

       <input type=hidden name='id' value='$id'>
       <center>
<table width=90% class=grid><tr><td>
<div id=\"movie_files_list\">";

                $i=0;
                while($data = db_fetch($qr)){


        print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
        <input type='hidden' name='file_id[$i]' value='$data[id]'> 
        <table width=100%><tr>
         <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img alt='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td>
      <td><b>$phrases[the_name] : </b><input type=text size=20 name=name[$i] value=\"$data[name]\"></td>
        <td><b>$phrases[download_url] : </b><input type=text size=30 name=url[$i] dir=ltr value=\"$data[url]\"><br>
        <input type=checkbox name=\"custom_watch[$i]\" value=1 ".iif($data['url_watch'],"checked ")."onClick=\"$('custom_watch_div[$i]').style.display=(this.checked ? 'inline':'none');\">$phrases[custom_watch_url]
        </td>
          
        <td><input type='checkbox' name='file_del[$i]' value='1'> $phrases[delete]</td>
        </tr>
        <tr>
        <td colspan=2></td>
         <td id='custom_watch_div[$i]'".iif(!$data['url_watch']," style=\"display:none;\"")."><b>$phrases[watch_url] : </b><input type=text size=30 name=\"url_watch[$i]\" dir=ltr value=\"$data[url_watch]\"><br> 
         </tr>
        </table><hr size=1 class='separate_line'></div>";
        $i++; 
        }
        print "</div></td></tr>
        <tr><td colspan=4 align=center><input type=submit value=\"$phrases[edit]\"></td></tr>
        </table></center>";
        
          print "<script type=\"text/javascript\">
        init_sortlist('movie_files_list','set_movie_files_list_sort');
</script>";
        }else{
                print_admin_table("<center>  $phrases[no_files] </center>");
                }
                
}else{
   print_admin_table("<center>$phrases[err_wrong_url]</center>");
               }
 }
 
 
 //-------------------- Add Files Form -----------------
 if($action=="movie_files_add"){

     

   $qr=db_query("select id,cat,name from movies_data where id='$id'");

    if(db_num($qr)){
            $data = db_fetch($qr);

 if_movie_admin($id); 
 
 
            print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$data[id]'>$data[name]</a> / $phrases[add_files]");  
            
 if(!$add_limit){
$add_limit = $settings['movies_add_limit'] ;
  }

print "<center><p align=center class=title>  $phrases[add_files] </p>

<form method=\"POST\" action=\"index.php\">

      <input type=\"hidden\" name=\"id\" value='$id'>
      <input type=hidden name=action value='movie_files_add'>
      <table width=30% class=grid>
      <tr><td align=center> $phrases[fields_count] : <input type=text name=add_limit value='$add_limit' size=3>
      &nbsp;&nbsp;<input type=submit value='$phrases[edit]'></td></tr></table></form>

      <br>

<form action=index.php method=post>
<input type=hidden name=action value='movie_files_add_ok'>
<input type=hidden name='id' value='$id'>
<table width=90% class=grid><tr><td> " ;
  for ($i=0;$i<$add_limit;$i++){
   if($tr_class=="row_2"){
       $tr_class="row_1" ;
   }else{
       $tr_class="row_2";
   }                             
           print "
     
        <table width=100% class='$tr_class'><tr>
      <td><b>#".($i+1)."</b></td> 
      <td><b>$phrases[the_name] : </b><input type=text size=20 name=\"name[$i]\"></td>
        <td><b>$phrases[download_url] : </b><input type=text size=30 name=url[$i] dir=ltr><br>
        <input type=checkbox name=\"custom_watch[$i]\" onClick=\"$('custom_watch_div[$i]').style.display=(this.checked ? 'inline':'none');\">$phrases[custom_watch_url]
        </td>
      
        </tr>
        <tr>
        <td colspan=2></td>
         <td id='custom_watch_div[$i]' style=\"display:none;\"><b>$phrases[watch_url] : </b><input type=text size=30 name=\"url_watch[$i]\" dir=ltr><br> 
         </tr>
        </table><hr size=1 class='separate_line'>";
        }
print "</td></tr>
<tr><td colspan=3 align=center><input type=submit value='$phrases[add]'></td></tr></table>
</form></center>";
}else{
    print_admin_table("<center>$phrases[err_wrong_url]</center>");
        }
        }
        
