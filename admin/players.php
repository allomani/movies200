<?
  if(!defined('IS_ADMIN')){die('No Access');}   

if($action=="players" || $action=="players_edit_ok" || $action=="players_add_ok" || $action=="players_del"){
 
 if_admin("players");


//-------- del ----------
if($action=="players_del"){
   $id = (int) $id; 
   if($id > 1){
       db_query("delete from movies_players where id='$id'");
   }  
}

//----- edit ---------- 
if($action=="players_edit_ok"){
    $id = (int) $id;
    db_query("update movies_players set name='".db_escape($name)."',int_enabled='".intval($int_enabled)."',ext_enabled='".intval($ext_enabled)."',int_content='".db_escape($int_content,false)."',ext_content='".db_escape($ext_content,false)."',
    int_icon='".db_escape($int_icon)."',int_icon_alt='".db_escape($int_icon_alt)."',ext_icon='".db_escape($ext_icon)."',ext_icon_alt='".db_escape($ext_icon_alt)."',ext_mime='".db_escape($ext_mime)."',ext_filename='".db_escape($ext_filename)."',
    download_enabled='".intval($download_enabled)."',download_icon='".db_escape($download_icon)."',download_icon_alt='".db_escape($download_icon_alt)."',exts='".db_escape($exts)."',view_style='".intval($view_style)."' where id='$id'");
    
}

//----- add ---------
if($action=="players_add_ok"){
    db_query("insert into movies_players (name) values ('".db_escape($name)."')");
    $id = mysql_insert_id();
    $data=db_qr_fetch("select * from movies_players where id='1'");
    db_query("update movies_players set int_enabled='".intval($data['int_enabled'])."',ext_enabled='".intval($data['ext_enabled'])."',int_content='".db_escape($data['int_content'],false)."',ext_content='".db_escape($data['ext_content'],false)."',
    int_icon='".db_escape($data['int_icon'])."',int_icon_alt='".db_escape($data['int_icon_alt'])."',ext_icon='".db_escape($data['ext_icon'])."',ext_icon_alt='".db_escape($data['ext_icon_alt'])."',ext_mime='".db_escape($data['ext_mime'])."',ext_filename='".db_escape($data['ext_filename'])."',
    download_enabled='".intval($data['download_enabled'])."',download_icon='".db_escape($data['download_icon'])."',download_icon_alt='".db_escape($data['download_icon_alt'])."',exts='',view_style='".intval($data['view_style'])."' where id='$id'");
 print "<script>window.location=\"index.php?action=players_edit&id=$id\";</script>";    
}  


print "<p align=center class=title>$phrases[the_players]</p>";

print "<img src='images/add.gif'>&nbsp; <a href='index.php?action=players_add'>$phrases[players_add]</a> <br><br>";



$qr=db_query("select id,name from movies_players");
if(db_num($qr)){
    print "<center><table width=90% class=grid>";
    while($data=db_fetch($qr)){
        print "<tr><td>$data[name]</td><td align='$global_align_x'><a href='index.php?action=players_edit&id=$data[id]'>$phrases[edit]</a>
         ".iif($data['id'] > 1 , "- <a href='index.php?action=players_del&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\">$phrases[delete]</a>")."
        </td></tr>";
    }
    print "</table></center>";
}else{
print_admin_table("<center> $phrases[no_players] </center>");
}

    
}


//----------- players edit ------------------
if($action=="players_edit"){
    
    if_admin("players");
    
    $id = (int) $id;
    $qr=db_query("select * from movies_players where id='$id'");
    if(db_num($qr)){
        $data=db_fetch($qr);
        
        print "<img src='images/arrw.gif'>&nbsp; <a href='index.php?action=players'>$phrases[the_players]</a> / $data[name] <br><br>"; 
        print "<center>
        <form action='index.php' method=post>
        <input type=hidden name='action' value='players_edit_ok'>
        <input type=hidden name='id' value='$id'>
        
          <table width=70% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name='name' value=\"$data[name]\"></td></tr>
         ".iif($id > 1,"<tr><td><b>$phrases[extensions]</b></td><td><input type=text name='exts' dir=ltr value=\"$data[exts]\" style=\"font-family:Arial, Helvetica, sans-serif;font-weight:bold;\">
          <br><font size=1 color='#ACACAC'>$phrases[use_comma_between_types]</font></td></tr>")."
            
        </table><br>
        
        
        <table width=70% class=grid>
        <tr><td colspan=2><h3>$phrases[internal_player]</h3></td></tr>
        <tr><td><b>$phrases[the_status]</b></td><td>";
        print_select_row("int_enabled",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$data['int_enabled']);
        print "</td></tr>
        
        <tr><td><b>$phrases[the_icon_url]</b></td>
         <td> 

                <table cellpadding=\"0\" cellspacing=\"0\"><tr><td>
                                 <input type=\"text\" name=\"int_icon\" size=\"30\" dir=ltr value=\"$data[int_icon]\">   </td>

                                <td> <a href=\"javascript:uploader('icons','int_icon');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
                                 </tr>
       <tr><td><b>$phrases[the_icon_alt]</b></td><td><input type=text name='int_icon_alt' value=\"$data[int_icon_alt]\" size=30></td></tr>  
           
           
        <tr><td><b>$phrases[the_content]</b></td><td>
            <textarea cols=50 rows=15 name='int_content' dir=ltr>".htmlspecialchars($data['int_content'])."</textarea></td></tr>
           
            <tr><td><b>$phrases[player_view_style]</b></td><td>";
        print_select_row("view_style",array("0"=>$phrases['player_view_in_page_ajax'],"1"=>$phrases['player_view_dialog_ajax'],"2"=>$phrases['player_view_ext_page'],"3"=>"Pop-up"),$data['view_style']);
        print "</td></tr>
        
         
            </table><br>
        
        
           <table width=70% class=grid>
        <tr><td colspan=2><h3>$phrases[external_player]</h3></td></tr>
        <tr><td><b>$phrases[the_status]</b></td><td>";
        print_select_row("ext_enabled",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$data['ext_enabled']);
        print "</td></tr>
         <tr><td><b>MIME</b></td><td><input type=text name='ext_mime' value=\"$data[ext_mime]\" size=30 dir=ltr></td></tr>   
       <tr><td><b>$phrases[file_name]</b></td><td><input type=text name='ext_filename' dir=ltr value=\"$data[ext_filename]\" size=30></td></tr>
       <tr><td><b>$phrases[the_icon_url]</b></td>
         <td> 

                <table cellpadding=\"0\" cellspacing=\"0\"><tr><td>
                                 <input type=\"text\" name=\"ext_icon\" size=\"30\" dir=ltr value=\"$data[ext_icon]\">   </td>

                                <td> <a href=\"javascript:uploader('icons','ext_icon');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
                                 </tr>
       <tr><td><b>$phrases[the_icon_alt]</b></td><td><input type=text name='ext_icon_alt' value=\"$data[ext_icon_alt]\" size=30></td></tr>  
           
            <tr><td><b>$phrases[the_content]</b></td><td>
            <textarea cols=50 rows=15 name='ext_content' dir=ltr>".htmlspecialchars($data['ext_content'])."</textarea></td></tr>
            
           
            </table>
            <br>
            
               <table width=70% class=grid>
        <tr><td colspan=2><h3>$phrases[the_download]</h3></td></tr>
        <tr><td><b>$phrases[the_status]</b></td><td>";
        print_select_row("download_enabled",array("0"=>$phrases['disabled'],"1"=>$phrases['enabled']),$data['download_enabled']);
        print "</td></tr>
        <tr><td><b>$phrases[the_icon_url]</b></td>
         <td> 

                <table cellpadding=\"0\" cellspacing=\"0\"><tr><td>
                                 <input type=\"text\" name=\"download_icon\" size=\"30\" dir=ltr value=\"$data[download_icon]\">   </td>

                                <td> <a href=\"javascript:uploader('icons','download_icon');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
                                 </tr>
       <tr><td><b>$phrases[the_icon_alt]</b></td><td><input type=text name='download_icon_alt' value=\"$data[download_icon_alt]\" size=30></td></tr>  
          
           
            </table>
            <br>
            
               <table width=70% class=grid>            
                <tr><td colspan=2 align=center><input type=submit value='$phrases[edit]'></td></tr>     
                 </table>    
            </form>
            </center>";
    }else{
    print_admin_table("<center>$phrases[err_wrong_url]</center>");
    }
}



//----------- players edit ------------------
if($action=="players_add"){
    
    if_admin("players");     

        print "<img src='images/arrw.gif'>&nbsp; <a href='index.php?action=players'>$phrases[the_players]</a> / $phrases[players_add] <br><br>"; 
        print "<center>
        <form action='index.php' method=post>
        <input type=hidden name='action' value='players_add_ok'>
       
        
        <table width=70% class=grid>
        <tr><td><b>$phrases[the_name]</b></td><td><input type=text name='name'></td>
        <td align=center><input type=submit value='$phrases[add]'></td></tr>
            </table></form>
            </center>";
   
}
