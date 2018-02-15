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
 
 
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
 
<html xmlns="http://www.w3.org/1999/xhtml" dir="rtl"> 
 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 

 
 <script language='javascript'> 
 
   var NS = (navigator.appName=="Netscape")?true:false; 

     function FitPic() { 
       iWidth = (NS)?window.innerWidth:document.body.clientWidth; 
       iHeight = (NS)?window.innerHeight:document.body.clientHeight; 
       iWidth = document.images[0].width - iWidth; 
       iHeight = document.images[0].height ; 
                    
       window.resizeBy(iWidth, iHeight); 
       self.focus(); 
     }; 
 </script>

<?
if(!strpos(strtolower($_GET['url']),"javascript")){
print "<title>".htmlspecialchars($_GET['title'])."</title> 
</head> 
<body onload='FitPic();' topmargin=\"0\"  
marginheight=\"0\" leftmargin=\"0\" marginwidth=\"0\">"; 

 print "<img src=\"" . htmlspecialchars(strip_tags($_GET['url'])) . "\" border=0> 
";
}else{
    die();
}
?>
</body>
</html>

