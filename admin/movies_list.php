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

if (!check_admin_login()) {die("Access Deneid");}


echo "<html dir=$global_dir>\n";
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
<title>$phrases[movies_list]</title>
<LINK href='images/style.css' type=text/css rel=StyleSheet>";

  $qr = db_query("select movies_data.id as id ,movies_data.name as name,movies_cats.name as cat from movies_data,movies_cats where movies_data.cat=movies_cats.id order by binary movies_cats.name , binary movies_data.name asc");
 if(db_num($qr)){
         print "<table width=100% class=grid><tr>
<td><b> $phrases[the_cat] </b></td><td><b>$phrases[the_movie] </b></td><td><b>$phrases[the_id]</b></td>
";

 $tr_ord=1;
  while($data = db_fetch($qr)){

   if($tr_class == "row_2"){
                   $tr_class="row_1" ;
                 
                   }else{
                    $tr_class="row_2";
                  
                           }

         print "<tr class='$tr_class'><td>$data[cat]</td><td><a href='#' onclick=\"opener.document.forms['add_movie_by_id_form'].elements['movie_id'].value ='$data[id]';window.close();\">$data[name]</a></td><td>$data[id]</td></tr>";
          }
          print "</table>";
          }else{
          print "<center>  $phrases[no_movies] </center>";
                  }
 

          ?>
