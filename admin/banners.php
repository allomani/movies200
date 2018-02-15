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
  
//----------------  Banners -------------------------------------
   if($action == "banners" || $action =="adv2" || $action =="adv2_edit_ok" || $action =="adv2_del" || $action =="adv2_add_ok" || $action=="banner_disable" || $action=="banner_enable"){

   if_admin("adv");

//----------- add ----------------
if($action =="adv2_add_ok"){
    if($pages){
foreach ($pages as $value) {
       $pg_view .=  "$value," ;
     }
       }else{
               $pg_view = '' ;
               }

      db_query("insert into movies_banners (title,url,img,ord,type,date,menu_id,menu_pos,pages,content,c_type,active) values ('".db_escape($title)."','".db_escape($url)."','".db_escape($img)."','".intval($ord)."','".db_escape($type)."','".time()."','".intval($menu_id)."','".db_escape($menu_pos)."','".db_escape($pg_view)."','".db_escape($content,false)."','".db_escape($c_type)."','1')");

          }

//---------- edit --------------
if($action =="adv2_edit_ok"){

 if($pages){
foreach ($pages as $value) {
       $pg_view .=  "$value," ;
     }
       }else{
               $pg_view = '' ;
               }
      db_query("update movies_banners set title='".db_escape($title)."',url='".db_escape($url)."',img='".db_escape($img)."',ord='".intval($ord)."',type='".db_escape($type)."',menu_id='".intval($menu_id)."',menu_pos='".db_escape($menu_pos)."',pages='".db_escape($pg_view)."',content='".db_escape($content,false)."',c_type='".db_escape($c_type)."' where id='$id'");

          }

//---------- delete -------------
if($action =="adv2_del"){

      db_query("delete from movies_banners where id='$id'");

 }


 if($action=="banner_disable"){
        db_query("update movies_banners set active=0 where id='$id'");
        }

if($action=="banner_enable"){

       db_query("update movies_banners set active=1 where id='$id'");
        }
//-------------------------------------

print "<p align=center class=title>$phrases[the_banners]</p>";
  print "<img src='images/add.gif'>&nbsp; <a href='index.php?action=banners_add'>$phrases[add_button]</a><br><br>";
     
//------------Bannners List -----------//

 $qr= db_query("select * from movies_banners order by type , ord");
 
 if(db_num($qr)){
     

    print "
  <center>
  <table width=99% class=grid>

 
  
  <tr><td>
  ";
  $i=0;
  while($data=db_fetch($qr)){

          if($last_banner_type != $data['type']){
          if($i > 0){print "</div><hr class=separate_line>";}
          $types_array[] =  $data['type'] ;
        print "<div id='banners_list_".$data['type']."'>";
        $i++;
       } 
                          
       $last_banner_type = $data['type'];
       
       
  print "<div id=\"item_$data[id]\" style=\"".iif(!$data['active'],"background-color:#FFEAEA;")."\" 
     onmouseover=\"this.style.backgroundColor='#EFEFEE'\"
     onmouseout=\"this.style.backgroundColor='".iif($data['active'],"#FFFFFF","#FFEAEA")."'\">
  <table width=100%><tr>
   <td  align=$global_align width=25>
      <span style=\"cursor: move;\" class=\"handle\"><img alt='$phrases[click_and_drag_to_change_order]' src='images/move.gif'></span> 
      </td>
      ";
  if($data['c_type']=="code"){
      print "<td width=25><img src='images/code_icon.gif' alt='$phrases[bnr_ctype_code]'></td>";
      }else{
          print "<td width=25><img src='images/image_icon.gif' alt='$phrases[bnr_ctype_img]'></td>";
          }
          
  print "<td>$data[title]</td>";
                  
         $types_values = array_flip($banners_places);
         
   print "<td width=60>".$types_values[$data['type']]."</td>
   <td width=60> ";
   if($data['type'] == "menu"){
   print iif($data['menu_pos']=="r","$phrases[right]",iif($data['menu_pos']=="l","$phrases[left]",iif($data['menu_pos']=="c","$phrases[center]")));
   }else{
           print "-" ;
           }
           print "</td>
     <td width=100>$data[views] $phrases[bnr_views]</td>
       <td width=100>$data[clicks] $phrases[bnr_visits] </td>
    <td width=200>";
     if($data['active']){
                        print "<a href='index.php?action=banner_disable&id=$data[id]'>$phrases[disable]</a> - " ;
                        }else{
                        print "<a href='index.php?action=banner_enable&id=$data[id]'>$phrases[enable]</a> - " ;
                        }
                        
    print "<a href='index.php?action=adv2_edit&id=$data[id]'>$phrases[edit]</a> - 
    <a href='index.php?action=adv2_del&id=$data[id]' onClick=\"return confirm('$phrases[are_you_sure]');\">$phrases[delete] </a></td>
  </tr>
  </table></div>" ;

      }
       print "</div>
       </td></tr></table></center>\n";
    

if(is_array($types_array)){
print "<script type=\"text/javascript\">"; 
foreach($types_array as $value){
print "Sortable.create
(
    'banners_list_".$value."',{
tag:'div',
handle:'handle',
        constraint: false,
        onUpdate: function()
        {
      new Ajax.Updater
            (
                'result', 'ajax.php',
                { postBody: Sortable.serialize('banners_list_".$value."',{name:'sort_list'}) +'&action=set_banners_sort'}
            );
        }
    }
);";
    
}
print "</script>";
}
 }else{
    print_admin_table("<center>$phrases[no_banners]</center>");
 } 
}

//------------------ banners add ---------
if($action=="banners_add"){
    if_admin("adv");
    
                  print "
                  <img src='images/arrw.gif'>&nbsp; <a href='index.php?action=banners'>$phrases[the_banners]</a> / $phrases[add_button] <br><br> 
                
                
                <form method=\"POST\" action=\"index.php\" name='sender'>
                 <input type='hidden' value='adv2_add_ok' name='action'>
                 
                    
                   <center>
<table width=\"80%\"><tr><td>

                   
<table  width=\"100%\" class=grid>
  <tr>
                <td>$phrases[bnr_appearance_places]</td>
                <td><select id=\"type\" name=\"type\" size=\"1\" onChange=\"show_adv_options();\">
             ";
             foreach($banners_places as $key => $value){
                 print "<option value=\"$value\">$key</option>";
             }
                print "
                </select></td>

                </tr>

                
                  
              <tr>
                   <td>$phrases[the_name]<td>
                <input type=\"text\" name=\"title\" size=\"38\"></td>
        </tr>
        
</table>


<table  width=\"100%\" class=grid id='bnr_content_type'>
           <tr>
                   <td >
                   $phrases[the_content_type]    <td >
                   <select id=\"c_type\" name=\"c_type\" onChange=\"show_adv_options();\">
                   <option value='img'> $phrases[bnr_ctype_img] </option>
                   <option value='code'>$phrases[bnr_ctype_code]</option>
                   </select>
              
                </td>
        </tr>
</table>

<table  width=\"100%\" class=grid id='banners_url_area'>

         <tr>
                <td>$phrases[the_url]</td>
                <td>
                <input type=\"text\" name=\"url\"  dir=ltr value='http://' size=\"38\"></td>
        </tr>
</table>

<table  width=\"100%\" class=grid id='banners_img_area'>
        <tr>
                <td >$phrases[the_image]</td>
                <td >

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr value=\"$data[img]\">   </td>

                                <td> <a href=\"javascript:uploader('banners','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

                                 </td>
        </tr>
</table>

<table width=\"100%\" class=grid id='banners_code_area' style=\"display: none; text-decoration: none\">
<tr> <td>$phrases[the_content] </td>
<td>
<textarea dir=ltr rows=\"8\" name=\"content\" cols=\"50\"></textarea>
</td></tr>
</table>


<table width=\"100%\" class=grid id='add_after_menu' style=\"display: none; text-decoration: none\">
  <tr> <td> $phrases[add_after_menu_number] :</td>
                <td>
              
                <input type=\"text\"  name=\"menu_id\" value=0 size=\"4\">&nbsp;  $phrases[bnr_menu_pos]&nbsp;
                <select name=\"menu_pos\" size=\"1\">

                <option value=\"r\" >$phrases[the_right]</option>
                <option value=\"c\" >$phrases[the_center]</option>
                 <option value=\"l\" >$phrases[the_left]</option>

                </select>  </td>

                </tr>
</table>


 <table width=\"100%\" class=grid>
                <tr>
                <td>$phrases[the_order]</td>
                <td ><input type=\"text\" name=\"ord\" value='0' size=\"4\"></td>
                </tr>
 </table>
 
 <table width=\"100%\" class=grid id='banners_pages_area'>
                <tr><td>$phrases[bnr_appearance_pages]</td><td>

<table width=100%><tr><td>";


  if(is_array($actions_checks)){


  $c=0;
 for($i=0; $i < count($actions_checks);$i++) {

        $keyvalue = current($actions_checks);

if($c==3){
    print "</td><td>" ;
    $c=0;
    }

print "<input  name=\"pages[$i]\" type=\"checkbox\" value=\"$keyvalue\" checked>".key($actions_checks)."<br>";


$c++ ;

 next($actions_checks);
}
}
       print"</tr></table>
       </td></tr>
</table>
<table width=\"100%\" class=grid>
        <tr>
                <td  align=center>

                <input type=\"submit\" value=\"$phrases[add_button]\"></td>
        </tr>
</table>


</td></tr></table></center>
        </form>
        <script>
        show_adv_options();
        </script>";
}
   //-------------------EDIT BANNER-----------------------------
   if ($action == "adv2_edit"){
    if_admin("adv");


        $data=db_qr_fetch("select * from movies_banners where id='$id'");
        print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=banners'>$phrases[the_banners]</a> / $data[title] <br><br> 
                   
                   <form name=sender method=\"POST\" action=\"index.php\">
                 <input type='hidden' value='adv2_edit_ok' name='action'>
                  <input type='hidden' value='$id' name='id'>
                  
                  
          <center>
          
        <table width=\"80%\"><tr><td>
        
        
          
          <table width=\"100%\" class=grid>
      

                  <tr>
                <td>$phrases[bnr_appearance_places]</td><td>
                 <select id=\"type\" name=\"type\" size=\"1\" onChange=\"show_adv_options();\">";
             foreach($banners_places as $key => $value){
                 print "<option value=\"$value\"".iif($data['type']==$value," selected").">$key</option>";
             }
                print "
                </select></td>

                </tr>

                  <tr>   
                         <td>
                       $phrases[the_name]</td><td>
                <input type=\"text\" name=\"title\" value='$data[title]' size=\"38\"></td>
        </tr>
        </table>
        
        
        <table width=\"100%\" class=grid id='bnr_content_type'>";

      
         print " <tr>
                   <td>
                   $phrases[the_content_type]</td>
                  <td>
                   <select id=\"c_type\" name=\"c_type\" onChange=\"show_adv_options();\">
                   <option value='img'".iif($data['c_type'] == "img"," selected")."> $phrases[bnr_ctype_img] </option>
                   <option value='code'".iif($data['c_type'] == "code"," selected").">$phrases[bnr_ctype_code]</option>
                   </select>
              
                </td>
        </tr>
        </table>
        
        <table width=\"100%\" class=grid id='banners_url_area'".iif($data['c_type']=="code","style=\"display: none; text-decoration: none\"").">
       <tr><td >$phrases[the_url]</td>
                <td>
                <input type=\"text\" name=\"url\"  dir=ltr value='$data[url]' size=\"38\"></td>
        </tr>
        </table>
        
      <table width=\"100%\" class=grid id='banners_img_area'".iif($data['c_type']=="code","style=\"display: none; text-decoration: none\"").">
    <tr><td>$phrases[the_image]</td><td>

                <table><tr><td>
                                 <input type=\"text\" name=\"img\" size=\"30\" dir=ltr value=\"$data[img]\">   </td>

                                <td> <a href=\"javascript:uploader('banners','img');\"><img src='images/file_up.gif' border=0 alt='$phrases[upload_file]'></a>
                                 </td></tr></table>

         </td>
        </tr>
        </table>
        
  <table width=\"100%\" class=grid id='banners_code_area'>
<tr>
 <td valign=top>$phrases[the_code] </td><td> 
<textarea dir=ltr rows=\"8\" name=\"content\" cols=\"50\">$data[content]</textarea>
</td></tr>
</table>


<table width=\"100%\" class=grid id='add_after_menu'".iif($data['type']!="menu"," style=\"display: none; text-decoration: none\"",'').">
    
        <tr>
                <td> $phrases[add_after_menu_number]</td>
                <td>
                <input type=\"text\" value='$data[menu_id]' name=\"menu_id\" value='0' size=\"4\">  $phrases[bnr_menu_pos]
                <select name=\"menu_pos\" size=\"1\">
           
                <option value=\"r\"".iif($data['menu_pos']=="r"," selected").">$phrases[right]</option>
                <option value=\"c\"".iif($data['menu_pos']=="c"," selected").">$phrases[center]</option>
                 <option value=\"l\"".iif($data['menu_pos']=="l"," selected").">$phrases[left]</option>

                </select></td>

                </tr>
</table>
<table width=\"100%\" class=grid>

                <tr>
                <td>$phrases[the_order]</td>
                <td><input type=\"text\" value='$data[ord]' name=\"ord\" value='0' size=\"4\"></td>
                </tr>
 </table>
 <table width=\"100%\" class=grid id='banners_pages_area'>               
                
                <tr>
                <td>  $phrases[bnr_appearance_pages]</td>
                
                
                <td><table width=100%><tr><td>";

                         $pages_view = explode(",",$data['pages']);


  if(is_array($actions_checks)){

  $c=0;
 for($i=0; $i < count($actions_checks);$i++) {

        $keyvalue = current($actions_checks);

if($c==3){
    print "</td><td>" ;
    $c=0;
    }

if(in_array($keyvalue,$pages_view)){$chk = "checked" ;}else{$chk = "" ;}

print "<input  name=\"pages[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>".key($actions_checks)."<br>";


$c++ ;

 next($actions_checks);
}
}



                          print "</tr></table>
                          </td><tr>
</table>
<table width=\"100%\" class=grid >
                          
        <tr>
                <td  align=center>
              
                <input type=\"submit\" value=\"$phrases[edit]\"></td>
        </tr>
</table>


</td></tr></table></center>
        </form>\n
        <script>
        show_adv_options();
        </script>";

           }
