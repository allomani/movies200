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

print "<table width=100%>

<tr><td width=24><img src='images/home.gif' width=24></td><td bgcolor=#FFFFFF><a href='index.php'> $phrases[main_page] </a></td></tr>
</table>";

unset($admin_menu_content);

$admin_menu_content .= "<tr><td width=24><img src='images/movies.gif' width=24></td><td class=row_1><a href='index.php?action=movies'> $phrases[manage_movies_cats] </a></td></tr>";

if(if_admin("new_menu",true)){  
$admin_menu_content .= "<tr><td width=24><img src='images/hot_items.gif' width=24></td><td class=row_2><a href='index.php?action=new_menu'> $phrases[new_movies_list] </a></td></tr>";
}

if(if_admin("actors",true)){  
$admin_menu_content .= "<tr><td width=24><img src='images/actors.gif' width=24></td><td class=row_1><a href='index.php?action=actors'> $phrases[the_actors]</a></td></tr>";
}

//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------


if(if_admin("movies_fields",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/custom_fields.gif' width=24></td><td class=row_1><a href='index.php?action=movies_fields'> $phrases[movies_fields]</a></td></tr>\n";
}

if(if_admin("players",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/players.gif' width=24></td><td class=row_2><a href='index.php?action=players'> $phrases[the_players] </a></td></tr>";
}


//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------


//-----------------------------
if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/blocks.gif' width=24></td><td class=row_1><a href='index.php?action=blocks'> $phrases[the_blocks] </a></td></tr>";
}



if(if_admin("votes",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/votes.gif' width=24></td><td class=row_2><a href='index.php?action=votes'> $phrases[the_votes] </a></td></tr>";
}

if(if_admin("news",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/news.gif' width=24></td><td class=row_1><a href='index.php?action=news'> $phrases[the_news] </a></td></tr>";
}

if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/pages.gif' width=24></td><td class=row_2><a href='index.php?action=pages'> $phrases[the_pages] </a></td></tr>";
}




if(if_admin("adv",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/adv.gif' width=24></td><td class=row_1><a href='index.php?action=banners'> $phrases[the_banners] </a></td></tr>";   
}

//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------


if(if_admin("comments",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/comments.gif' width=24></td><td class=row_1><a href='index.php?action=comments'> $phrases[the_comments]</a></td></tr>\n";
}

if(if_admin("reports",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/reports.gif' width=24></td><td class=row_2><a href='index.php?action=reports'> $phrases[the_reports]</a></td></tr>\n";
}




//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------

if(if_admin("members",true)){
print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[the_members]</legend>
<table width=100%>";
print "<tr><td width=24><img src='images/members.gif' width=24></td><td class=row_1><a href='index.php?action=members'> $phrases[cp_mng_members]</a></td></tr>\n";
print "<tr><td width=24><img src='images/members_custom_fields.gif' width=24></td><td class=row_2><a href='index.php?action=members_fields'> $phrases[members_custom_fields]</a></td></tr>\n";
print "<tr><td width=24><img src='images/members_mailing.gif' width=24></td><td class=row_1><a href='index.php?action=members_mailing'> $phrases[members_mailing]</a></td></tr>\n";

print "</table></fieldset>";  
}

if(if_admin("",true)){  
    print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[members_connectors]</legend>
<table width=100%>";
    
print "<tr><td width=24><img src='images/clients_remote_db.gif' width=24></td><td class=row_1><a href='index.php?action=members_remote_db'>$phrases[cp_members_remote_db]</a></td></tr>\n";
print "<tr><td width=24><img src='images/db_clean.gif' width=24></td><td class=row_2><a href='index.php?action=members_local_db_clean'>$phrases[members_local_db_clean_wizzard]</a></td></tr>\n";

print "</table></fieldset>";  
}



if(if_admin("",true)){
print "<br>
<fieldset style=\"padding: 2\">
<legend>$phrases[the_database]</legend>
<table width=100%>
<tr><td width=24><img src='images/db_info.gif' width=24></td><td class=row_1><a href='index.php?action=db_info'>$phrases[cp_db_check_repair]</a></td></tr>
<tr><td width=24><img src='images/db_backup.gif' width=24></td><td class=row_2><a href='index.php?action=backup_db'>$phrases[backup]</a></td></tr>
</table></fieldset>";
}


//--------------- Load Menu Plugins --------------------------
$pls = load_plugins("menu.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
//------------------------//
    


if(if_admin("templates",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/templates.gif' width=24></td><td class=row_1><a href='index.php?action=templates'> $phrases[the_templates] </a></td></tr>";
}


if(if_admin("phrases",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/phrases.gif' width=24></td><td class=row_2><a href='index.php?action=phrases'>$phrases[the_phrases]</a></td></tr>";
}

if(if_admin("",true)){ 
$admin_menu_content .= "<tr><td width=24><img src='images/seo_settings.gif' width=24></td><td class=row_1><a href='index.php?action=seo_settings'> $phrases[seo_settings]</a></td></tr>";
}



//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------



if(if_admin("",true)){
    
if(if_admin("",true)){
$admin_menu_content .= "<tr><td width=24><img src='images/statics.gif' width=24></td><td class=row_1><a href='index.php?action=counters'> $phrases[the_counters]</a></td></tr> ";
}
 
$admin_menu_content .= "<tr><td width=24><img src='images/hooks.gif' width=24></td><td class=row_2><a href='index.php?action=hooks'>$phrases[cp_hooks]</a></td></tr>\n";

$admin_menu_content .= "<tr><td width=24><img src='images/stng.gif' width=24></td><td class=row_1><a href='index.php?action=settings'> $phrases[the_settings]</a></td></tr>";
}

//--------------------
if($admin_menu_content){
print "<br>
<fieldset style=\"padding: 2\">
<table width=100%>";
print $admin_menu_content; 
print "</table></fieldset>";
unset($admin_menu_content); 
}
//---------------------


print "<br> 
<fieldset style=\"padding: 2\"> 
<table width=100%>";
print "<tr><td width=24><img src='images/users2.gif' width=24></td><td class=row_1><a href='index.php?action=users'>$phrases[users_and_permissions]</a></td></tr>";

if(if_admin("",true)){ 
print "<tr><td width=24><img src='images/access_log.gif' width=24></td><td class=row_2><a href='index.php?action=access_log'>$phrases[access_log]</a></td></tr>";
}

print "<tr><td width=24><img src='images/logout.gif' width=24></td><td class=row_1><a href='index.php?action=logout'> $phrases[logout] </a></td></tr>";
print "</table></fieldset><br>";
?>
