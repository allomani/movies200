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

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------

$qrm = db_query("select id,cat,name from movies_data where id='$id'");
if(db_num($qrm)){
  
  $data_movie = db_fetch($qrm);
  
       print_path_links($data_movie['cat'],"<a class='path_link' href=\"".str_replace("{id}","$data_movie[id]",$links['links_movie_info'])."\">$data_movie[name]</a>" ." / ". "$phrases[the_actors]");
    

  
 $qr = db_query("select movies_actors.* from movies_actors,movies_actors_index where movies_actors.id = movies_actors_index.actor_id and movies_actors_index.movie_id='$id' order by movies_actors_index.ord");

    open_table("$phrases[the_actors]");     
 if(db_num($qr)){
  
    print "<table width=\"100%\"><tr>";
    $c = 0;
    while($data=db_fetch($qr)){
    
    if($c==$settings['movie_actors_cells']){print "</tr><tr>";$c=0;}
    
        print "<td align=center>
        <a href=\"".str_replace("{id}",$data['id'],$links['links_actor_details'])."\" title=\"$data[name]\">
        <img src=\"".get_image($data['thumb'])."\" title=\"$data[name]\"><br>$data[name]</a></td>";
        
        $c++;
    }
    print "</tr></table>";
    
    
}else{
 print "<center>$phrases[no_actors]</center>";   
}

close_table();  
}else{
    open_table();
    print "<center>$phrases[err_wrong_url]</center>";
    close_table();
}


 
//---------------------------------------------
require(CWD . "/includes/framework_end.php"); 
