<?
 if(!defined('IS_ADMIN')){die('No Access');}   
 
//------------------------------------- New Stores Menu ------------------------------
if($action=="new_menu" || $action=="new_menu_add" || $action=="new_menu_del"){

if_admin("new_menu");

   print "<p align=center class=title>$phrases[new_movies_list]</p>";
   
if($action=="new_menu_add"){
$movie_id = (array) $movie_id;

foreach($movie_id as $sid){
          $sid = intval($sid);
        $cntx = db_qr_fetch("select count(*) as count from movies_data where id='$sid'");
     if($cntx['count']){
         
       $cntm = db_qr_fetch("select count(*) as count from movies_new_menu where movie_id='$sid'");
        
        if(!$cntm['count']){
        db_query("insert into movies_new_menu (movie_id) values ('$sid')");
        
//-------update Ord-----
$c=1;
$qr=db_query("select id from movies_new_menu order by ord asc");
while($data=db_fetch($qr)){
db_query("update movies_new_menu set ord='$c' where id='$data[id]'");
$c++;
}
//------------


        }
        }else{
        print_admin_table("<center> $phrases[err_invalid_id] </center>");    
        }
        }
}


        
if($action=="new_menu_del"){
$id = (array) $id;
foreach($id as $idx){
 db_query("delete from movies_new_menu where id='".intval($idx)."'");
  }
}

//------ add form --------
print " <script src=\"$scripturl/js/jquery.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <script src=\"$scripturl/js/jquery.fcbkcomplete.js\" type=\"text/javascript\" charset=\"utf-8\"></script>";
    
    
    print "<form action=\"index.php\" method=\"post\" accept-charset=\"utf-8\">
    <input type='hidden' name='action' value='new_menu_add'>
   
    
     <br>
   <center>
   <table width=90%><tr><td valign=top>
    
   <fieldset style=\"width:90%;text-align:$global_align;\">
   <legend>$phrases[add_by_name]</legend>
     <table width=100%><tr><td>
      <select id=\"actor_id\" name=\"movie_id\"></select></td>
   
      <td><input type=\"submit\" value=\"$phrases[add]\"> 
      </td></tr></table> 
      </fieldset> <br><br>
     </center> 
    </form>
    
    </td><td valign=top>
    
    <form action=\"index.php\" method=\"post\" name='add_movie_by_id_form'>
    <input type='hidden' name='action' value='new_menu_add'>
    
    
      <fieldset style=\"width:100%;height:55;text-align:$global_align;\">
   <legend>$phrases[add_by_id]</legend>
     <table width=100%><tr><td>
     <input type=text size=3 name='movie_id'></td>
   
      <td><input type=\"submit\" value=\"$phrases[add]\"> 
      </td><td><a  href=\"javascript:movies_list()\"><img src=\"images/list.gif\" title=\"$phrases[movies_list]\" border=0></a></td></tr></table> 
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
            json_url: "ajax.php?action=get_movies_json",
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

            
 //---------------------
$qr=db_query("select * from movies_new_menu order by ord asc");  
if(db_num($qr)){  
          print "
          <form action='index.php' method=post name='submit_form'>
          <input type=hidden name='action' value='new_menu_del'>
          <table width=90% class=grid><tr><td colspan=2 width=100%>
         <div id=\"new_movies_list\"> ";
while($data = db_fetch($qr)){

        $qr2=db_query("select * from movies_data where id='$data[movie_id]'");
        if(db_num($qr2)){
         $data2= db_fetch($qr2);
        print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
       
        <table width=100%><tr><td width=2><input type='checkbox' name='id[]' value='$data[id]'>
        </td>
         <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img title='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td><td width=70%>$data2[name]</td>
      <td><a href=\"index.php?action=new_menu_del&id=$data[id]\" onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a></td>
       </tr></table></div>
       ";
       }else{
       db_query("delete from movies_new_menu where movie_id='$data[movie_id]'");
       }
        }
        print "</div></td></tr>
         <tr><td width=30><img src='images/arrow_".$global_dir.".gif'></td>   
          <td width=100%>

          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
          <input type=submit value=' $phrases[delete] '>
          </td></tr></table>
        </form></table><br></center>";
        
               
  print "<script type=\"text/javascript\">
        init_sortlist('new_movies_list','set_new_movies_list_sort');
</script>";

}else{
    print_admin_table("<center> $phrases[no_movies] </center>");
}      
        
        }
?>