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
 
 //----------------------- Settings --------------------------------
 if($action == "settings" || $action=="settings_edit"){
 if_admin();

  if($action=="settings_edit"){
     
 
 
  if(is_array($stng)){
 for($i=0;$i<count($stng);$i++) {

        $keyvalue = current($stng);

       db_query("update movies_settings set value='".db_escape($keyvalue,false)."' where name like '".db_escape(key($stng))."'");


 next($stng);
}
}

$stng_prv = array_map('intval',$stng_prv);
$default_prv = serialize($stng_prv);
db_query("update movies_settings set value='".db_escape($default_prv,false)."' where name like 'default_privacy_settings'"); 
 
 

         }


 load_settings();
 unset($prv_data);
 $prv_data = unserialize($settings['default_privacy_settings']);

 print "<center>
 <p align=center class=title>  $phrases[the_settings] </p>
 <form action=index.php method=post>
 <input type=hidden name=action value='settings_edit'>
 
 <fieldset style=\"width:70%\">
 <legend><b>$phrases[general_settings]</b></legend>
 <table width=100%>
  
 <tr><td>  $phrases[site_name] : </td><td><input type=text name=stng[sitename] size=30 value='$settings[sitename]'> &nbsp; </td></tr>
  <tr><td> $phrases[show_sitename_in_subpages] </td><td>";
  print_select_row("stng[sitename_in_subpages]",array($phrases['no'],$phrases['yes']),$settings['sitename_in_subpages']);
  print "</td></tr>
 
 
 <tr><td>  $phrases[section_name] : </td><td><input type=text name=stng[section_name] size=30 value='$settings[section_name]'></td></tr>
 <tr><td> $phrases[show_section_name_in_subpages] </td><td>";
  print_select_row("stng[section_name_in_subpages]",array($phrases['no'],$phrases['yes']),$settings['section_name_in_subpages']);
  print "</td></tr>
 
  <tr><td>  $phrases[copyrights_sitename] : </td><td><input type=text name=stng[copyrights_sitename] size=30 value='$settings[copyrights_sitename]'></td></tr>
 
  <tr><td>  $phrases[admin_email] : </td><td><input type=text dir=ltr name=stng[admin_email] size=30 value=\"$settings[admin_email]\"></td></tr>

  
  
  
 <tr><td> $phrases[page_dir] : </td><td><select name=stng[html_dir]>" ;
 if($settings['html_dir'] == "rtl"){$chk1 = "selected" ; $chk2=""; }else{ $chk2 = "selected" ; $chk1="";}
 print "<option value='rtl' $chk1>$phrases[right_to_left]</option>
 <option value='ltr' $chk2>$phrases[left_to_right]</option>
 </select>
 </td></tr>
  <tr><td>  $phrases[pages_lang] : </td><td><input type=text name=stng[site_pages_lang] size=30 value='$settings[site_pages_lang]'></td></tr>
    <tr><td>  $phrases[pages_encoding] : </td><td><input type=text name=stng[site_pages_encoding] size=30 value='$settings[site_pages_encoding]'></td></tr>
  <tr><td> $phrases[page_keywords] : </td><td><input type=text name=stng[header_keywords] size=30 value='$settings[header_keywords]'></td></tr>
   <tr><td> $phrases[page_description] : </td><td><input type=text name=stng[header_description] size=30 value='$settings[header_description]'></td></tr>

   

   
  </table>
  </fieldset> <br>
  
  
 
   <table width=70% class=grid>
  <tr><td>  $phrases[cp_enable_browsing]</td><td><select name=stng[enable_browsing]>";
  if($settings['enable_browsing']=="1"){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
  print "<option value='1' $chk1>$phrases[cp_opened]</option>
  <option value='0' $chk2>$phrases[cp_closed]</option>
  </select></td></tr>
  <tr><td>$phrases[cp_browsing_closing_msg]</td><td><textarea cols=30 rows=5 name=stng[disable_browsing_msg]>$settings[disable_browsing_msg]</textarea>
  </td></tr>
   </table>
   <br>
   
 <table width=70% class=grid>
  <tr><td>$phrases[default_style]</td><td><select name=stng[default_styleid]>";
  $qrt=db_query("select * from movies_templates_cats order by id asc");
while($datat =db_fetch($qrt)){
print "<option value=\"$datat[id]\"".iif($settings['default_styleid']==$datat['id']," selected").">$datat[name]</option>";
}
  print "</select>
  </td>
 </table>
                     <br>
                     
                      <fieldset style=\"width:70%\">
 <legend><b>$phrases[time_and_date]</b></legend>
 <table width=100%>
 <tr><td>$phrases[timezone]</td><td>
 <select name='stng[timezone]'> ";
  $zones = get_timezones();
  foreach($zones as $zone){
  print "<option value=\"$zone[value]\"".iif($zone[value]==$settings['timezone'], " selected").">$zone[name]</option>";           
  }
  
 print "</select></td></tr>
    <tr><td>  $phrases[date_format] </td><td><input type=text dir=ltr name=stng[date_format] size=30 value=\"$settings[date_format]\"></td></tr>
</table>
 
 </fieldset>
    <br>
    
    
 <table width=70% class=grid>

   <tr><td>  $phrases[images_cells_count] : </td><td><input type=text name=stng[movies_cells] size=5 value='$settings[movies_cells]'></td></tr>
     <tr><td>  $phrases[news_perpage]  : </td><td><input type=text name=stng[news_perpage] size=5 value='$settings[news_perpage]'></td></tr>

<tr><td>  $phrases[votes_expire_time] : </td><td><input type=text name=stng[votes_expire_hours] size=5 value='$settings[votes_expire_hours]'> $phrases[hour] </td></tr>
<tr><td>  $phrases[rating_exire_time] : </td><td><input type=text name=stng[rating_expire_hours] size=5 value='$settings[rating_expire_hours]'> $phrases[hour] </td></tr>

<tr><td>  $phrases[search_min_letters] : </td><td><input type=text name=stng[search_min_letters] size=5 value='$settings[search_min_letters]'>  </td></tr>

 </table>  <br>

 <fieldset style=\"width:70%\">
<legend><b>$phrases[the_movies]</b></legend> 
 <table width=100%>
 
     <tr><td>$phrases[show_movies_in_groups] : </td><td>";
 print_select_row("stng[movies_groups]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['movies_groups']);
 print "</td></tr>
 
   <tr><td>  $phrases[movies_add_fields] : </td><td><input type=text name=stng[movies_add_limit] size=5 value='$settings[movies_add_limit]'></td></tr>
  <tr><td>  $phrases[movies_perpage] : </td><td><input type=text name=stng[movies_perpage] size=5 value='$settings[movies_perpage]'></td></tr>
 
 
 
      <tr><td>  $phrases[movie_files_list_ajax] : </td><td>";
     print_select_row("stng[movie_files_list_ajax]",array("0"=>"$phrases[no]","1"=>"$phrases[yes]"),$settings['movie_files_list_ajax']);
   print " </td></tr>
   
     <tr><td>  $phrases[movie_subtitles_list_ajax] : </td><td>";
     print_select_row("stng[movie_subtitles_list_ajax]",array("0"=>"$phrases[no]","1"=>"$phrases[yes]"),$settings['movie_subtitles_list_ajax']);
   print " </td></tr>
   
   

   <tr><td>  $phrases[movie_photos_cells] : </td><td><input type=text name=stng[movie_photos_cells] size=5 value='$settings[movie_photos_cells]'>  </td></tr>
<tr><td>  $phrases[movie_photos_max] : </td><td><input type=text name=stng[movie_photos_max] size=5 value='$settings[movie_photos_max]'>  </td></tr>






  <tr><td> $phrases[movie_files_list_max] : </td><td><input type=text name=stng[movie_files_list_max] size=5 value='$settings[movie_files_list_max]'>  </td></tr>

  
   <tr><td> $phrases[movie_subtitles_list_max] : </td><td><input type=text name=stng[movie_subtitles_list_max] size=5 value='$settings[movie_subtitles_list_max]'>  </td></tr>

   <tr><td> $phrases[vote_movie] : </td><td>" ;
 
    print_select_row("stng[vote_movie]",array("0"=>$phrases['not_activated'],"1"=>$phrases['activated']),$settings['vote_movie']);

 print "</td></tr>
 
       </table>
       </fieldset>
       
   <br> ";
   
    /*
 
  <tr><td>Bad Words</td>
  <td><textarea cols=30 rows=5 name=\"stng[comments_bad_words]\">$settings[comments_bad_words]</textarea>
  </td></tr>
  
    <tr><td>Bad Words Replacement</td>
  <td><textarea cols=30 rows=5 name=\"stng[comments_bad_words_replacement]\">$settings[comments_bad_words_replacement]</textarea>
  </td></tr>
                 */
  
 
 print "
  <fieldset style=\"width:70%\">
 <legend><b>$phrases[send_movie]</b></legend>
  <table width=100%>
 <tr><td> $phrases[send_movie] : </td><td>" ;
   print_select_row("stng[snd2friend]",array("0"=>$phrases['not_activated'],"1"=>$phrases['activated']),$settings['snd2friend']);
 print "</td></tr>
 
  <tr><td>$phrases[security_code_in_send] : </td><td>";
 print_select_row("stng[send_sec_code]",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$settings['send_sec_code']);
 print "
 </td></tr>
 
  </table>
  </fieldset>  

  
              <br>
              
<fieldset style=\"width:70%\">
<legend><b>$phrases[movies_order]</b></legend> 
  <table width=100%>                
                   
   <tr><td> $phrases[visitors_can_sort_movies] : </td><td>" ;
 print_select_row("stng[visitors_can_sort_movies]",array($phrases['no'],$phrases['yes']),$settings['visitors_can_sort_movies']);
 print "</td></tr>
 <tr><td>$phrases[movies_default_sort] : </td><td>
<select size=\"1\" name=\"stng[movies_default_orderby]\">";
for($i=0; $i < count($orderby_checks);$i++) {

$keyvalue = current($orderby_checks);
if($keyvalue==$settings['movies_default_orderby']){$chk="selected";}else{$chk="";}

print "<option value=\"$keyvalue\" $chk>".key($orderby_checks)."</option>";;

 next($orderby_checks);
}
print "</select>&nbsp;&nbsp; <select name=stng[movies_default_sort]> ";
if($settings['movies_default_sort']=="asc"){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
print "<option value='asc' $chk1>$phrases[asc]</option>
<option value='desc' $chk2>$phrases[desc]</option>
</select>
</td></tr>
   </table>
   </fieldset>
   

<br>
<fieldset style=\"width:70%\">
<legend><b>$phrases[movie_cover_thumb]</b></legend> 
<table width=100%>
   <tr><td> $phrases[movie_cover_thumb_width] : </td><td><input type=text name=\"stng[movie_cover_thumb_width]\" size=5 value='$settings[movie_cover_thumb_width]'>  $phrases[pixel] </td></tr>

<tr><td> $phrases[movie_cover_thumb_height]  : </td><td><input type=text name=\"stng[movie_cover_thumb_height]\" size=5 value='$settings[movie_cover_thumb_height]'> $phrases[pixel] </td></tr>
 
 <tr><td>$phrases[fixed] : </td><td>";
 print_select_row("stng[cover_thumb_fixed]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['cover_thumb_fixed']);
 print "</td></tr>
 
  </table>
</fieldset>
 
 <br>
<fieldset style=\"width:70%\">
<legend><b>$phrases[the_photos]</b></legend> 
<table width=100%>
 
   <tr><td> $phrases[pic_max_width] : </td><td><input type=text name=stng[photo_resized_width] size=5 value='$settings[photo_resized_width]'>  $phrases[pixel] </td></tr>

<tr><td> $phrases[pic_max_height] : </td><td><input type=text name=stng[photo_resized_height] size=5 value='$settings[photo_resized_height]'> $phrases[pixel] </td></tr>



     <tr><td> $phrases[thumb_width] : </td><td><input type=text name=stng[photo_thumb_width] size=5 value='$settings[photo_thumb_width]'>  $phrases[pixel] </td></tr>

<tr><td> $phrases[thumb_height] : </td><td><input type=text name=stng[photo_thumb_height] size=5 value='$settings[photo_thumb_height]'> $phrases[pixel] </td></tr>

</table>
 </fieldset>
 
         <br>
  
 <fieldset style=\"width:70%\">
<legend><b>$phrases[the_actors]</b></legend> 
<table width=100%>
         <tr><td>$phrases[actors_show_in_groups] : </td><td>";
 print_select_row("stng[actors_show_in_groups]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['actors_show_in_groups']);
 print "</td></tr>
 
  <tr><td> $phrases[actors_per_page] : </td><td><input type=text name=stng[actors_per_page] size=5 value='$settings[actors_per_page]'>  </td></tr>

 
 <tr><td>  $phrases[movie_actors_cells] : </td><td><input type=text name=stng[movie_actors_cells] size=5 value='$settings[movie_actors_cells]'>  </td></tr>
<tr><td> $phrases[movie_actors_max] : </td><td><input type=text name=stng[movie_actors_max] size=5 value='$settings[movie_actors_max]'>  </td></tr>


<tr><td> $phrases[actor_photos_cells] : </td><td><input type=text name=stng[actor_photos_cells] size=5 value='$settings[actor_photos_cells]'>  </td></tr>
<tr><td>  $phrases[actor_photos_max] : </td><td><input type=text name=stng[actor_photos_max] size=5 value='$settings[actor_photos_max]'>  </td></tr>


</table>
</fieldset>


<br>
<fieldset style=\"width:70%\">
<legend><b>$phrases[actor_thumb]</b></legend> 
<table width=100%>
   <tr><td> $phrases[actor_thumb_width] : </td><td><input type=text name=\"stng[actor_thumb_width]\" size=5 value='$settings[actor_thumb_width]'>  $phrases[pixel] </td></tr>

<tr><td> $phrases[actor_thumb_height]  : </td><td><input type=text name=\"stng[actor_thumb_height]\" size=5 value='$settings[actor_thumb_height]'> $phrases[pixel] </td></tr>
 
 <tr><td>$phrases[fixed] : </td><td>";
 print_select_row("stng[actor_thumb_fixed]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['actor_thumb_fixed']);
 print "</td></tr>
 
 </table>
</fieldset>


<br>
<fieldset style=\"width:70%\">
<legend><b>$phrases[actor_pic]</b></legend> 
<table width=100%> 
 <tr><td> $phrases[pic_width] : </td><td><input type=text name=stng[actor_img_width] size=5 value='$settings[actor_img_width]'>  $phrases[pixel] </td></tr>
<tr><td> $phrases[pic_height] : </td><td><input type=text name=stng[actor_img_height] size=5 value='$settings[actor_img_height]'> $phrases[pixel] </td></tr>
<tr><td>$phrases[fixed] : </td><td>";
print_select_row("stng[actor_img_fixed]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['actor_img_fixed']);
print " </td></tr>

  </table>
</fieldset>

<br> 
<fieldset style=\"width:70%\">
<legend><b>$phrases[the_comments]</b></legend> 
<table width=100%> 
     <tr><td> $phrases[comments_max_letters] : </td><td><input type=text name=stng[comments_max_letters] size=5 value='$settings[comments_max_letters]'>  </td></tr>
 <tr><td> $phrases[commets_per_request] : </td><td><input type=text name=stng[commets_per_request] size=5 value='$settings[commets_per_request]'>  </td></tr>


     <tr><td>  $phrases[movies_comments] : </td><td>";
     print_select_row("stng[enable_comments]",array("0"=>"$phrases[not_activated]","1"=>"$phrases[activated]"),$settings['enable_comments']);
   print " </td></tr>
 

 
 
                  
    <tr><td> $phrases[movie_photo_comments]  : </td><td>" ;
  print_select_row("stng[enable_photo_comments]",array("0"=>"$phrases[not_activated]","1"=>"$phrases[activated]"),$settings['enable_photo_comments']);

 print "</td></tr>
 
   <tr><td>  $phrases[actors_comments] : </td><td>" ;
    print_select_row("stng[enable_actor_comments]",array("0"=>"$phrases[not_activated]","1"=>"$phrases[activated]"),$settings['enable_actor_comments']);

 print "</td></tr>
 
 
    <tr><td> $phrases[actor_photo_comments]  : </td><td>" ;
    print_select_row("stng[enable_actor_photo_comments]",array("0"=>"$phrases[not_activated]","1"=>"$phrases[activated]"),$settings['enable_actor_photo_comments']);

 print "</td></tr>
 
 
 
  <tr><td>  $phrases[news_comments] : </td><td>";
     print_select_row("stng[enable_news_comments]",array("0"=>"$phrases[not_activated]","1"=>"$phrases[activated]"),$settings['enable_news_comments']);
   print " </td></tr>
   
      <tr><td>  $phrases[comments_auto_activate]  : </td><td>" ;
         print_select_row("stng[comments_auto_activate]",array("0"=>"$phrases[no]","1"=>"$phrases[yes]"),$settings['comments_auto_activate']);
print "</td></tr>  

 
   
   
 </table>
 </fieldset>
           <br>
           
 <fieldset style=\"width:70%\">
<legend><b>$phrases[the_votes]</b></legend> 
  <table width=100%>                
                   
 <tr><td>$phrases[show_prev_votes] : </td><td><select name=stng[other_votes_show]>" ;
 if($settings['other_votes_show']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 <tr><td> $phrases[max_count] : </td><td><input type=text name=stng[other_votes_limit] dir=ltr size=4 value='$settings[other_votes_limit]'> </td></tr>

  <tr><td>$phrases[orderby] : </td><td> ";
  print_select_row("stng[other_votes_orderby]",array("rand()"=>"$phrases[random]","id asc"=>"$phrases[the_date] $phrases[asc]","id desc"=>"$phrases[the_date] $phrases[desc]"),$settings['other_votes_orderby']);
  print "</td></tr>
 </table>
 </fieldset>
 
       
   
                   <br>
<fieldset style=\"width:70%\">
<legend><b>$phrases[cp_statics]</b></legend> 

 <table width=100%>


 <tr><td>$phrases[os_and_browsers_statics] : </td><td><select name=stng[count_visitors_info]>" ;
 if($settings['count_visitors_info']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

  <tr><td>$phrases[visitors_hits_statics] : </td><td><select name=stng[count_visitors_hits]>" ;
 if($settings['count_visitors_hits']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

  <tr><td>$phrases[online_visitors_statics] : </td><td><select name=stng[count_online_visitors]>" ;
 if($settings['count_online_visitors']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 
     <tr><td> $phrases[show_online_members_count] : </td><td>";
 print_select_row("stng[online_members_count]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['online_members_count']);
 print "</td></tr>


    </table>
    
    </fieldset>
 

  
 <br>
 <fieldset style=\"width:70%\">
 <legend><b>$phrases[the_reports]</b></legend>
  <table width=100%>

  <tr><td> $phrases[report_do] : </td><td>" ;
  print_select_row("stng[reports_enabled]",array("0"=>$phrases['not_activated'],"1"=>$phrases['activated']),$settings['reports_enabled']);
 print "</td></tr>
 
 <tr><td>$phrases[visitors_can_send_reports] : </td><td>";
 print_select_row("stng[reports_for_visitors]",array("0"=>$phrases['no'],"1"=>$phrases['yes']),$settings['reports_for_visitors']);
 print "
 </td></tr>
 
 <tr><td>$phrases[security_code_in_report] : </td><td>";
 print_select_row("stng[report_sec_code]",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$settings['report_sec_code']);
 print "
 </td></tr>
 
 </table>
 </fieldset>
 
 <br>

 
 <fieldset style=\"width:70%;\">
 <legend><b>$phrases[mailing_settings]</b></legend>
  <table width=100%>
 <tr><td>$phrases[emails_msgs_default_type] : </td><td><select name=stng[mailing_default_use_html]>" ;
 if($settings['mailing_default_use_html']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>HTML</option>
 <option value=0 $chk2>TEXT</option>
 </select>
 </td></tr>
 <tr><td> $phrases[emails_msgs_default_encoding] : </td><td><input type=text name=stng[mailing_default_encoding] size=20 value='$settings[mailing_default_encoding]'> <br> * $phrases[leave_blank_to_use_site_encoding]</td></tr>

 <tr><td>  $phrases[mailing_email] : </td><td><input type=text dir=ltr name=stng[mailing_email] size=30 value='$settings[mailing_email]'></td></tr>
 
 </table>
 </fieldset><br>";
   //--------------- Load Settings Plugins --------------------------
$pls = load_plugins("settings.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
//----------------------------------------------------------------

 print "
  <fieldset style=\"width:70%;\">
 <legend><b>$phrases[the_members]</b></legend>
 <table width=100%>
    <tr><td>$phrases[registration] : </td><td>";
    print_select_row("stng[members_register]",array("0"=>$phrases['cp_closed'],"1"=>$phrases['cp_opened']),$settings['members_register']);
print "
 </td></tr>
 
 <tr><td>$phrases[security_code_in_registration] : </td><td>";
 print_select_row("stng[register_sec_code]",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$settings['register_sec_code']);
 print "
 </td></tr>

  <tr><td>$phrases[auto_email_activate]: </td><td><select name=stng[auto_email_activate]>" ;
 if($settings['auto_email_activate']){$chk1 = "selected" ; $chk2 ="" ;}else{ $chk2 = "selected" ; $chk1 ="" ;}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>

 <tr><td>  $phrases[msgs_count_limit] : </td><td><input type=text name=stng[msgs_count_limit] size=5 value='$settings[msgs_count_limit]'>  $phrases[message] </td></tr>

<tr><td>  $phrases[username_min_letters] : </td><td><input type=text name=stng[register_username_min_letters] size=5 value='$settings[register_username_min_letters]'> </td></tr>

<tr><td> $phrases[username_exludes] : </td><td><input type=text name=stng[register_username_exclude_list] dir=ltr size=20 value='$settings[register_username_exclude_list]'> </td></tr>


  </table> 
  </fieldset>
  
  <br>
  <fieldset style=\"width:70%;\">
 <legend><b>$phrases[profile_picture]</b></legend>
 <table width=100%>     
     <tr><td> $phrases[profile_picture] </td><td>" ;
         print_select_row("stng[members_profile_pictures]",array("0"=>"$phrases[not_activated]","1"=>"$phrases[activated]"),$settings['members_profile_pictures']);
print "</td></tr>  

<tr><td> $phrases[pic_max_width] : </td><td><input type=text name=\"stng[profile_pic_width]\" size=5 value='$settings[profile_pic_width]'>  $phrases[pixel] </td></tr>
<tr><td> $phrases[pic_max_height]  : </td><td><input type=text name=\"stng[profile_pic_height]\" size=5 value='$settings[profile_pic_height]'> $phrases[pixel] </td></tr>



<tr><td> $phrases[thumb_width] : </td><td><input type=text name=\"stng[profile_pic_thumb_width]\" size=5 value='$settings[profile_pic_thumb_width]'>  $phrases[pixel] </td></tr>
<tr><td> $phrases[thumb_height]  : </td><td><input type=text name=\"stng[profile_pic_thumb_height]\" size=5 value='$settings[profile_pic_thumb_height]'> $phrases[pixel] </td></tr>
 
 
 
 
</table>
</fieldset>


  <br>
  <fieldset style=\"width:70%;\">
  <legend><b>$phrases[default_privacy_settings]</b></legend>
  <table width='100%'>
  <tr><td>$phrases[profile_view]</td><td>";
  print_select_row("stng_prv[profile]",$privacy_settings_array,$prv_data['profile']);
  print "</td>
  </tr>
 
    <tr><td>$phrases[gender]</td><td>";
  print_select_row("stng_prv[gender]",$privacy_settings_array,$prv_data['gender']);
  print "</td>
  </tr>
  
  
  <tr><td>$phrases[birth]</td><td>";
  print_select_row("stng_prv[birth]",$privacy_settings_array,$prv_data['birth']);
  print "</td>
  </tr>
  
  
  <tr><td>$phrases[country]</td><td>";
  print_select_row("stng_prv[country]",$privacy_settings_array,$prv_data['country']);
  print "</td>
  </tr>
  
   <tr><td>$phrases[last_login]</td><td>";
  print_select_row("stng_prv[last_login]",$privacy_settings_array,$prv_data['last_login']);
  print "</td>
  </tr>
  
   <tr><td>$phrases[online_status]</td><td>";
  print_select_row("stng_prv[online]",$privacy_settings_array,$prv_data['online']);
  print "</td>
  </tr>";
  
  $qrfp = db_query("select * from movies_members_sets order by ord");
   if(db_num($qrfp)){
       while($datafp = db_fetch($qrfp)){
       print "
  <tr><td>$datafp[name]</td><td>";
  print_select_row("stng_prv[field_".$datafp['id']."]",$privacy_settings_array,$prv_data["field_$datafp[id]"]);
  print "</td>
  </tr>";
       }
   }
   
  
  
  print "
    
  
    <tr><td>$phrases[fav_movies]</td><td>";
  print_select_row("stng_prv[fav_movies]",$privacy_settings_array,$prv_data['fav_movies']);
  print "</td>
  </tr> 
  
  <tr><td>$phrases[receive_pm_from]</td><td>";
  print_select_row("stng_prv[messages]",$privacy_settings_array,$prv_data['messages']);
  print "</td>
  </tr> 
  
  
  </table>
  </fieldset><br>
  
 
  <fieldset style=\"width:70%;\">
  <legend><b>$phrases[uploader_system]</b></legend> 
  
 <table width=100%>

 <tr><td>  $phrases[uploader_system] : </td><td><select name=stng[uploader]>" ;
 if($settings['uploader']){$chk1 = "selected" ; $chk2=""; }else{ $chk2 = "selected" ; $chk1="";}
 print "<option value=1 $chk1>$phrases[enabled]</option>
 <option value=0 $chk2>$phrases[disabled]</option>
 </select>
 </td></tr>
 <tr><td> $phrases[disable_uploader_msg]  : </td><td><input type=text name=stng[uploader_msg] size=30 value='$settings[uploader_msg]'></td></tr>
 <tr><td>  $phrases[uploader_path] : </td><td><input dir=ltr type=text name=stng[uploader_path] size=30 value='$settings[uploader_path]'></td></tr>
 <tr><td>  $phrases[uploader_allowed_types] : </td><td><input dir=ltr type=text name=stng[uploader_types] size=30 value='$settings[uploader_types]' style=\"font-family:Arial, Helvetica, sans-serif;font-weight:bold;\">
  <br><font size=1 color='#ACACAC'>$phrases[use_comma_between_types]</font></td></tr>

<tr><td> $phrases[uploader_thumb_width] : </td><td><input type=text name=stng[uploader_thumb_width] size=5 value='$settings[uploader_thumb_width]'> $phrases[pixel] </td></tr>
<tr><td>  $phrases[uploader_thumb_hieght]  : </td><td><input type=text name=stng[uploader_thumb_hieght] size=5 value='$settings[uploader_thumb_hieght]'> $phrases[pixel] </td></tr>
</table>
</fieldset>


 <br>
 <input type=submit value=\"$phrases[edit]\" style=\"width:100;height:30;\">
       <br><br>
 </center>
 </form>" ;

         }
 ?>
