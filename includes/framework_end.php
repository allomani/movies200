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

 if(!defined("GLOBAL_LOADED")){die("Access Denied");}

 
 //--------------- Load freamwork Plugins --------------------------
  $pls = load_plugins("end_".CFN);
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}
  
  
  
//---------------------  Footer Banners ------------------------------------------------------
//$qr = db_query("select * from movies_banners where type='footer' and active=1 and pages like '%$pg_view,%' order by ord");
if(count($banners['footer']['x'][0])){
 foreach($banners['footer']['x'][0] as $data){
db_query("update movies_banners set views=views+1 where id='$data[id]'");

if($data['c_type']=="code"){
    compile_template($data['content']);
    }else{
        compile_template(get_template("center_banners"));     
}
        }
 print "<br>";
}

//---------------------------Right Content--------------------------------------
print "</td>" ;



  if(count($blocks['r'][0])){
print "<td width='$blocks_width' valign=\"top\" align=\"center\" dir=\"$global_dir\">";

print "<table width=100%>";


             $adv_c= 1 ;
       foreach($blocks['r'][0] as $zdata){
        print "<tr>
                <td  width=\"100%\" valign=\"top\">";
                
          $sub_count = count($blocks['r'][$zdata['id']]);      
          
        
            open_block(iif(!$zdata['hide_title'] && !$sub_count,$zdata['title']),$zdata['template']);    
            
           if($sub_count){
              
               $tabs = new tabs("block_".$zdata['id']);
 
        $tabs->start($zdata['title']);
           run_php($zdata['file']);
          $tabs->end();  
          
          foreach($blocks['r'][$zdata['id']] as $sub_data){    

           $tabs->start($sub_data['title']);
           run_php($sub_data['file']);
          $tabs->end();     
          }
          
           $tabs->run(); 
         
          
           }else{
               
               run_php($zdata['file']);           
                     
           } 
          close_block($zdata['template']);  

                print "</td>
        </tr>";

              //---------------------------------------------------

         if(count($banners['menu']['r'][$adv_c])){
           
       print_block_banners($banners['menu']['r'][$adv_c]);  
        unset($banners['menu']['r'][$adv_c]);
               }
            ++$adv_c ;
        //----------------------------------------------------
           }
           
//------- print remaining blocks banners --------//
if(count($banners['menu']['r'])){
    foreach($banners['menu']['r'] as $data_array){
         print_block_banners($data_array); 
    }
}
   
print "</table></center></td>" ;
unset($zdata,$data,$adv_c); 
}
unset($zqr);
print "</tr></table>\n";


print_copyrights();

compile_hook('site_before_footer'); 
site_footer();
compile_hook('site_after_footer');                         
           
if($debug){ 
print "<br><div dir=ltr><b>Excution Time :</b> " .  (microtime()-$start_time)." Sec";                                                          
print "<br><b>Memory Usage :</b> " .  convert_number_format(memory_get_usage(),2,true,true);
print "<br><div dir=ltr><b>Queries :</b> " .  $queries."<br>"; 
print "</div>";
}

?>
