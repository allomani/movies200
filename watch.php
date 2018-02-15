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
 
 require("global.php");

set_meta_values(); 
run_template('page_head');

//---------------------------------------------------------

$qrf = db_query("select * from movies_files where id='$id'");

if(db_num($qrf)){
    
    $dataf = db_fetch($qrf);

    $data = db_qr_fetch("select movies_data.name,movies_data.cat,movies_cats.watch_for_members from movies_data,movies_cats where movies_cats.id=movies_data.cat and movies_data.id='$dataf[cat]'");
    
  // print_path_links($data['cat'],$data['name']);    
  
  
//-------------------------
 if($data['watch_for_members']){
 if(check_member_login()){ 
     $continue = 1;
 }else{
     $continue = 0;
       open_table();
     print "<center>$phrases[please_login_first]</center>";
     close_table();
 }
 }else{
     $continue= 1;
 }
 
 //---------------------
 
 if($continue){ 
   $players = get_players_data();
      $player_data = get_file_player(iif($dataf['url_watch'],$dataf['url_watch'],$dataf['url']),$players);    
  
  

    db_query("update movies_files set views=views+1 where id='$id'");
    
         
open_table();
run_php(str_replace("{url}",iif($dataf['url_watch'],$dataf['url_watch'],$dataf['url']),$player_data['int_content']));     
close_table();
 }
 
 
    
}else{
    open_table();
    print "<center>$phrases[err_wrong_url]</center>";
    close_table();
}




//---------------------------------------------

?>
