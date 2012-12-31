<?
if(!defined('IS_ADMIN')){die('No Access');}  

//--------------------- Templates ----------------------------------

  if($action =="templates" || $action =="template_edit_ok" || $action=="template_del" ||
  $action =="template_add_ok" || $action=="template_cat_edit_ok" || $action=="template_cat_add_ok" ||
  $action=="template_cat_del" || $action=="templates_set_default"){

 if_admin("templates");

 $id=intval($id);
 $cat =intval($cat);

 //-----  set default style ----
 if($action=="templates_set_default"){
     db_query("update movies_settings set value='$id' where name like 'default_styleid'");
     load_settings(); 
 }
 //------- template cat edit ---------
 if($action=="template_cat_edit_ok"){
 if(trim($name)){
 db_query("update movies_templates_cats set name='".db_escape($name)."',selectable='".intval($selectable)."',images='".db_escape($images)."' where id='$id'");
     }
 }
//------ template cat add ----------
if($action=="template_cat_add_ok"){
db_query("insert into movies_templates_cats (name,selectable,images) values('".db_escape($name)."','".intval($selectable)."','".db_escape($images)."')");
$catid = mysql_insert_id();

$qr = db_query("select * from movies_templates where cat='1' order by id");
while($data = db_fetch($qr)){
db_query("insert into movies_templates (name,title,content,cat,protected) values (
'".db_escape($data['name'])."',
'".db_escape($data['title'])."',
'".db_escape($data['content'],false)."',
'$catid','".intval($data['protected'])."')");
    }

}
//--------- template cat del --------
if($action=="template_cat_del"){
if($id !="1"){
db_query("delete from movies_templates where cat='$id'");
db_query("delete from movies_templates_cats where id='$id'");
     }
    }
//-------- template edit -----------
if($action =="template_edit_ok"){
$non_safe_content =  check_safe_functions($content);
if(!$non_safe_content){
db_query("update movies_templates set title='".db_escape($title)."',content='".db_escape($content,false)."' where id='$id'");
}else{
    print_admin_table("<center> $non_safe_content </center>");
}
}
//--------- template add ------------
if($action =="template_add_ok"){
$non_safe_content =  check_safe_functions($content);
if(!$non_safe_content){
    
db_query("insert into  movies_templates (name,title,content,cat) values(
'".db_escape($name)."',
'".db_escape($title)."',
'".db_escape($content,false)."',
'".intval($cat)."')");

}else{
    print_admin_table("<center> $non_safe_content </center>");
}
}
//---------- template del ---------
if($action=="template_del"){
      db_query("delete from movies_templates where id='$id' and protected=0");
      db_query("update movies_blocks set template=0 where template='$id'");
}

print "<center>
  <p class=title>  $phrases[the_templates] </p> ";


  if($cat){

$cat_data = db_qr_fetch("select name from movies_templates_cats where id='$cat'");
print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=templates'>$phrases[the_templates] </a> / $cat_data[name]</p>";


         $qr = db_query("select * from movies_templates where cat='$cat' order by views desc,id");
        if (db_num($qr)){
      print "<p align='$global_align'><img src='images/add.gif'> <a href='index.php?action=template_add&cat=$cat'> $phrases[cp_add_new_template] </a></p>
      <br>
      <center>
  <table width=80% class=grid>" ;

   $trx = 1;
    while($data=db_fetch($qr)){
    if($trx == 1){
        $tr_color = "#FFFFFF";
        $trx=2;
        }else{
        $tr_color = "#F2F2F2";
        $trx=1;
        }
    print "<tr bgcolor=$tr_color><td><b>$data[name]</b><br><span class=small>$data[title]</span></td>
   <td align=center> <a href='index.php?action=template_edit&id=$data[id]'> $phrases[edit] </a>";
    if($data['protected']==0){
            print " - <a href='index.php?action=template_del&id=$data[id]&cat=$cat' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>";
            }
            print "</td></tr>";

     }
      print "</table>";

                }else{
                    print_admin_table($phrases['cp_no_templates']);
                     }

}else{
    $qr = db_query("select * from movies_templates_cats order by id asc");
     print "<p align='$global_align'><img src='images/add.gif'> <a href='index.php?action=template_cat_add'> $phrases[add_style] </a></p>
      <br>
    <center><table width=90% class=grid>";
    while($data =db_fetch($qr)){
    print "<tr><td><a href='index.php?action=templates&cat=$data[id]'>$data[name]</a></td>
    <td align=center>".iif($data['id']==$settings['default_styleid'],"[$phrases[default]]","<a href='index.php?action=templates_set_default&id=$data[id]'>$phrases[set_default]</a>")." - 
    <a href='index.php?action=templates&cat=$data[id]'>$phrases[edit_templates]</a> -   
    
     <a href='index.php?action=template_cat_edit&id=$data[id]'> $phrases[style_settings] </a>";
    if($data['id']!=1){
            print " - <a href='index.php?action=template_cat_del&id=$data[id]' onclick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete]</a>";
            }
            print "</td></tr>";
    }
    print "</table></center>";
}



          }
  //--------template cat edit --------
  if($action=="template_cat_edit"){
    if_admin("templates");

      $id= intval($id);
$qr= db_query("select * from movies_templates_cats where id='$id'");
 print  "<p class=title align=center>  $phrases[the_templates] </p> ";
if(db_num($qr)){
$data = db_fetch($qr);
 print "<center>
 <form action=index.php method=post>
 <input type=hidden name=action value='template_cat_edit_ok'>
 <input type=hidden name=id value='$id'>
 <table width=70% class=grid>
 <tr><td><b>$phrases[the_name]</b></td>
 <td>";
 print_text_row("name",$data['name']);
 print "</td></tr>
 <tr><td><b>$phrases[images_folder]</b></td>
 <td>";
 print_text_row("images",$data['images']);
 print "</td></tr>
 <tr><td><b>$phrases[style_selectable]</b></td><td>";
 print_select_row("selectable",array("$phrases[no]","$phrases[yes]"),$data['selectable']);
 print "</td></tr>
 <tr><td align=center colspan=2><input type=submit value=' $phrases[edit] '></td></tr>
 </table>";
}else{
    print_admin_table($phrases['err_wrong_url']);
    }
  }
  //--------template cat add --------
  if($action=="template_cat_add"){
    if_admin("templates");



print  "<p class=title align=center>  $phrases[the_templates] </p> ";

print "<center>
 <form action=index.php method=post>
 <input type=hidden name=action value='template_cat_add_ok'>
 <table width=70% class=grid>
 <tr><td><b>$phrases[the_name]</b></td>
 <td>";
 print_text_row("name");
 print "</td></tr>
  <tr><td><b>$phrases[images_folder]</b></td>
 <td>";
 print_text_row("images");
 print "</td></tr>
 <tr><td><b>$phrases[style_selectable]</b></td><td>";
 print_select_row("selectable",array("$phrases[no]","$phrases[yes]"));
 print "</td></tr>
 <tr><td align=center colspan=2><input type=submit value=' $phrases[add_button] '></td></tr>
 </table>";

  }
 //-------- template edit ------------
          if($action=="template_edit"){
    if_admin("templates");
   $id=intval($id);
$qr = db_query("select * from movies_templates where id='$id'");
      if(db_num($qr)){
      $data = db_fetch($qr);
      
db_query("update movies_templates set views=views+1 where id='$id'");

    $data['content'] = htmlspecialchars($data['content']);
    
     
 $cat_data = db_qr_fetch("select name from movies_templates_cats where id='$data[cat]'");
print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=templates'>$phrases[the_templates] </a> / <a href='index.php?action=templates&cat=$data[cat]'>$cat_data[name]</a> / $data[name]</p>";



print "
  <center>
          <span class=title>$data[name]</span>  <br><br>
  <form method=\"POST\" action=\"index.php\">
  <input type='hidden' name='action' value='template_edit_ok'>
  <input type='hidden' name='id' value='$data[id]'>
   <input type='hidden' name='cat' value='$data[cat]'>

  <table width=80% class=grid><tr>
  <td> <b> $phrases[template_name] : </b></td><td>$data[name]</td></tr>
  <tr>
  <td> <b> $phrases[template_description] : </b></td><td><input type=text size=30 name=title value='$data[title]'></td></tr>
   <tr><td colspan=2 align=center>
        <textarea dir=ltr rows=\"20\" name=\"content\" cols=\"70\">$data[content]</textarea></td></tr>
        <tr><td colspan=2 align=center>
        <input type=\"submit\" value=\" $phrases[edit] \" name=\"B1\"></td></tr>
        </table>
</form></center>\n";
}else{
print_admin_table($phrases['err_wrong_url']);
        }
 }
//------------ template add ------------
  if($action=="template_add"){
if_admin("templates");

   $cat=intval($cat);
 $cat_data = db_qr_fetch("select name from movies_templates_cats where id='$cat'");
print "<p align=$global_align><img src='images/link.gif'><a href='index.php?action=templates'>$phrases[the_templates] </a> / <a href='index.php?action=templates&cat=$cat'>$cat_data[name]</a> / $phrases[add_new_template]</p>";


print "
  <center>
          <span class=title>$phrases[add_new_template] </span>  <br><br>
  <form method=\"POST\" action=\"index.php\">
  <input type='hidden' name='action' value='template_add_ok'>
  <input type='hidden' name='cat' value='".intval($cat)."'>
  <table width=80% class=grid><tr>
  <td> <b> $phrases[template_name] : </b></td><td><input type=text size=30 name=name></td></tr>
  <tr>
  <td> <b> $phrases[template_description] : </b></td><td><input type=text size=30 name=title></td></tr>
   <tr><td colspan=2 align=center>
        <textarea dir=ltr rows=\"20\" name=\"content\" cols=\"70\"></textarea></td></tr>
        <tr><td colspan=2 align=center>
        <input type=\"submit\" value=\"$phrases[add_button]\" name=\"B1\"></td></tr>
        </table>
</form></center>\n";

 }
 ?>