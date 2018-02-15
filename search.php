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

function gen_keyword_sql($keyword,$field){
      $words = explode(' ',$keyword);
$count_words = count($words); 
if($count_words){
  for($i=0;$i<$count_words;$i++){  
    $words[$i] = trim($words[$i]);
    if($words[$i]){
     $sql .= "$field like '%".db_escape($words[$i])."%' ";
     if($i < ($count_words-1)){
     $sql .= " and ";    
     }
    }
  }    
}

return $sql;
}
//--------------------------


 $keyword = trim($keyword);
 
 compile_hook('search_start') ;
 
               
   if(strlen($keyword) >= $settings['search_min_letters']){
 open_table("$phrases[search_results]" );

 if($op=="movies" || !$op){
        //----------------- start pages system ----------------------
     $start = (int) $start;
     $field_id = (int) $field_id;
     $page_string= "search.php?op=movies&keyword=$keyword&start={start}" ;
      $movies_perpage = $settings['movies_perpage']; 
        //--------------------------------------------------------------
      
        if($field_id){
          $qr=db_query("select movies_data.*,movies_cats.name as cat_name,movies_cats.id as cat_id from movies_data,movies_cats where movies_cats.id=movies_data.cat and field_".$field_id." like '".db_escape($keyword)."' order by movies_data.date DESC  limit $start,$movies_perpage");
        $page_result = db_qr_fetch("SELECT count(*) as count from movies_data where field_".$field_id." like '".db_escape($keyword)."'");
          
        }else{
        $qr=db_query("select movies_data.*,movies_cats.name as cat_name,movies_cats.id as cat_id from movies_data,movies_cats where movies_cats.id=movies_data.cat and ".gen_keyword_sql($keyword,"movies_data.name")." order by movies_data.date DESC  limit $start,$movies_perpage");
        $page_result = db_qr_fetch("SELECT count(*) as count from movies_data where ".gen_keyword_sql($keyword,"name")."");
        }
     /*  $qr=db_query("select movies_data.*,movies_cats.name as cat_name,movies_cats.id as cat_id from movies_data,movies_cats where movies_cats.id=movies_data.cat and ".gen_keyword_sql($keyword,"movies_data.name")." order by movies_data.date DESC");
   
      $page_result['count'] = 50000;  */
      
         if(db_num($qr)){
         print "<table width=100%>";
        while($data = db_fetch($qr)){


if ($c==$settings['movies_cells']) {
print "  </tr><TR>" ;
$c = 0 ;
}
 ++$c ;

 print "<td>";
  $data_cat = db_qr_fetch("select name,id from movies_cats where id='$data[cat]'");
 compile_template(get_template('browse_movies'));
 print "</td>";

        }
        print "</tr></table>";


print_pages_links($start,$page_result['count'],$movies_perpage,$page_string); 

      }else{
                print "<center>  $phrases[no_results] </center>";
                }
//---------------------------------------------------------------------------------
 }
 
 
 
//---------------------News Search --------------------


if($op=="news"){
    //----------------- start pages system ----------------------
        $start = (int) $start; 
       $page_string= "search.php?op=news&keyword=$keyword&start={start}" ;
       $news_perpage = $settings['news_perpage'];   
        //--------------------------------------------------------------

        
       $qr = db_query("select * from movies_news where ".gen_keyword_sql($keyword,"title")." order by id desc limit $start,$news_perpage");
       $page_result = db_qr_fetch("SELECT count(*) as count from movies_news where ".gen_keyword_sql($keyword,"title")."");


    if(db_num($qr)){

       print "<hr class=separate_line size=\"1\">";
    while($data = db_fetch($qr)){

    $data['content'] = str_replace("$keyword","<font color='red'>$keyword</font>",$data['content']);
  run_template('browse_news');   


             }

print_pages_links($start,$page_result['count'],$news_perpage,$page_string); 

            }else{
               print "<center>  $phrases[no_results] </center>";

        }
        
}



      
//-----------------------------------------------------
close_table();
         }else{
         open_table();
         $phrases['type_search_keyword'] = str_replace('{letters}',$settings['search_min_letters'],$phrases['type_search_keyword']);
                 print "<center>  $phrases[type_search_keyword] </center>";
                 close_table();
                 }
                 
compile_hook('search_end') ;

//---------------------------------------------
require(CWD . "/includes/framework_end.php");      
?>
