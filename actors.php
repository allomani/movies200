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

     //----------------- start pages system ----------------------
 $start=(int) $start;
   $page_string= $links['actors_w_pages'];
   $actors_perpage = intval($settings['actors_per_page']);
   //----------------------------------------------------------
   
   
$qr = db_query("select * from movies_actors order by name asc limit $start,$actors_perpage");
if(db_num($qr)){
    

   //-------------------
   $page_result = db_qr_fetch("select count(*) as count from movies_actors");  
   //--------------------------------------------------------------
   
   
   
   open_table("$phrases[the_actors]");  
   
   if($settings['actors_show_in_groups']){
  
//-------- use groups -------------- 
unset($data_arr,$data_arr2);   
while($data = db_fetch($qr)){
 $lt_found = false ;
 
 

for($cx=0;$cx < count($letters_groups) ;++$cx){   
if(in_array(utf8_substr(strtoupper($data['name']),0,1),$letters_groups[$cx])){
$data_arr[$cx][] = $data ;
        $lt_found = true ;
        break;
  }
}
if(!$lt_found){
 $data_arr2[] = $data ;  
  }

   }
unset($data);
//-----------------------------

 
  for($cy = 0;$cy < count($letters_groups) ;++$cy){

       $data_arr_main = $data_arr[$cy];
      if(count($data_arr_main)){
  
          
        print "<span align=right class=title>".$letters_groups_names[$cy]."</span><hr class=separate_line 1px\" size=\"1\">";

        print "<table width=100%><tr>" ;

   $c = 0 ;
   
   foreach($data_arr_main as $data){  
       
  if($c==$settings['movies_cells']){print "</tr><tr>";$c=0;}
    
       print "<td align=center>";
        run_template('browse_actors');
       print " </td>";
       
        $c++;




             }

   print "</tr></table>";

     }
    }
    
    unset($data);
 
   //---------------------- others array --------------------
    if(count($data_arr2)){
     print "<span align=right class=title>$phrases[other]</span><hr class=separate_line size=\"1\">";

       print "<table width=100%><tr>" ;

   $c = 0 ;

  foreach($data_arr2 as $data){  
      
if($c==$settings['movies_cells']){print "</tr><tr>";$c=0;}
    
        print "<td align=center>";
        run_template('browse_actors');
       print " </td>";
        
        $c++;

             }

  print  "</tr></table>";
   }
   
   unset($data);
   
   }else{
       //--------- without groups --------------------
        print "<table width=\"100%\"><tr>";
    $c = 0;
     while($data =db_fetch($qr)){  
      
if($c==$settings['movies_cells']){print "</tr><tr>";$c=0;}
    
        print "<td align=center>";
        run_template('browse_actors');
       print " </td>";
        
        $c++;

             }

  print  "</tr></table>";
   }
   
   //----------------------------------
   close_table(); 
 
 //-------------------- pages system ------------------------
print_pages_links($start,$page_result['count'],$actors_perpage,$page_string);
//------------ end pages system -------------  
  
}else{
    open_table();
    print "<center> $phrases[no_actors] </center>";
     close_table(); 
}


//---------------------------------------------
require(CWD . "/includes/framework_end.php"); 
