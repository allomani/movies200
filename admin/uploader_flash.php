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
require(CWD . "/includes/functions_admin.php") ; 


echo "<html dir=$global_dir>\n";
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">";
?>
<? print "<title>$phrases[uploader_title]</title>\n";?>
<link href="images/style.css" type=text/css rel=stylesheet>
<link href="images/uploadify.css" rel="stylesheet" type="text/css" />   

<script src='js.js' type="text/javascript" language="javascript"></script>
<?
print "<script type=\"text/javascript\" src=\"$scripturl/js/jquery.js\"></script>
<script type=\"text/javascript\" src=\"$scripturl/js/swfobject.js\"></script>
<script type=\"text/javascript\" src=\"$scripturl/js/jquery.uploadify.js\"></script>";
?>

<br>
<?
if (check_admin_login()) {
if($settings['uploader']){


$folder = htmlspecialchars($folder);
$f_name = htmlspecialchars($f_name);
$frm = htmlspecialchars($frm);
$filename = htmlspecialchars($filename); 


//------- upload -----------
if($action=="upload"){ 
if($url && $folder && $f_name){
   $upload_folder = $settings['uploader_path']."/$folder" ;


     if(!$upload_folder || !file_exists(CWD ."/$upload_folder")){
     print_admin_table("<center>$phrases[err_wrong_uploader_folder]</center>");
     die();
      }
      

    require_once(CWD. "/includes/class_save_file.php");  
//---------------- import from external url ---------

  $imtype = file_extension($url);
  if(in_array($imtype,$upload_types)){

  $fl = new save_file($url,$upload_folder);
   
if($fl->status){
$saveto_filename =  $fl->saved_filename;
 
print "<script>
";
 
 
if($frm){
print "opener.document.forms['".$frm."'].elements['" . $f_name . "'].value = \"".$saveto_filename."\";" ;
        }else{
print "opener.document.forms['sender'].elements['" . $f_name . "'].value = \"".$saveto_filename."\";";
   }

   
 if(in_array($imtype,array("jpg","png","gif","jpeg","bmp"))){  
   print "window.location = \"uploader.php?action=resize&filename=$saveto_filename&f_name=$f_name&frm=$frm\";";     
 }else{  
print "
window.close(); ";
 }

print "</script>\n";


}else{
print_admin_table("<center>".$fl->last_error_description."</center>");
die();    
}


     }else{
     print_admin_table("<center>$phrases[this_filetype_not_allowed]</center>");
die();
}
  
}else{
   print_admin_table("<center>Missing Data</center>");  
   die();    
}
}



//---------- resize pic ok-----------
if($action=="resize_ok" && $filename){

if($resize){
$uploader_thumb_width = intval($uploader_thumb_width);
$uploader_thumb_hieght = intval($uploader_thumb_hieght);

if($uploader_thumb_width <=0){$uploader_thumb_width=100;}
if($uploader_thumb_hieght <=0){$uploader_thumb_hieght=100;}

	$thumb_saved =  create_thumb($filename,$uploader_thumb_width,$uploader_thumb_hieght,$fixed);
    if($thumb_saved){
 	 @unlink(CWD . "/". $filename);
     
      if($replace_exists){
     @rename($thumb_saved,$filename);
     $thumb_saved = $filename;
     }
     
 print "<script>
";
   
if($frm){
print "opener.document.forms['".$frm."'].elements['" . $f_name . "'].value = \"".$thumb_saved."\";" ;
        }else{
print "opener.document.forms['sender'].elements['" . $f_name . "'].value = \"".$thumb_saved."\";";
   }
   
print "</script>";
    }
 
    }
   print "
   <script>
window.close();

</script>"; 
    
	}
    
//-------- resize pic form ------
if($action=="resize"){
if($filename){
print "<center>
<form action='uploader.php' method='post'>
<input type='hidden' name='action' value='resize_ok'>
<input type='hidden' name='filename' value=\"$filename\"> 
<input type='hidden' name='f_name' value=\"$f_name\"> 
<input type='hidden' name='frm' value=\"$frm\"> 


  <br>
<fieldset style=\"width: 90%; padding: 2 \">
<input name='resize' type=checkbox value='1'>
$phrases[auto_photos_resize]  ($phrases[cp_photo_resize_width] : <input type=text id='uploader_thumb_width' name='uploader_thumb_width' size=2 value=\"$settings[uploader_thumb_width]\"> &nbsp;&nbsp;$phrases[cp_photo_resize_hieght]: <input type=text name=uploader_thumb_hieght size=2 value=\"$settings[uploader_thumb_hieght]\"> <input type=\"checkbox\" name=\"fixed\" value=1>$phrases[fixed])
</fieldset>
<br><br>
<input type=submit value='  $phrases[continue]  '> <br></form>";
}else{
      print_admin_table("<center>Missing Data</center>");  
   die();    
}
}
//--------------------------------



//---- uploader form --------
if(!$action){




$upload_max = convert_number_format(ini_get('upload_max_filesize'));
$post_max = (convert_number_format(ini_get('post_max_size'))/2) ;
$max_size = iif($upload_max < $post_max,$upload_max,$post_max);



print "
<center>
<table width=90% class=grid>
<tr><td align=center>
<form action='uploader.php' method=post enctype=\"multipart/form-data\" name='sender'>
<center><table width=90%><tr><td>
<input type='radio' name='external_url' value=0 checked onClick=\"show_uploader_options(0);\">$phrases[local_file_uploader]  </td>
<td><input type='radio' name='external_url' value=1 onClick=\"show_uploader_options(1);\">$phrases[external_file_uploader]</td></tr></table></center>

<input type='hidden' name='action' value='upload'>
<input type=hidden name=folder value='$folder'>
<input type=hidden name=f_name value='$f_name'>
<input type=hidden name=frm value='$frm'> ";



print "<fieldset style=\"width: 90%; padding: 2;text-align:center;direction:ltr;\" id=file_field> ";

//<b> $phrases[the_file]  : </b><input type=file dir=ltr size=25 name=datafile>";


//----------------
 ?>
 <br>
 <input type="file" name="uploadify" id="uploadify" />
 <br>   
 <script type="text/javascript">
 
 
$(document).ready(function() {
      
       
        
    $("#uploadify").uploadify({
        'uploader'       : 'uploadify.swf',
        'script'         : 'ajax.php',
        'fileDataName'   : 'datafile',
        'cancelImg'      : 'images/cancel.png',
        'scriptData'     : {'action':'upload','upload_folder_suffix':'<?=$folder?>'}, 
        'auto'           : true,
        'multi'          : false,
        'rollover'       : false,
        'displayData': 'both', 
        'sizeLimit'      : <?=$max_size?>,
        'fileDesc'       : <?="'Allowed Files (".implode($upload_types,",").")'"?>,
        'fileExt'        : <?="'*.".implode($upload_types,";*.")."'"?>,
        'onComplete': function(event, queueID, fileObj, reposnse, data) {
          
    var reposnse_json = jQuery.parseJSON(reposnse); 
     
    
        
 if(reposnse_json.status==1){  
<?
      
if($frm){
print "opener.document.forms['".$frm."'].elements['" . $f_name . "'].value = reposnse_json.file;" ;
        }else{
print "opener.document.forms['sender'].elements['" . $f_name . "'].value = reposnse_json.file;";

   }
?>
if(reposnse_json.show_resize==1){
 window.location = "uploader.php?action=resize&filename="+reposnse_json.file+"&f_name=<?=$f_name?>&frm=<?=$frm?>";   
}else{
 window.close();  
} 
    }else{
        alert(reposnse_json.msg); 
    }
},
'onError':function(event,queueID,fileObj,errorObj){
    alert("Error: " + errorObj.type + " : " + errorObj.info) ;   
}


    });
});
</script>
<?
//--------------------





if($upload_max || $post_max){
print "Max: ".convert_number_format($max_size,2,true)." ";
}
print "

</fieldset>

<fieldset style=\"width: 90%; padding: 2 ;display:none\" id=url_field>
<b> $phrases[the_url]  : </b><input type=text dir=ltr size=30 name=url value='http://'>
<br><br>
<input type=submit value=' $phrases[upload_file_do] '>  
</fieldset>
";





          print "<br>
       
          
</form>\n ";

$count = count($upload_types);
for ($i=0; $i<$count; $i++) {
$allowed_types .= "$upload_types[$i] &nbsp;";
}

print "<br>
$phrases[allowed_filetypes] :
<font color='#CE0000'>$allowed_types</font>\n

</td></tr></table></center>";


}

}else{
        print_admin_table("<center>  $settings[uploader_msg] </center> ","90%") ;
        }
 

}else{
print_admin_table("<center>$phrases[please_login_first]</center>");
     }



     print "</html>";
     ?>
