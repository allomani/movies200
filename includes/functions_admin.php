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

   //----------------- Admin Path Links ---------
 function print_admin_path_links($cat,$filename=""){
     global $phrases,$global_align;
     
     $dir_data['cat'] = intval($cat) ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select name,id,cat from movies_cats where id='$dir_data[cat]'");


        $dir_content = "<a href='index.php?action=movies&cat=$dir_data[id]'>$dir_data[name]</a> / ". $dir_content  ;

        }
   print "<p align=$global_align><img src='images/link.gif'> <a href='index.php?action=movies&cat=0'>$phrases[the_movies]  </a> / $dir_content " . "$filename</p>";

 }
 


 //---------------------------- Delete Movie Function---------------------------------
function delete_movie($id){

$qr = db_query("select img,thumb from movies_data where id='$id'");
if(db_num($qr)){
        $data = db_fetch($qr);

 delete_file($data['img']);
 delete_file($data['thumb']);

   db_query("delete from movies_data where id='$id'");
   db_query("delete from movies_files where cat='$id'");

   //---- delete subtitles --- //
    $qr = db_query("select id from movies_subtitles where cat='$id'");
    while($data = db_fetch($qr)){
     delete_subtitle($data['id']);
            }
   // ----------------------- //
   delete_movie_photos($id);
    }
        }
//------------------------- Delete Subtitle ---------------
 function delete_subtitle($id){
 
$qr = db_query("select url from movies_subtitles where id='$id'");
if(db_num($qr)){
        $data = db_fetch($qr);

 delete_file($data['url']);
 db_query("delete from movies_subtitles where id='$id'");

    }
 }

//------------------------- Delete Photos ---------------
  function delete_movie_photos($id){
 
  $qr = db_query("select thumb,img from movies_photos where cat='$id'");
       while($data = db_fetch($qr)){
        delete_file($data['thumb']);
        delete_file($data['img']);
                }
     db_query("delete from movies_photos where cat='$id'");
   }

         
         //-------------- Get Dir Files List ----------
function get_files($dir,$allowed_types="",$subdirs_search=1) {
      $dir = (substr($dir,-1,1)=="/" ? substr($dir,0,strlen($dir)-1) : $dir);

    if($dh = opendir($dir)) {

        $files = Array();
        $inner_files = Array();

        while($file = readdir($dh)) {
            if($file != "." && $file != ".." && $file[0] != '.') {
                if(is_dir($dir . "/" . $file) && $subdirs_search) {
                    $inner_files = get_files($dir . "/" . $file,$allowed_types);
                    if(is_array($inner_files)) $files = array_merge($files, $inner_files);
                }else{
                  $fileinfo= pathinfo($dir . "/" . $file);
                $imtype = $fileinfo["extension"];
          if(is_array($allowed_types)){
          if(in_array($imtype,$allowed_types)){
               $files[] =  $dir . "/" . $file;
           }
          }else{
               $files[] =  $dir . "/" . $file;
          }
                }
            }
        }

        closedir($dh);
        return $files;
    }
}

        

       
  /*      
//--------------------------- Create Thumb ----------------------------
function create_thumb($filename , $width , $height,$fixed=false,$suffix='',$replace_exists=false,$save_filename=''){
    
require_once(CWD .'/includes/class_thumb.php');

if(function_exists("ImageCreateTrueColor")){
 if(file_exists(CWD . "/$filename")){ 
 $img_info = @getimagesize(CWD . "/$filename"); 
 $thumb=new thumbnail(CWD . "/$filename");

 if($fixed){
 $thumb->size_fixed($width,$height);
 }else{    
 if($img_info[0] < $width){
 $width = $img_info[0];
 }
 if($img_info[1] < $height){
$height = $img_info[1];
  }
  
 if($height > $width){
  $thumb->size_height($height); 
 }else{
 $thumb->size_width($width);
 } 
 }        
           

   $imtype = file_extension(CWD . "/$filename");


$thumb->jpeg_quality(100); 

if($save_filename){
 $save_name  =  $save_filename ;
   
}else{
$save_name  =  basename($filename);
$save_path = str_replace("/".$save_name,'',$filename);
                                                              
$imtype = file_extension($save_name);
$save_name = convert2en($save_name);
$save_name = strtolower($save_name);
$save_name= str_replace(" ","_",$save_name);


if($suffix){
$save_name = str_replace(".$imtype","",$save_name)."_".$suffix.".$imtype";
}


    
while(file_exists(CWD . "/" .$save_path."/".$save_name)){
$save_name = str_replace(".$imtype","",$save_name)."_".rand(0,999).".$imtype";    
}
 }
    
    
$thumb->save(CWD . "/" .$save_path."/".$save_name);           
return ($save_path."/".$save_name) ;
 }else{
     return false;
 }
 }else{
return $false;     
 }
        }
           */
           
           
//------------- if  Cat Admin ---------
function if_cat_admin($cat,$skip_zero_id=true){
 global $user_info,$phrases ;

 if($user_info['groupid'] != 1){
  

  if($cat){
          $cat_users =get_cat_users($cat,true);
              
  
         if(!in_array($user_info['id'],$cat_users)){
              print_admin_table("<center>$phrases[err_cat_access_denied]</center>");
         die();
    }
    }else{
        if(!$skip_zero_id){
          print_admin_table("<center>$phrases[err_cat_access_denied]</center>");
         die();
        }
    }
      }
}

//------------- if  Movie Admin ---------
function if_movie_admin($id,$skip_zero_id=true){
    $id = (int) $id;
    $data = db_qr_fetch("select cat from movies_data where id='$id'");
    if_cat_admin($data['cat'],$skip_zero_id);
}


//-------- Get Cat users  -------
function get_cat_users($id,$fields_only=false,$type=''){

  
   
         $fields_array = array();
         $dir_data['cat'] = intval($id) ;
while($dir_data['cat']!=0){
   $dir_data = db_qr_fetch("select id,cat from movies_cats where id='$dir_data[cat]'");


   
    $data = db_qr_fetch("select `users` from movies_cats where id='".$dir_data['id']."'");
    if(trim($data['users'])){
       $cat_fields = explode(",",$data['users']);
    
    for($z=0;$z<count($cat_fields);$z++){  
    if($fields_only){ 
    if(!in_array($cat_fields[$z],$fields_array)){$fields_array[]=$cat_fields[$z];}
    }else{
    $fields_array[$cat_fields[$z]]=$dir_data['id'];  
    }
    }      
    } 

        }
        
     
    

          return  $fields_array ;
}

//------------ Access Log ------------
   function access_log_record($username,$status){
       global $access_log_expire ;
        
       $expire_date  = datetime("",time()-(24*60*60*$access_log_expire));
       db_query("delete from movies_access_log where date < '$expire_date'");
       db_query("insert into movies_access_log (username,date,status,ip) values ('".db_escape($username)."','".datetime()."','$status','".db_escape(getenv("REMOTE_ADDR"))."')");
   } 
   
   //--------- print admin table -------------
function print_admin_table($content,$width="50%",$align="center"){
    print "<center><table class=grid width='$width'><tr><td align='$align'>$content</td></tr></table></center>";
    }
   
   
   //----------- members custom fields ----------

function get_movie_field($name,$data,$value=""){
      global $phrases;

    $cntx = "" ;

//----------- text ---------------
if($data['type']=="text"){

$cntx .= "<input type=text name=\"$name\" value=\"".iif($search,"",iif($value,$value,$data['value']))."\" $data[style]>";  

//---------- text area -------------
}elseif($data['type']=="textarea"){

$cntx .= "<textarea name=\"$name\" $data[style]>".iif($search,"",iif($value,$value,$data['value']))."</textarea>"; 

//-------- select -----------------
}elseif($data['type']=="select"){
  
     $cntx .= "<select name=\"$name\" $data[style]>";
 $cntx .= "<option value=\"\">$phrases[without_selection]</option>";

        $vx  = explode("\n",$data['value']);
        foreach($vx as $value_f){
                          $value_f =trim($value_f);
        $cntx .= "<option value=\"$value_f\"".iif($value==$value_f," selected").">$value_f</option>";
            }
        $cntx .= "</select>";

//--------- radio ------------
}elseif($data['type']=="radio"){

        $cntx .= "<input type=\"radio\" name=\"$name\" value=\"\" $data[style] checked>$phrases[without_selection]<br>";

     
        $vx  = explode("\n",$data['value']);
        foreach($vx as $value_f){
        $cntx .= "<input type=\"radio\" name=\"$name\" value=\"$value_f\" $data[style] ".iif($value==$value_f," checked")."> $value_f<br>";
            }

//-------- checkbox -------------
}elseif($data['type']=="checkbox"){


        $vx  = explode("\n",$data['value']);
        foreach($vx as $value_f){
       
        $cntx .= "<input type=\"checkbox\" name=\"$name\" value=\"$value_f\" ".iif($value==$value_f,"checked").">$value_f<br>";
            }
        }
return $cntx;
}


//---------------- get timezones --------------------
function get_timezones(){
require_once(CWD . '/includes/class_xml.php');     
  $xmlobj = new XMLparser(false, CWD . "/xml/time_zones.xml");
  $xml = $xmlobj->parse();
  return (array) $xml['zone'];
  
}

//--------------------------------- Check Functions ---------------------------------
function check_safe_functions($condition_value){

  global $phrases ;
                            
                         
  //------ get safe functions ----------
  require_once(CWD . '/includes/class_xml.php');     
  $xmlobj = new XMLparser(false, CWD . "/xml/safe_functions.xml");
  $xml = $xmlobj->parse();
$safe_functions =  (array) $xml['func'];
if(!count($safe_functions)){ return "Error : Please check safe functions XML File";}
//------------------------------------------


      if (preg_match_all('#([a-z0-9_{}$>-]+)(\s|/\*.*\*/|(\#|//)[^\r\n]*(\r|\n))*\(#si', $condition_value, $matches))
                        {

                                $functions = array();
                                foreach($matches[1] AS $key => $match)
                                {
                                        if (!in_array(strtolower($match), $safe_functions) && function_exists(strtolower($match)))
                                        {
                                                $funcpos = strpos($condition_value, $matches[0]["$key"]);
                                                $functions[] = array(
                                                        'func' => stripslashes($match),
                                                    //    'usage' => substr($condition_value, $funcpos, (strpos($condition_value, ')', $funcpos) - $funcpos + 1)),
                                                );
                                        }
                                }
                                if (!empty($functions))
                                {
                                        unset($safe_functions[0], $safe_functions[1], $safe_functions[2]);



                                        foreach($functions AS $error)
                                        {
                                                $errormsg .= "$phrases[err_function_usage_denied]: <code>" . htmlspecialchars($error['func']) . "</code>
                                                <br>\n";
                                        }

                                        return "$errormsg";
                                     //   return false ;
                                }else{
                                      //   return true ;
                                      return false;
                                          }
                        }
                     //   return true ;
                     return false;
                        }
