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

 
//---------------------- Movies Fields ---------------------
if($action=="movies_fields" || $action=="movies_fields_edit_ok" || $action=="movies_fields_add_ok" || $action=="movies_fields_del"){

 if_admin("movies_fields");
 
if($action=="movies_fields_del"){
$id=intval($id);
db_query("delete from movies_fields_sets where id='$id'");
db_query("ALTER TABLE  movies_data  DROP  `field_".$id."`",MEMBER_SQL);
}

if($action=="movies_fields_edit_ok"){
$id=intval($id);
if($name){
    $value=trim($value);
db_query("update movies_fields_sets set name='".db_escape($name)."',details='".db_escape($details)."',type='$type',value='".db_escape($value,false)."',style='$style_v',ord='".intval($ord)."',enable_search='".intval($enable_search)."' where id='$id'");
    }
}

if($action=="movies_fields_add_ok"){
$id=intval($id);
if($name){
    $value=trim($value);
db_query("insert into movies_fields_sets  (name,details,type,value,style,ord,enable_search) values('".db_escape($name)."','".db_escape($details)."','$type','".db_escape($value,false)."','$style_v','$ord','".intval($enable_search)."')");

$field_id = mysql_insert_id();

db_query("ALTER TABLE  movies_data ADD  `field_".$field_id."` TEXT NOT NULL",MEMBER_SQL);  
}
}


print "<p align=center class=title> $phrases[movies_fields]</p>

<p align=$global_align><a href='index.php?action=movies_fields_add'><img src='images/add.gif' border=0> $phrases[add_member_custom_field] </a></p>";




$qr= db_query("select * from movies_fields_sets order by ord asc");
if(db_num($qr)){
print "<center><table width=90% class=grid><tr><td>
<div id=\"movies_fields_list\">";

while($data=db_fetch($qr)){
print "<div id=\"item_$data[id]\" onmouseover=\"this.style.backgroundColor='#EFEFEE'\" onmouseout=\"this.style.backgroundColor='#FFFFFF'\">
       
       <table width=100%>
<tr>
 <td width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img title='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td>
      
      <td width=75%>";
if($data['required']){
    print "<b>$data[name]</b>";
    }else{
    print "$data[name]";
        }
        print "</td>
      
<td><a href='index.php?action=movies_fields_edit&id=$data[id]'>$phrases[edit]</a> - <a href='index.php?action=movies_fields_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td></tr>
</table></div>";

}
print "</div>
</td></table></center>";

 print "<script type=\"text/javascript\">
        init_sortlist('movies_fields_list','set_movies_fields_sort');
</script>";



}else{
print_admin_table("<center>$phrases[no_movies_fields] </center>");
    }


}

//---------- Add Member Field -------------
if($action=="movies_fields_add"){
 if_admin("movies_fields");
 
print "<img src='images/arrw.gif'>&nbsp; <a href='index.php?action=movies_fields'>$phrases[movies_fields]</a> / $phrases[add_custom_field]</a><br>";

print "<center>
<p align=center class=title>$phrases[add_custom_field]</p>
<form action=index.php method=post>
<input type=hidden name=action value='movies_fields_add_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>";
print "<tr><td><b> $phrases[the_name]</b> </td><td><input type=text size=20  name=name></td></tr>
<tr><td><b> $phrases[the_description] </b></b></td><td><input type=text size=30  name=details></td></tr>
<tr><td><b>$phrases[the_type]</b></td><td><select name=type>
<option value='text'>$phrases[textbox]</option>
<option value='textarea'>$phrases[textarea]</option>
<option value='select'>$phrases[select_menu]</option>
<option value='radio'>$phrases[radio_button]</option>
<option value='checkbox'>$phrases[checkbox]</option>
</select>
</td></tr>
<tr><td><b>$phrases[default_value_or_options]</b><br><br>$phrases[put_every_option_in_sep_line]</td><td>
<textarea name='value' rows=10 cols=30>$data[value]</textarea></td></tr>

<tr><td><b>$phrases[addition_style]</b> </td><td><input type=text size=30  name='style_v' value=\"$data[style]\" dir=ltr></td></tr>


<tr><td><b>$phrases[enable_search]</b></td><td>";
print_select_row("enable_search",array("1"=>$phrases['yes'],"0"=>$phrases['no']),"1");
print "</td></tr>                                                                         

<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>";
print "</table></center>";

}


//---------- Edit Member Field -------------
if($action=="movies_fields_edit"){

    if_admin("movies_fields");
$id=intval($id);

$qr = db_query("select * from movies_fields_sets where id='$id'");

if(db_num($qr)){
$data = db_fetch($qr);

print "<img src='images/arrw.gif'>&nbsp; <a href='index.php?action=movies_fields'>$phrases[movies_fields]</a> / $data[name]</a><br><br>";



print "<center><form action=index.php method=post>
<input type=hidden name=action value='movies_fields_edit_ok'>
<input type=hidden name=id value='$id'>
<table width=80% class=grid>";
print "<tr><td><b> $phrases[the_name]</b> </td><td><input type=text size=20  name=name value=\"$data[name]\"></td></tr>
<tr><td><b> $phrases[the_description] </b></b></td><td><input type=text size=30  name=details value=\"$data[details]\"></td></tr>
<tr><td><b>$phrases[the_type]</b></td><td><select name=type>";


print "<option value='text'".iif($data['type']=="text", "selected").">$phrases[textbox]</option>
<option value='textarea'".iif($data['type']=="textarea"," selected").">$phrases[textarea]</option>
<option value='select'".iif($data['type']=="select"," selected").">$phrases[select_menu]</option>
<option value='radio'".iif($data['type']=="radio"," selected").">$phrases[radio_button]</option>
<option value='checkbox'".iif($data['type']=="checkbox"," selected").">$phrases[checkbox]</option>
</select>
</td></tr>
<tr><td><b>$phrases[default_value_or_options]</b><br><br>$phrases[put_every_option_in_sep_line]</td><td>
<textarea name='value' rows=10 cols=30>$data[value]</textarea></td></tr>

<tr><td><b>$phrases[addition_style]</b> </td><td><input type=text size=30  name='style_v' value=\"$data[style]\" dir=ltr></td></tr>


<tr><td><b>$phrases[enable_search]</b></td><td>";
print_select_row("enable_search",array("1"=>$phrases['yes'],"0"=>$phrases['no']),$data['enable_search']);
print "</td></tr>   
                                                                                                       

<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>";
print "</table></center>";
}else{
print "<center><table width=70% class=grid>";
print "<tr><td align=center>$phrases[err_wrong_url]</td></tr>";
print "</table></center>";
}

}
?>
