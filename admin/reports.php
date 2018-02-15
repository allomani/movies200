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

if($action=="reports" || $action=="reports_activate" || $action=="reports_del" || $action=="reports_edit_ok"){

if_admin("reports");
  
  print "<p align=center class=title>$phrases[the_reports]</p>";  




//---- del -----
if($action=="reports_del"){
     $id = (array) $id;
    for($i=0;$i<count($id);$i++){
    db_query("delete from movies_reports where id='".$id[$i]."'");
    }
}


  
if(!$op){

  
$qr = db_query("select count(*) as count,report_type from movies_reports group by report_type");


if(db_num($qr)){
print "
<center>

<table width=80% class='grid'><tr><td><b>$phrases[report_type]</b></td><td><b>$phrases[new_reports]</b></td><td><b>$phrases[reports_count]</b></td></tr>";
while($data=db_fetch($qr)){

$new_reports = db_qr_fetch("select count(*) as count from movies_reports where report_type like '$data[report_type]' and opened=0");

print "<tr><td><a href='index.php?action=reports&op=$data[report_type]'>".$reports_types_phrases[$data['report_type']]."</a></td><td>$new_reports[count]</td><td>$data[count] </td></tr>";

} 

print "</table>";
}else{
    print_admin_table("<center>$phrases[no_reports]</center>");
}

}else{

  print "<img src='images/arrw.gif'>&nbsp; <a href='index.php?action=reports'>$phrases[the_reports]</a> / ".$reports_types_phrases[$op]." <br><br>";
   
   $start = (int) $start;
  $reports_perpage = 50;
  $page_string = "index.php?action=reports&op=".htmlspecialchars($op)."&start={start}";
  
  
                    
  $qr = db_query("select * from movies_reports where report_type like '".db_escape($op)."' order by id desc limit $start,$reports_perpage");
  if(db_num($qr)){
      
  $reports_count = db_qr_fetch("select count(*) as count from movies_reports where report_type like '".db_escape($op)."'");
 
 
 
      print "<form action='index.php' method='post' name='submit_form'>
      <input type=hidden name='op' value='".htmlspecialchars($op)."'>
      <table width=100% class=grid>";
      while($data=db_fetch($qr)){
      
       
         
 
         if($tr_color == "row_1"){
             $tr_color = "row_2";
         }else{
             $tr_color = "row_1";
         }
         
          print "<tr class='$tr_color'>
         
          <td width=10><input type='checkbox' name=\"id[]\" value=\"$data[id]\"></td>
           <td width=16>".iif(!$data['opened'],"<img src='images/new.gif'>")."</td>  
          <td><a href=\"index.php?action=report_view&id=$data[id]\">$phrases[report_number_x] $data[id]</a></td>
        
          <td>$data[content]</td>
          <td>".date("d-m-Y h:s:i",$data['date'])."</td>
          <td align='$global_align_x'>
   <a href='index.php?action=reports_del&id=$data[id]&op=$data[report_type]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
          </td></tr>";
      }
      print "
      <tr><td colspan=6>
      
       <img src='images/arrow_".$global_dir.".gif'>    
        
          <a href='#' onclick=\"CheckAll(); return false;\"> $phrases[select_all] </a> -
          <a href='#' onclick=\"UncheckAll(); return false;\">$phrases[select_none] </a>
          &nbsp;  &nbsp;
         
          <select name='action'>
         <option value='reports_del'>$phrases[delete]</option>
         </select>
        <input type=submit value=\"$phrases[do_button]\" onClick=\"return confirm('$phrases[are_you_sure]');\"> 
        
        </td></tr></table></form>";
        
        
        print_pages_links($start,$reports_count['count'],$reports_perpage,$page_string);

  }else{
      print_admin_table("<center>$phrases[no_reports]</center>");  
  }   
    
}


}


//------ report view -------
if($action=="report_view"){
if_admin("reports");

  print "<p align=center class=title>$phrases[the_reports]</p>";  

  

 $qr = db_query("select * from movies_reports where id='$id'");
  if(db_num($qr)){
      
      $data = db_fetch($qr);
    
    db_query("update movies_reports set opened=1 where id='$id'");
    
    
     print "<img src='images/arrw.gif'>&nbsp; <a href='index.php?action=reports'>$phrases[the_reports]</a> / <a href='index.php?action=reports&op=$data[report_type]'>".$reports_types_phrases[$data['report_type']]."</a> / $data[id] <br><br>";
   
     
      print "<table width=100% class=grid>
      <tr><td><b>$phrases[from]</b></td><td>";
     if($data['uid']){
     print "<a href=\"$scripturl/".str_replace("{id}",$data['uid'],$links['links_profile'])."\" target=_blank>$data[name]</a>";
     }else{
     print "<a href=\"mailto:$data[email]\" target=_blank>$data[name]</a>";     
     }
     
     print "</td></tr>
     
     <tr><td valign=top><b>$phrases[the_content]</b></td><td><textarea cols=40 rows=8>$data[content]</textarea></td><tr>";
    
    //------ comments ------// 
     if($data['report_type']=="comment"){
        print "<tr><td><b>$phrases[the_comment]</b></td>
         <td>";
          
         $data_comment = db_qr_fetch("select id,content,comment_type from movies_comments where id='$data[fid]'");
        if($data_comment['id']){
         print "
         <textarea cols=40 rows=8>$data_comment[content]</textarea>
         <br> <a href=\"index.php?action=comments_edit&op=$data_comment[comment_type]&id=$data[fid]\">$phrases[edit] / $phrases[delete] $phrases[the_comment]</a>";
        }else{
            print $phrases['comment_is_not_exist'];
        }
        
         print "</td><tr>";
    //------ member -------//     
     }elseif($data['report_type']=="member"){
         
     print "<tr><td><b>$phrases[the_member]</b></td>
         <td>";
         
         
      $data_profile = db_qr_fetch("select ".members_fields_replace("id").",".members_fields_replace("username")." from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='$data[fid]'",MEMBER_SQL);
        if($data_profile['id']){
  print "<a href=\"index.php?action=member_edit&id=$data[fid]\">$data_profile[username]</a>";
        }else{
            print $phrases['member_is_not_exist'];
        }  
        
       print "</td><tr>";        
     //----- movie file -----//      
     }elseif($data['report_type']=="movie_file"){
         
       print "<tr><td><b>$phrases[the_file]</b></td>
         <td>";
         
         
      $data_movie = db_qr_fetch("select movies_data.*,movies_files.name as file_name , movies_files.url as file_url from movies_data,movies_files where movies_data.id=movies_files.cat and movies_files.id='$data[fid]'");
      
          if($data_movie['id']){
  print iif($data_movie['file_name'],$data_movie['file_name'],basename($data_movie['file_url']))." <b> $phrases[from] : </b> <a href=\"index.php?action=movie_files&id=$data_movie[id]\">$data_movie[name]</a>";
        }else{
            print $phrases['movie_is_not_exist'];
        }  
        
       print "</td><tr>";    
         
         
     }
     
     
      
      
     print "
     <tr><td colspan=2 align='$global_align_x'>
       <a href='index.php?action=reports_del&id=$data[id]&op=$data[report_type]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>
  </td></tr>
  
     </table>";
      
  }else{
      print_admin_table("<center>$phrases[err_wrong_url]</center>");
  }
  
}
 
 
 ?>
