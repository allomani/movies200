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

 
//---------------------- Members Fields ---------------------
if($action=="members_fields" || $action=="members_fields_edit_ok" || $action=="members_fields_add_ok" || $action=="members_fields_del"){

 if_admin("members");
 
if($action=="members_fields_del"){
$id=intval($id);
db_query("delete from movies_members_sets where id='$id'");
db_query("ALTER TABLE  `".members_table_replace("movies_members")."` DROP  `field_".$id."`",MEMBER_SQL);
}

if($action=="members_fields_edit_ok"){
$id=intval($id);
if($name){
      $value=trim($value); 
db_query("update movies_members_sets set name='".db_escape($name)."',details='".db_escape($details)."',required='$required',type='$type',value='".db_escape($value,false)."',style='$style_v',ord='".intval($ord)."' where id='$id'");
    }
}

if($action=="members_fields_add_ok"){
$id=intval($id);
if($name){
    $value=trim($value);
db_query("insert into movies_members_sets  (name,details,required,type,value,style,ord) values('".db_escape($name)."','".db_escape($details)."','$required','$type','".db_escape($value,false)."','$style_v','$ord')");

$field_id = mysql_insert_id();

db_query("ALTER TABLE  `".members_table_replace("movies_members")."` ADD  `field_".$field_id."` TEXT NOT NULL",MEMBER_SQL);  
}
}


print "<p align=center class=title> $phrases[members_custom_fields]</p>

<p align=$global_align><a href='index.php?action=members_fields_add'><img src='images/add.gif' border=0> $phrases[add_member_custom_field] </a></p>";




$qr= db_query("select * from movies_members_sets order by required desc,ord asc");
if(db_num($qr)){
print "<center><table width=90% class=grid><tr><td>
<div id=\"members_fields_list\">";

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
      
<td><a href='index.php?action=members_fields_edit&id=$data[id]'>$phrases[edit]</a> - <a href='index.php?action=members_fields_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a></td></tr>
</table></div>";

}
print "</div>
</td></table></center>";

 print "<script type=\"text/javascript\">
        init_sortlist('members_fields_list','set_members_fields_sort');
</script>";



}else{
print_admin_table("<center>$phrases[no_members_custom_fields] </center>");
    }


}

//---------- Add Member Field -------------
if($action=="members_fields_add"){
 if_admin("members");
print "<center>
<p align=center class=title>$phrases[add_member_custom_field]</p>
<form action=index.php method=post>
<input type=hidden name=action value='members_fields_add_ok'>
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


<tr><td><b>$phrases[required]</b></td><td><select name=required>";
print "<option value=1>$phrases[yes]</option>
<option value=0>$phrases[no]</option>
</select></td></tr>
                                                                         

<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>";
print "</table></center>";

}


//---------- Edit Member Field -------------
if($action=="members_fields_edit"){

    if_admin("members");
$id=intval($id);

$qr = db_query("select * from movies_members_sets where id='$id'");

if(db_num($qr)){
$data = db_fetch($qr);
print "<center><form action=index.php method=post>
<input type=hidden name=action value='members_fields_edit_ok'>
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


<tr><td><b>$phrases[required]</b></td><td><select name=required>";
if($data['required']){$chk1="selected";$chk2="";}else{$chk1="";$chk2="selected";}
print "<option value=1 $chk1>$phrases[yes]</option>
<option value=0 $chk2>$phrases[no]</option>
</select></td></tr>
                                                                                                       

<tr><td colspan=2 align=center><input type=submit value=' $phrases[edit] '></td></tr>";
print "</table></center>";
}else{
print "<center><table width=70% class=grid>";
print "<tr><td align=center>$phrases[err_wrong_url]</td></tr>";
print "</table></center>";
}

}
?>
