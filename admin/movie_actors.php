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
 
if($action=="movie_actors" ||  $action=="movie_actors_add" || $action=="movie_actors_del"){
    
    $id = (int) $id;
 
 
  if_movie_admin($id); 
  
  
if($action=="movie_actors_add"){
    $actor_id = (array) $actor_id;
    
    foreach($actor_id as $iid){
        $iid = intval($iid);
     
     $numa = db_qr_fetch("select count(*) as count from movies_actors where id='$iid'");  
      if($numa['count']){  
      $num = db_qr_fetch("select count(*) as count from movies_actors_index where movie_id='$id' and actor_id='$iid'");
      if(!$num['count']){
          db_query("insert into movies_actors_index (actor_id,movie_id) values ('$iid','$id')");
          
//-------update Ord-----
$c=1;
$qr=db_query("select id from movies_actors_index where movie_id='$id' order by ord asc");
while($data=db_fetch($qr)){
db_query("update movies_actors_index set ord='$c' where id='$data[id]'");
$c++;
}
//------------


      }
    }else{
        print_admin_table("<center> $phrases[err_invalid_id] </center>");
    }
    }
     
}


//-------- Movie Actor Del --------   
if($action=="movie_actors_del"){
    $actor_id = (array) $actor_id;
    
    foreach($actor_id as $iid){
    db_query("delete from movies_actors_index where actor_id='".intval($iid)."' and movie_id='$id'");
    }
}

    
      $qr=db_query("select id,name,cat from movies_data where id='$id'");
    if(db_num($qr)){
            $data = db_fetch($qr);
            
print_admin_path_links($data['cat'],"<a href='index.php?action=movie_edit&id=$id'>$data[name]</a> / $phrases[the_actors]</a>"); 

   
print "<p align=center class=title> $phrases[the_actors]</p>";

//------ add form --------
print " <script src=\"$scripturl/js/jquery.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"$scripturl/js/jquery.fcbkcomplete.js\" type=\"text/javascript\" charset=\"utf-8\"></script>";
    
    
    print "<form action=\"index.php\" method=\"post\" accept-charset=\"utf-8\">
    <input type='hidden' name='action' value='movie_actors_add'>
    <input type='hidden' name='id' value='$id'>  
    
     <br>
   <center>
   <table width=90%><tr><td valign=top>
    
   <fieldset style=\"width:90%;text-align:$global_align;\">
   <legend>$phrases[add_by_name]</legend>
     <table width=100%><tr><td>
      <select id=\"actor_id\" name=\"actor_id\"></select></td>
   
      <td><input type=\"submit\" value=\"$phrases[add]\"> 
      </td></tr></table> 
      </fieldset> <br><br>
     </center> 
    </form>
    
    </td><td valign=top>
    
    <form action=\"index.php\" method=\"post\" name='add_actor_by_id_form'>
    <input type='hidden' name='action' value='movie_actors_add'>
    <input type='hidden' name='id' value='$id'> 
    
      <fieldset style=\"width:100%;height:55;text-align:$global_align;\">
   <legend>$phrases[add_by_id]</legend>
     <table width=100%><tr><td>
     <input type=text size=3 name='actor_id'></td>
   
      <td><input type=\"submit\" value=\"$phrases[add]\"> 
      </td><td><a  href=\"javascript:actors_list()\"><img src=\"images/list.gif\" title=\"$phrases[actors_list]\" border=0></a></td></tr></table> 
      </fieldset> <br><br>
     </center> 
    </form>
    
    </td></tr></table>";
    ?>  
    <script language="JavaScript">
  jQuery.noConflict();

        jQuery(document).ready(function() 
        {        
    
          
          jQuery("#actor_id").fcbkcomplete({
            json_url: "ajax.php?action=get_actors_json",
            cache: false,
            filter_case: false,
            filter_hide: true,
            firstselected: true,
            filter_selected: true,
            newel: false        
          });         
        }); 
    </script>
<?

//--------------
$qr = db_query("select movies_actors.* from movies_actors,movies_actors_index where movies_actors.id = movies_actors_index.actor_id and movies_actors_index.movie_id='$id' order by movies_actors_index.ord");
if(db_num($qr)){
    
    print "<center>     
       <form action='index.php' method=post name='submit_form'>
          <input type=hidden name='action' value='movie_actors_del'>
          <input type=hidden name='id' value='$id'>
    <table width=90% class=grid><tr><td colspan=2 width=100%>
    <div id='movie_actors_list_div'>";
    
  while($data=db_fetch($qr)){
    print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
       
        <table width=100%><tr><td width=2><input type='checkbox' name='actor_id[]' value='$data[id]'>
        </td>
         <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img alt='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td><td width=70%>$data[name]</td>
      <td><a href=\"index.php?action=movie_actors_del&actor_id=$data[id]&id=$id\" onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a></td>
       </tr></table></div>
       ";
    }
      print "</div></td></tr>
    
    


         <tr><td width=30><img src='images/arrow_".$global_dir.".gif'></td>   
          <td width=100%>

          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
          <input type=submit value=' $phrases[delete] '>
          </td></tr></table>
        </form><br></center>
        
        
        <script>
        init_sortlist('movie_actors_list_div','set_movie_actors_sort');
        </script>";
        
        
}else{
    print_admin_table("<center> $phrases[no_actors] </center>");
}
  
 
  
  
      
   

  




}else{
   print_admin_table("<center>$phrases[err_wrong_url]</center>");
               }

}
