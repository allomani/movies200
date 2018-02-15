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

chdir('./../');
define('CWD', (($getcwd = getcwd()) ? $getcwd : '.'));

require(CWD . "/global.php") ;

if (check_admin_login()) {


echo "<html dir=$global_dir>\n";
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
<title>$phrases[actors_list]</title>
<LINK href='images/style.css' type=text/css rel=StyleSheet>";

  $qr = db_query("select name,id,thumb from movies_actors order by  name asc");
 if(db_num($qr)){
         print "<table width=100% class=grid><tr>
<td><b>$phrases[actor_name] </b></td><td><b>$phrases[the_id]</b></td>
";

 
while($data = db_fetch($qr)){

if($tr_class=="row_2"){
$tr_class="row_1" ;
}else{
$tr_class="row_2";
}

         print "<tr class='$tr_class'><td><a href='#' onclick=\"opener.document.forms['add_actor_by_id_form'].elements['actor_id'].value ='$data[id]';window.close();\">$data[name]</a></td><td>$data[id]</td></tr>";
          }
          print "</table>";
          }else{
          print "<center>  $phrases[no_actors] </center>";
                  }
 }else{
          print "<SCRIPT>window.location=\"index.php\";</script>";
          }

          ?>
