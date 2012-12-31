<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------

  if(!$cat){ $cat=0;}

   compile_hook('browse_start');  
     
      print_path_links($cat);  
        
    compile_hook('browse_after_path_links');  
    
    
  $qr = db_query("select * from movies_cats where cat='$cat' and active=1 order by ord asc");

    $cats_num = db_num($qr) ;

    if(db_num($qr)){
   
   compile_hook('browse_before_cats');  
        
   templates_cache(array('browse_movies_cats_header','browse_movies_cats_sep','browse_movies_cats','browse_movies_cats_footer'));
   
    
  run_template('browse_movies_cats_header');
  
    $c=0;
        while($data = db_fetch($qr)){



if ($c==$settings['movies_cells']) {
run_template('browse_movies_cats_sep');   
$c = 0 ;
}
    

run_template('browse_movies_cats'); 

$c++ ; 
           }
           run_template('browse_movies_cats_footer'); 
           
    compile_hook('browse_after_cats');         
         }
         
//=============== MOVIES ===========================

//---- order by vars -------//    
if(!$orderby || !$settings['visitors_can_sort_movies'] || !in_array($orderby,$orderby_checks)){$orderby=($settings['movies_default_orderby'] ? $settings['movies_default_orderby'] : "id");}
if(!$sort || !$settings['visitors_can_sort_movies'] || !in_array($sort,array('asc','desc'))){$sort=($settings['movies_default_sort'] ? $settings['movies_default_sort'] : "desc");}

if($orderby == "votes"){
    $orderby_qr = "(votes / votes_total)";
}else{$orderby_qr=$orderby;}
//-------------------------//


    //----------------- pages system ----------------------
    $start = (int) $start;
      $page_string= str_replace(array("{id}","{orderby}","{sort}"),array($cat,$orderby,$sort),$links['links_cats_w_pages']) ;
       $movies_perpage = $settings['movies_perpage'];
  //---------------------------

   $qr = db_query("select * from movies_data where cat='$cat' order by $orderby_qr $sort limit $start,$movies_perpage");
  $movies_count = db_qr_fetch("SELECT count(*) as count from movies_data where cat='$cat'");

    $data_title = db_qr_fetch("select name from movies_cats where id='$cat'");

 
   $movies_num = db_num($qr) ;

    if($movies_num){
        
      compile_hook('browse_before_movies');  
      
    templates_cache(array('browse_movies_header','browse_movies_sep','browse_movies','browse_movies_footer','movies_sort_bar'));
        
    run_template('movies_sort_bar');
    
      compile_hook('browse_after_sort_bar');  
      
      
     open_table($data_title['name']);
     
   if(!$settings['movies_groups'] || $orderby != "name"){  
   
   //---------- no name groups --------------------//  
     if(!$settings['movies_groups'] || $orderby != "year"){
    run_template("browse_movies_header"); 
     }else{
     $last_year = "undefined";   
     } 
    $c=0;
    
        
      
      
        while($data = db_fetch($qr)){

    //---------- open year table -------------------- 
     if($settings['movies_groups'] && $orderby=="year"){           
    if($data['year'] != $last_year){
        
        
        if($last_year !="undefined"){
        run_template("browse_movies_footer"); 
        }
        
        $last_year = $data['year'];
 
    
        print "<span class='title'>".iif($data['year'],$data['year'],$phrases['not_classified'])."</span>
        <hr class='separate_line' SIZE=\"1\">";
        
        run_template("browse_movies_header");   
    
        $c =0;
    }
     }
   //------------------------------------------------
   
   

if ($c==$settings['movies_cells']) {
run_template("browse_movies_sep");  
$c = 0 ;
}
    ++$c ;

run_template("browse_movies");

           }
         run_template("browse_movies_footer"); 
      
   }else{
   //-------------------- order by names groups ---------------------
   
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

//  for(iif($sort=="asc",$cy = 0,$cy = count($letters_groups)-1);iif($sort=="asc",$cy < count($letters_groups),$cy > 0) ;iif($sort=="asc",$cy++,$cy--)){

 //for($cy = count($letters_groups)-1;$cy >= 0;$cy--){
 $cy = iif($sort=="asc",0,count($letters_groups)-1);
 
 
  foreach($letters_groups as $xx){
       $data_arr_main = $data_arr[$cy];
      if(count($data_arr_main)){
  
          
        print "<span align='$global_align' class=title>".$letters_groups_names[$cy]."</span><hr class=separate_line 1px\" size=\"1\">";

      run_template("browse_movies_header");    

   $c = 0 ;
   
   foreach($data_arr_main as $data){  
       
  if ($c==$settings['movies_cells']) {
run_template("browse_movies_sep");  
$c = 0 ;
}
    ++$c ;
    
    
    run_template("browse_movies"); 
    




             }

  run_template("browse_movies_footer");   

     }
     if($sort=="asc"){
     $cy++;
     }else{
         $cy--;
     }
    }
    
    unset($data);
 
   //---------------------- others array --------------------
    if(count($data_arr2)){
     print "<span align='$global_align' class=title>$phrases[other]</span><hr class=separate_line size=\"1\">";

       run_template("browse_movies_header");    

   $c = 0 ;

  foreach($data_arr2 as $data){  
      
if ($c==$settings['movies_cells']) {
run_template("browse_movies_sep");  
$c = 0 ;
}
    ++$c ;
    
    
    
     run_template("browse_movies"); 
        
     

             }

  run_template("browse_movies_footer");   
   }
   
   unset($data);
   
   
   //-----------------------------------------------------------------
   } 
          close_table();

   compile_hook('browse_after_movies');  
            
print_pages_links($start,$movies_count['count'],$movies_perpage,$page_string);

  compile_hook('browse_after_pages');  

            }

if(!$movies_num && !$cats_num){
        open_table();
        compile_hook('browse_before_no_videos');
        print "<center> $phrases[err_no_videos]</center>";
        close_table();
        compile_hook('browse_after_no_videos');
        }
        
        
//---------------------------------------------
require(CWD . "/includes/framework_end.php");  

?>