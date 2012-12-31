<?
/* 09-07-2012 : bug fixed : get_movie_file_icons : no custom url_watch implement :: */

//-----------------------------
define('MEMBER_SQL',"member_sql");
//------------------------------
define('GLOBAL_LOADED',true);
//----------------------------------
define('SCRIPT_NAME',"movies"); 
define('SCRIPT_VER',"2.0");   

//----------- current work dir definition -------
define('CWD', (($getcwd = getcwd()) ? str_replace("\\","/",$getcwd) : '.'));
define('CFN',basename($_SERVER['SCRIPT_FILENAME']));
//---------------------------------------------
require(CWD . "/config.php") ;


//---------- custom error handler --------//
if($custom_error_handler){
$old_error_handler = set_error_handler("error_handler");
}  


//----- remove slashes if magic quotes -----//
function stripslashes_deep($value){
   return (is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value));
}

if(get_magic_quotes_gpc()){ 
$_POST = array_map('stripslashes_deep',$_POST);
$_GET = array_map('stripslashes_deep',$_GET); 
$_COOKIE = array_map('stripslashes_deep',$_COOKIE); 
}

//--------- extract variabls -----------------------
 if (!empty($_POST)) {extract($_POST);}
if (!empty($_GET)) {extract($_GET);}
if (!empty($_ENV)) {extract($_ENV);}
//-----------------------------------------------------


//------ clean global vars ---------//
$_SERVER['QUERY_STRING'] = strip_tags($_SERVER['QUERY_STRING']);
$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);
$_SERVER['REQUEST_URI'] = strip_tags($_SERVER['REQUEST_URI']);
$PHP_SELF = strip_tags($_SERVER['PHP_SELF']);

define("CUR_FILENAME",$PHP_SELF);

//---------------------- common variables types clean-----------------------
 if($id){if(is_array($id)){$id=array_map("intval",$id);}else{$id=(int) $id;}}
if($cat){if(is_array($cat)){$id=array_map("intval",$cat);}else{$cat=(int) $cat;}}


require(CWD . "/includes/functions_db.php") ;
//---------------------------
db_connect($db_host,$db_username,$db_password,$db_name,$db_charset);




// ------------- lang dir -------------
if($global_lang=="arabic" || $global_lang=="ar"){
$global_dir = "rtl" ;
$global_align = "right" ;
$global_align_x = "left"; 
}else{
$global_dir = "ltr" ;
$global_align = "left" ;
$global_align_x = "right" ; 
}

//--------------- Load Phrases ---------------------
$phrases = array();
$qr = db_query("select * from movies_phrases");
while($data = db_fetch($qr)){

$phrases["$data[name]"] = $data['value'] ;
        }
 //------------------------------ 
 
 //----- actions ------
 $actions_checks = array(
"$phrases[main_page]" => 'main' ,
"$phrases[browse_movies]" => 'browse.php',
"$phrases[movie_info]" => 'movie_info.php', 
"$phrases[movie_actors]" => 'movie_actors.php', 
"$phrases[movie_photos]" => 'movie_photos.php',   
  
"$phrases[the_news]" => 'news.php',
"$phrases[pages]" => 'pages',
"$phrases[the_search]" => 'search.php' ,
"$phrases[the_votes]" => 'votes.php',
"$phrases[actor_photos]" => 'actor_photos.php', 

"$phrases[the_statics]" => 'statics',
"$phrases[contact_us]" => 'contactus.php'
);

//------ permissions -----
$permissions_checks = array(
"$phrases[the_actors]" => 'actors',
"$phrases[the_players]" => 'players' , 
"$phrases[new_movies_list]" => 'new_menu' ,
"$phrases[the_templates]" => 'templates' ,
"$phrases[the_news]" => 'news' ,
"$phrases[the_phrases]" => 'phrases' ,
"$phrases[the_banners]" => 'adv',
"$phrases[the_votes]" => 'votes',
"$phrases[the_members]" => 'members',
"$phrases[movies_fields]" => 'movies_fields',
"$phrases[the_comments]" => 'comments',
"$phrases[the_reports]" => 'reports' 
);

//---- banners -----
$banners_places = array(
"$phrases[offers_menu]"=>'offer',
"$phrases[bnr_header]"=> 'header',
"$phrases[bnr_footer]"=> 'footer',
"$phrases[bnr_open]" => 'open',
"$phrases[bnr_close]" => 'close',
"$phrases[bnr_menu]"=> 'menu'

);

//---- order by -----
$orderby_checks = array(
"$phrases[the_name]" => 'name',
"$phrases[release_year]"=>'year',
"$phrases[add_date]" => 'id', 
"$phrases[the_most_voted]" => 'votes' 
);


//--- privacy -----
$privacy_settings_array = array(
"0" => "$phrases[for_any_one]",
"1" => "$phrases[for_friends_only]",
"2" => "$phrases[for_no_one]");


//---- comments --------
$comments_types_phrases = array(
"movie"=>"$phrases[the_movies]",
"actor"=>"$phrases[the_actors]",
"photo"=>"$phrases[movie_photos]",
"actor_photo"=>"$phrases[actor_photos]",
"news"=>"$phrases[the_news]");

$comments_types = array_keys($comments_types_phrases);
 
 
//----- reports ----- 
$reports_types_phrases = array(
"comment"=>$phrases['the_comments'],
"movie_file"=>$phrases['movies_files'],
"member"=>$phrases['the_members']);

$reports_types = array_keys($reports_types_phrases);


//----------------- 
 
 function utf8_substr($str,$from,$len){
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
}

   unset($letters_groups,$letters_groups_names);
 //-------- letters groups ----------
    $letters_groups[0] = array('ا','أ','إ','آ');
    $letters_groups[1] = array('ب','ت','ث');
    $letters_groups[2] = array('ج','ح','خ');
   $letters_groups[3] = array('د','ذ','ر','ز');
   $letters_groups[4] = array('س','ش');
   $letters_groups[5] = array('ص','ض','ط','ظ');
   $letters_groups[6] = array('ع','غ');
   $letters_groups[7] = array('ف','ق');
   $letters_groups[8] = array('ك','ل');
   $letters_groups[9] = array('م','ن');
   $letters_groups[10] = array('ه');
   $letters_groups[11] = array('و','ي');
   $letters_groups[12] = array('A','B','C');
   $letters_groups[13] = array('D','E','F');
   $letters_groups[14] = array('G','H','I');
   $letters_groups[15] = array('J','K','L');
   $letters_groups[16] = array('M','N');
   $letters_groups[17] = array('O','P','Q');
   $letters_groups[18] = array('R','S','T');
   $letters_groups[19] = array('U','V','W');
   $letters_groups[20] = array('X','Y','Z');
   $letters_groups[21] = array('0','1','2','3','4','5','6','7','8','9');
 //-------- letter groups names ------------
 $letters_groups_names[21] = "0-9";
 //--------- fix unnamed groups ------------
  for($i=0;$i<count($letters_groups);$i++){
  if(!$letters_groups_names[$i]){
      $letters_groups_names[$i] = implode(" ",$letters_groups[$i]);
  }
  }
  
 //--------------------------------------------
        

        
$settings = array();

//--------------- Get Settings --------------------------
function load_settings(){
global  $settings ;
$qr = db_query("select * from movies_settings");
while($data = db_fetch($qr)){

$settings["$data[name]"] = $data['value'] ;
        }
}

 //------------------ Load Settings ---------
load_settings();


$sitename = $settings['sitename'] ;
$section_name = $settings['section_name'] ;
$siteurl = "http://$_SERVER[HTTP_HOST]" ;
$script_path = trim(str_replace(rtrim(str_replace('\\', '/',$_SERVER['DOCUMENT_ROOT']),"/"),"",CWD),"/");
$scripturl = $siteurl . iif($script_path,"/".$script_path,"");
$upload_types = explode(',',str_replace(" ","",$settings['uploader_types']));
$mailing_email = str_replace("{domain_name}",$_SERVER['HTTP_HOST'],$settings['mailing_email']);



//------------- timezone ------------------
if($settings['timezone']){date_default_timezone_set($settings['timezone']);} 
//-------------------------------------------


//------ validate styleid functon ------
function is_valid_styleid($styleid){
if(is_numeric($styleid)){
$data = db_qr_fetch("select count(id) as num from movies_templates_cats where id='$styleid' and selectable=1");
if($data['num']){
    return true;
}else{
    return false;
    }
}else{
    return false;
}
}
//----- check if valid styleid -------
$styleid=(isset($styleid) ? intval($styleid) : get_cookie("styleid"));
if(!is_valid_styleid($styleid)){
$styleid = $settings['default_styleid'];
if(!is_valid_styleid($styleid)){
$styleid = 1;
}
}
//----- get style settings ----//
$data_style = db_qr_fetch("select images from  movies_templates_cats where id='$styleid'");
$style['images'] =  iif($data_style['images'],$data_style['images'],"images");

set_cookie('styleid', intval($styleid));


//----------- Load links -----------
 $qr=db_query("select * from movies_links");
 while($data=db_fetch($qr)){
 $links[$data['name']] = $data['value'];
 }
 
 
//------- theme file ---------
require(CWD . "/includes/functions_themes.php") ;
//---------
        

require(CWD . "/includes/functions_members.php") ;

init_members_connector(); 

require(CWD . '/includes/class_tabs.php') ; 



require(CWD . '/includes/functions_comments.php') ;  





//--- if admin function -------
function if_admin($dep="",$continue=0){
        global $user_info,$phrases ;

        if(!$dep){

        if($user_info['groupid'] != 1){



        if(!$continue){

        print_admin_table("<center>$phrases[access_denied]</center>");

         die();

         }
           return false;
         }else{
                 return true;
                 }
          }else{
           if($user_info['groupid'] != 1){

                  $data=db_qr_fetch("select * from movies_user where id='$user_info[id]'");
                  $prm_array = explode(",",$data['cp_permisions']);

                  if(!in_array($dep,$prm_array)){

        if(!$continue){
         print_admin_table("<center>$phrases[access_denied]</center>");
         die();
                           }
                            return false;
                          }else{
                          return true;
                                  }
                 }else{
                         return true;
                         }
            }
         }
//---------- validate email --------
function check_email_address($email) {
if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
    return false;
}else{
    return true;
}
}
 
//---------------------------- Admin Login Function ---------------------------------
$user_info = array();

function check_admin_login(){
      global $user_info;

$user_info['username'] = get_cookie('admin_username');
$user_info['password'] = get_cookie('admin_password');
$user_info['id'] = intval(get_cookie('admin_id'));


   if($user_info['id']){
   $qr = db_query("select * from movies_user where id='$user_info[id]'");
         if(db_num($qr)){
           $data = db_fetch($qr);
           if($data['username'] == $user_info['username'] && md5($data['password']) == $user_info['password']){
           $user_info['email'] = $data['email'];
           $user_info['groupid'] = $data['group_id'];
                   return true ;
                   }else{
                           return false ;
                           }

                 }else{
                         return false ;
                         }

           }else{
                   return false ;
                   }

        }
//------------------------------------------------
function get_image($src,$default="",$path=""){
    global $style;
         if($src){
              return $path.$src ;
            }else{
    if($default){
        return $path.$default;
        }else{
    return $path."$style[images]/no_pic.gif" ;
    }
    }
    }
                        



//------------ copyrights text ---------------------
function print_copyrights(){
global $_SERVER,$settings,$copyrights_lang ;

if(COPYRIGHTS_TXT_MAIN){
if($copyrights_lang == "arabic"){
print "<p align=center>جميع الحقوق محفوظة لـ :
<a target=\"_blank\" href=\"http://$_SERVER[HTTP_HOST]\">$settings[copyrights_sitename]</a> © " . date('Y') . " <br>
برمجة <a target=\"_blank\" href=\"http://allomani.com/\"> اللوماني للخدمات البرمجية </a> © 2011";
}else{
print "<p align=center>Copyright © ". date('Y')." <a target=\"_blank\" href=\"http://$_SERVER[HTTP_HOST]\">$settings[copyrights_sitename]</a> - All rights reserved <br>
Programmed By <a target=\"_blank\" href=\"http://allomani.com/\"> Allomani </a> © 2011";
    }
}
        }




function read_file($filename){
$fn = fopen($filename,"r");
$fdata = fread($fn,filesize($filename));
fclose($fn);
return $fdata ;
}
//--------------------- Delete File ---------------
function delete_file($filename){
    
 if(file_exists($filename)){
         @unlink($filename);
 }
        }
//---------------------- Send Email Function -------------------
function send_email($from_name,$from_email,$to_email,$subject,$msg,$html=0,$encoding="",$reply_to=""){
        global $PHP_SELF,$smtp_settings,$settings ;
    $from_name = htmlspecialchars($from_name);
    $from_email = htmlspecialchars($from_email);
    $to_email = htmlspecialchars($to_email);
    $subject = htmlspecialchars($subject);
   // $msg=htmlspecialchars($msg);

   $html = $settings['mailing_default_use_html'];
 //  $encoding = $settings['mailing_default_encoding'];
   if(!$encoding){$encoding =  $settings['site_pages_encoding'];} 

  //  $from = "$from_name <$from_email>" ;

  $from = "=?".$encoding."?B?".base64_encode($from_name)."?= <$from_email>" ;
  $subject = "=?".$encoding."?B?".base64_encode($subject)."?=";
    

    $mailHeader  = 'From: '.$from.' '."\r\n";
    $mailHeader .= "Reply-To: ".iif($reply_to,$reply_to,$from_email)."\r\n";
    $mailHeader .= "Return-Path: $from_email\r\n";
    $mailHeader .= "To: $to_email\r\n";
    $mailheader.="MIME-Version: 1.0\r\n";
    $mailHeader .= "Content-Type: ".iif($html,"text/html","text/plain")."; charset=".($encoding ? $encoding : $settings['site_pages_encoding'])."\r\n";
    $mailHeader .= "Subject: $subject\r\n";
    $mailHeader .= "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")."\r\n";
    $mailHeader .= "X-EWESITE: Allomani\r\n";
    $mailHeader .= "X-Mailer: PHP/".phpversion()."\r\n";
    $mailHeader .= "X-Mailer-File: "."http://".$_SERVER['HTTP_HOST'].($script_path ? "/".$script_path:"").$PHP_SELF."\r\n";
    $mailHeader .= "X-Sender-IP: {$_SERVER['REMOTE_ADDR']}\r\n";




    if($smtp_settings['enable']){

   if(!class_exists("smtp_class")){
   require_once(CWD ."/includes/class_smtp.php");
   }

   $smtp=new smtp_class;

    $smtp->host_name=$smtp_settings['host_name'];
    $smtp->host_port=$smtp_settings['host_port'];
    $smtp->ssl=$smtp_settings['ssl'];
    $smtp->localhost="localhost";       /* Your computer address */
    $smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
    $smtp->timeout=$smtp_settings['timeout'];    /* Set to the number of seconds wait for a successful connection to the SMTP server */
    $smtp->data_timeout=0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
                                           Set to 0 to use the same defined in the timeout variable */
    $smtp->debug=$smtp_settings['debug'];                     /* Set to 1 to output the communication with the SMTP server */
    $smtp->html_debug=1;                /* Set to 1 to format the debug output as HTML */

    if($smtp_settings['username'] && $smtp_settings['password']){
    $smtp->pop3_auth_host=$smtp_settings['host_name'];           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
    $smtp->user=$smtp_settings['username'];                     /* Set to the user name if the server requires authetication */
     $smtp->password=$smtp_settings['password'];                 /* Set to the authetication password */
    $smtp->realm="";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
    }

    $smtp->workstation="";              /* Workstation name for NTLM authentication */
    $smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
                                           Leave it empty to make the class negotiate if necessary */

   $mailResult =  $smtp->SendMessage(
        $from_email,
        array(
            $to_email
        ),
        array(
            $mailHeader
        ),
        $msg,0);

        if($mailResult){
              return true ;
                }else{
                    if($smtp_settings['show_errors']){
                    print "<b>SMTP Error: </b> ".$smtp->error ."<br>";
                    }
               return false;
               }

    }else{
    $mailResult = @mail($to_email,$subject,$msg,$mailHeader);

               if($mailResult){
              return true ;
                }else{
               return false;
               }
    }
        }
//---------------------------------------------
function execphp_fix_tag($match)
{
        // replacing WPs strange PHP tag handling with a functioning tag pair
        $output = '<?php'. $match[2]. '?>';
        return $output;
}

//------------------------------------------------------------
function run_php($content)
{
 /*
$content = str_replace(array("&#8216;", "&#8217;"), "'",$content);
$content = str_replace(array("&#8221;", "&#8220;"), '"', $content);
$content = str_replace("&Prime;", '"', $content);
$content = str_replace("&prime;", "'", $content);
        // for debugging also group unimportant components with ()
        // to check them with a print_r($matches)
        $pattern = '/'.
                '(?:(?:<)|(\[))[\s]*\?php'. // the opening of the <?php or [?php tag
                '(((([\'\"])([^\\\5]|\\.)*?\5)|(.*?))*)'. // ignore content of PHP quoted strings
                '\?(?(1)\]|>)'. // the closing ? > or ?] tag
                '/is';
      $content = preg_replace_callback($pattern, 'execphp_fix_tag', $content);
        // to be compatible with older PHP4 installations
        // don't use fancy ob_XXX shortcut functions
        */
        ob_start();
        eval(" ?> $content ");
        $output = ob_get_contents();
        ob_end_clean();
        print $output;
}

//---------- get players types --------------
function get_players_data($id=""){
    $qr=db_query("select * from movies_players ".iif($id,"where id='$id'")." order by id asc");
    $i=0;
    while($data=db_fetch($qr)){
        $players[$i] = $data;
        
         
        $players[$i]['exts'] = explode(",",$data['exts']);
         
        $i++;
    }
    
    return $players;
}

//---------- get player types --------------
function get_player_by_id($id){
    $players = get_players_data($id);
    return $players[0];
}
//------------ get file player -----------
function get_file_player($url,$players){
    if(is_array($players)){
        foreach($players as $player){
            if($player['id'] != 1){
            if(is_array($player['exts'])){
              foreach($player['exts'] as $ext){
                  if(strchr(strtolower($url),strtolower($ext))){
                   $player_data=$player;
                   $found=true;
                   break;  
                  }
              }  
            }    
            }
        if($found){break;}
        }
    
    if($found){
        return $player_data;
    }else{
        return $players[0];
    }
    }else{
        return false;
    }
}


//--------------------Get_cats---------------------------
function get_cats($id){
    
      $cats_arr = array();
   $cats_arr[]=$id;
                               
         $qr1 = db_query("select id from movies_cats where cat='$id'");
         while($data1 = db_fetch($qr1)){
          $nxx = get_cats($data1['id']);
          if(is_array($nxx)){
              $cats_arr = array_merge($nxx,$cats_arr);
          }
           unset($nxx);
          }

          return  $cats_arr ;
 
         }
         

 
 //------ cat path str -----
function get_cat_path_str($cat){
             $dir_data['cat'] = intval($cat) ;
               $path_arr[] = $dir_data['cat'];
while($dir_data['cat']!=0){
   
   $dir_data = db_qr_fetch("select id,cat from movies_cats where id='$dir_data[cat]'");
   $path_arr[] = $dir_data['cat'];
}
return implode(",",$path_arr);
}
 //----------------- Path Links ---------
 function print_path_links($cat,$filename=""){
     global $phrases,$style,$links,$global_align;
     
     $cat=intval($cat);
   if($cat) { 
   $data_cat = db_qr_fetch("select name,id,cat,path from movies_cats where id='$cat'");     
   $qr=db_query("select name,id,cat from movies_cats where id IN (".$data_cat['path'].")");
   while($data=db_fetch($qr)){
       $cats_data[$data['id']] = $data;
   }
    
   $cats_array = explode(",",$data_cat['path']);
   
foreach($cats_array as $id){
  // $dir_data = db_qr_fetch("select name,id,cat from movies_products_cats where id='$dir_data[cat]'");
     if($id){
      $dir_data =  $cats_data[$id];

        $dir_content = "<a class='path_link' href='".str_replace('{id}',$dir_data['id'],$links['links_cats'])."'>$dir_data[name]</a> / ". $dir_content  ;
     }
        }      
 }                                   
   print "<p align='$global_align' class='path'><img src='$style[images]/arrw.gif'> <a class='path_link' href='".str_replace('{id}','0',$links['links_cats'])."'>$phrases[the_cats] </a> / $dir_content " . "$filename</p>";

 }


       

//--------------------------- File Name Only --------------------------
function filename_only($filename){
        $arr = split("/",$filename);

        if(count($arr)){
        $x = count($arr)-1;
                return $arr[$x];
                }else{
                        return $filename ;
                        }
}

//--------- iif expression ------------
function iif($expression, $returntrue, $returnfalse = '')
{
    return ($expression ? $returntrue : $returnfalse);
}

//------- set cookies function -----------
function set_cookie($name,$value="",$expire=0){
global $cookies_prefix,$cookies_timemout,$cookies_path,$cookies_domain;
$name = $cookies_prefix . $name;
if($expire){
    $k_timeout = $expire;
}else{
$k_timeout = time() + (60 * 60 * 24 * intval($cookies_timemout));
}

setcookie($name, $value, $k_timeout,$cookies_path,$cookies_domain);
}
//--------- get cookies funtion ---------
function get_cookie($name){
global $cookies_prefix,$_COOKIE;
$name = $cookies_prefix . $name;
return $_COOKIE[$name];
}

//---------- Flush Function -------------
function data_flush()
{
    static $output_handler = null;
    if ($output_handler === null)
    {
        $output_handler = @ini_get('output_handler');
    }

    if ($output_handler == 'ob_gzhandler')
    {
        // forcing a flush with this is very bad
        return;
    }

    flush();
    if (PHP_VERSION  >= '4.2.0' AND function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false)
    {
        @ob_flush();
    }
    else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE)
    {
        @ob_end_flush();
        @ob_start();
    }
}

//----------- select row ------------
function print_select_row($name, $array, $selected = '', $options="" , $size = 0, $multiple = false,$same_values=false)
{
    global $vbulletin;

    $select = "<select name=\"$name\" id=\"sel_$name\"" . iif($size, " size=\"$size\"") . iif($multiple, ' multiple="multiple"') . iif($options , " $options").">\n";
    $select .= construct_select_options($array, $selected,$same_values);
    $select .= "</select>\n";

    print $select;
}


function construct_select_options($array, $selectedid = '',$same_values=false)
{
    if (is_array($array))
    {
        $options = '';
        foreach($array AS $key => $val)
        {
            if (is_array($val))
            {
                $options .= "\t\t<optgroup label=\"" . $key . "\">\n";
                $options .= construct_select_options($val, $selectedid, $tabindex, $htmlise);
                $options .= "\t\t</optgroup>\n";
            }
            else
            {
                if (is_array($selectedid))
                {
                    $selected = iif(in_array($key, $selectedid), ' selected="selected"', '');
                }
                else
                {
                    $selected = iif($key == $selectedid, ' selected="selected"', '');
                }
                $options .= "\t\t<option value=\"".($same_values ? $val : $key). "\"$selected>" . $val . "</option>\n";
            }
        }
    }
    return $options;
}
//---------- print text row ----------
function print_text_row($name,$value="",$size="",$dir="",$options=""){
print "<input type=text name=\"$name\"".iif($value," value=\"$value\"").iif($size," size=\"$size\"").iif($dir," dir=\"$dir\"").iif($options," $options").">";
}



//-------------- Get Remote filesize --------
function fetch_remote_filesize($url)
    {
        // since cURL supports any protocol we should check its http(s)
        preg_match('#^((http|ftp)s?):\/\/#i', $url, $check);
        
     /*   else if (false AND !empty($check) AND function_exists('curl_init') AND $ch = curl_init())
        { */
        if(function_exists('curl_init') AND $ch = curl_init()){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
            /* Need to enable this for self signed certs, do we want to do that?
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            */

            $header = curl_exec($ch);
            curl_close($ch);

            if ($header !== false)
            {
                preg_match('#Content-Length: (\d+)#i', $header, $matches);
                return sprintf('%u', $matches[1]);
            }
       }elseif (ini_get('allow_url_fopen') != 0 AND $check[1] == 'http')
        {
            $urlinfo = @parse_url($url);

            if (empty($urlinfo['port']))
            {
                $urlinfo['port'] = 80;
            }

            if ($fp = @fsockopen($urlinfo['host'], $urlinfo['port'], $errno, $errstr, 30))
            {
                fwrite($fp, 'HEAD ' . $url . " HTTP/1.1\r\n");
                fwrite($fp, 'HOST: ' . $urlinfo['host'] . "\r\n");
                fwrite($fp, "Connection: close\r\n\r\n");

                while (!feof($fp))
                {
                    $headers .= fgets($fp, 4096);
                }
                fclose ($fp);

                $headersarray = explode("\n", $headers);
                foreach($headersarray as $header)
                {
                    if (stristr($header, 'Content-Length') !== false)
                    {
                        $matches = array();
                        preg_match('#(\d+)#', $header, $matches);
                        return sprintf('%u', $matches[0]);
                    }
                }
            }
        }
        return false;   
    }
 //--------------- Get file Extension ----------
 function file_extension($filename)
{
    return strtolower(substr(strrchr($filename, '.'), 1));
}


//----------- Number Format --------------------
function convert_number_format($number, $decimals = 0, $bytesize = false, $decimalsep = null, $thousandsep = null)
{

    $type = '';

    if (empty($number))
    {
        return 0;
    }
    else if (preg_match('#^(\d+(?:\.\d+)?)(?>\s*)([mkg])b?$#i', trim($number), $matches))
    {
        switch(strtolower($matches[2]))
        {
            case 'g':
                $number = $matches[1] * 1073741824;
                break;
            case 'm':
                $number = $matches[1] * 1048576;
                break;
            case 'k':
                $number = $matches[1] * 1024;
                break;
            default:
                $number = $matches[1] * 1;
        }
    }

    if ($bytesize)
    {
        if ($number >= 1073741824)
        {
            $number = $number / 1073741824;
            $decimals = 2;
            $type = " GB";
        }
        else if ($number >= 1048576)
        {
            $number = $number / 1048576;
            $decimals = 2;
            $type = " MB";
        }
        else if ($number >= 1024)
        {
            $number = $number / 1024;
            $decimals = 1;
            $type = " KB";
        }
        else
        {
            $decimals = 0;
            $type = " Byte";
        }
    }

    if ($decimalsep === null)
    {
     //   $decimalsep = ".";
    }
    if ($thousandsep === null)
    {
    //    $thousandsep = ",";
    }

    if($decimalsep && $thousandsep){
    return str_replace('_', '&nbsp;', number_format($number, $decimals, $decimalsep, $thousandsep)) . $type;
    }else{
         return str_replace('_', '&nbsp;', round($number,$decimals)) . $type;
    }
}

//--------------------- preview Text ------------------------------------
function getPreviewText($text) {
             global $preview_text_limit ;
    // Strip all tags
    $desc = strip_tags(html_entity_decode($text), "<a><em>");
    $charlen = 0; $crs = 0;
    if(strlen_HTML($desc) == 0)
        $preview = substr($desc, 0, $preview_text_limit);
    else
    {
        $i = 0;
        while($charlen < 80)
        {
            $crs = strpos($desc, " ", $crs)+1;
            $lastopen = strrpos(substr($desc, 0, $crs), "<");
            $lastclose = strrpos(substr($desc, 0, $crs), ">");
            if($lastclose > $lastopen)
            {
                // we are not in a tag
                $preview = substr($desc, 0, $crs);
                $charlen = strlen_noHTML($preview);
            }
            $i++;
        }
    }
    return trim($preview)  ;

}


function strlen_noHtml($string){
    $crs = 0;
    $charlen = 0;
    $len = strlen($string);
    while($crs < $len)
    {
        $offset = $crs;
        $crs = strpos($string, "<", $offset);
        if($crs === false)
        {
           $crs = $len;
           $charlen += $crs - $offset;
        }
        else
        {
            $charlen += $crs - $offset;
            $crs = strpos($string, ">", $crs)+1;
        }
    }
    return $charlen;
}


function strlen_Html($string){
    $crs = 0;
    $charlen = 0;
    $len = strlen($string);
    while($crs < $len)
    {
        $scrs = strpos($string, "<", $crs);
        if($scrs === false)
        {
           $crs = $len;
        }
        else
        {
            $crs = strpos($string, ">", $scrs)+1;
            if($crs === false)
                $crs = $len;
            $charlen += $crs - $scrs;
        }
    }
    return $charlen;
}

//---- is english -----
function is_english($value){
    if(preg_match("/^([-a-zA-Z0-9_.!@#$&*+=|~%^()\\'])*$/", $value)){
    return true;
    }else{
        return false;
    } 
}


//------------- convert ar 2 en ------------------

function convert2en($filename){
    
if(!is_english($filename)){
$filename= str_replace("'","",$filename);
$filename= str_replace(" ","_",$filename);
$filename= str_replace("ا","a",$filename);
$filename= str_replace("أ","a",$filename);
$filename= str_replace("إ","i",$filename);
$filename= str_replace("ب","b",$filename);
$filename= str_replace("ت","t",$filename);
$filename= str_replace("ث","th",$filename);
$filename= str_replace("ج","g",$filename);
$filename= str_replace("ح","7",$filename);
$filename= str_replace("خ","k",$filename);
$filename= str_replace("د","d",$filename);
$filename= str_replace("ذ","d",$filename);
$filename= str_replace("ر","r",$filename);
$filename= str_replace("ز","z",$filename);
$filename= str_replace("س","s",$filename);
$filename= str_replace("ش","sh",$filename);
$filename= str_replace("ص","s",$filename);
$filename= str_replace("ض","5",$filename);
$filename= str_replace("ع","a",$filename);
$filename= str_replace("غ","gh",$filename);
$filename= str_replace("ف","f",$filename);
$filename= str_replace("ق","k",$filename);
$filename= str_replace("ك","k",$filename);
$filename= str_replace("ل","l",$filename);
$filename= str_replace("ن","n",$filename);
$filename= str_replace("ه","h",$filename);
$filename= str_replace("ي","y",$filename);
$filename= str_replace("ط","6",$filename);
$filename= str_replace("ظ","d",$filename);
$filename= str_replace("و","w",$filename);
$filename= str_replace("ؤ","o",$filename);
$filename= str_replace("ئ","i",$filename);
$filename= str_replace("لا","la",$filename);
$filename= str_replace("لأ","la",$filename);
$filename= str_replace("ى","a",$filename);
$filename= str_replace("ة","t",$filename);
$filename= str_replace("م","m",$filename);
}


return $filename ;

}

 

 
 //----------- Get Hooks ------------
function get_plugins_hooks(){

$hooklocations = array();
    require_once(CWD . '/includes/class_xml.php');
    $handle = opendir(CWD . '/xml/');
    while (($file = readdir($handle)) !== false)
    {
        if (!preg_match('#^hooks_(.*).xml$#i', $file, $matches))
        {
            continue;
        }
        $product = $matches[1];

        $phrased_product = $products[($product ? $product : 'allomani')];
        if (!$phrased_product)
        {
            $phrased_product = $product;
        }

        $xmlobj = new XMLparser(false, CWD . "/xml/$file");
        $xml = $xmlobj->parse();

        if (!is_array($xml['hooktype'][0]))
        {
            // ugly kludge but it works...
            $xml['hooktype'] = array($xml['hooktype']);
        }

        foreach ($xml['hooktype'] AS $key => $hooks)
        {
            if (!is_numeric($key))
            {
                continue;
            }
            //$phrased_type = isset($vbphrase["hooktype_$hooks[type]"]) ? $vbphrase["hooktype_$hooks[type]"] : $hooks['type'];
            $phrased_type =  $hooks['type'];
            $hooktype = $phrased_product . ' : ' . $phrased_type;

            $hooklocations["$hooktype"] = array();

            if (!is_array($hooks['hook']))
            {
                $hooks['hook'] = array($hooks['hook']);
            }

            foreach ($hooks['hook'] AS $hook)
            {
                $hookid = (is_string($hook) ? $hook : $hook['value']);
                $hooklocations["$hooktype"]["$hookid"] = $hookid;
            }
        }
    }
    ksort($hooklocations);
    return $hooklocations ;
    }

//--------- Get used hooks List -----------
$qr = db_query("select hookid from movies_hooks where active='1'");
while($data = db_fetch($qr)){
$used_hooks[] = $data['hookid'];
}
unset($qr,$data);
//-------------- compile hook --------------
function compile_hook($hookid){
global $used_hooks;
if(is_array($used_hooks)){
if(in_array($hookid,$used_hooks)){
$qr = db_query("select code from movies_hooks where hookid='".db_escape($hookid)."' and active='1' order by ord asc");
if(db_num($qr)){
while($data=db_fetch($qr)){
run_php($data['code']);
    }
}else{
 return false;
 }
 }else{
     return false;
     }
     }else{
         return false;
         }
}


//---------- Pages Links ---------//
function print_pages_links($start,$items_count,$items_perpage,$page_string){
     global $phrases;
 
 
$pages=intval($items_count/$items_perpage);
if ($items_count%$items_perpage){$pages++;}


$pages_line_limit = 8;
$pages_line_min = $pages_line_limit / 2;

$cur_page = iif($start,($start/$items_perpage)+1,1);
$f_start =  iif($cur_page <= $pages_line_min,1,$cur_page-$pages_line_min);
$f_end = iif($pages < $pages_line_min,$pages,iif($f_start+$pages_line_limit <=$pages,$f_start+$pages_line_limit ,$pages)) ;


if ($items_count>$items_perpage){
    print "<p align=center> $phrases[pages] : ";
if($start >0){
echo "<a title=\"$phrases[first_page]\"  href='".str_replace("{start}",0,$page_string)."'><<</a> \n";

$previouspage = $start - $items_perpage;
echo "<a title=\"$phrases[prev_page]\" href='".str_replace("{start}",$previouspage,$page_string)."'><</a>\n";
}




for ($i = $f_start; $i <= $f_end; $i++) {
$nextpag = $items_perpage*($i-1);
if ($nextpag == $start){
echo "<font size=2 face=tahoma><b>$i</b></font>&nbsp;\n";
}else{
echo "<a href='".str_replace("{start}",$nextpag,$page_string)."'>[$i]</a>&nbsp;\n";}
}


if (! ( ($start/$items_perpage) == ($pages - 1) ) && ($pages != 1) ){
$last_pag = ($pages-1)*$items_perpage;
$nextpag = $start+$items_perpage; 

//if($last_pag != $nextpag){  
if($f_end < $pages){  
echo " ... <a href='".str_replace("{start}",$last_pag,$page_string)."'>[$pages]</a>\n";
}

echo "<a title=\"$phrases[next_page]\" href='".str_replace("{start}",$nextpag,$page_string)."'>></a>\n";


echo "  <a title=\"$phrases[last_page]\" href='".str_replace("{start}",$last_pag,$page_string)."'>>></a>\n";

}
echo "</p>";
}
}

/*
function print_pages_links($start,$items_count,$items_perpage,$page_string){
  
 global $phrases ;
   
//$previous_page=$start - $songs_perpage;
//$next_page=$start + $songs_perpage;


if ($items_count>$items_perpage){
echo "<p align=center>$phrases[pages] : ";
if($start >0){
$previouspage = $start - $items_perpage;
echo "<a href='".str_replace("{start}",$previouspage,$page_string)."'><</a>\n";
}


$pages=intval($items_count/$items_perpage);
if ($items_count%$items_perpage){$pages++;}
for ($i = 1; $i <= $pages; $i++) {
$nextpag = $items_perpage*($i-1);
if ($nextpag == $start){
echo "<font size=2 face=tahoma><b>$i</b></font>&nbsp;\n";
}else{
echo "<a href='".str_replace("{start}",$nextpag,$page_string)."'>[$i]</a>&nbsp;\n";}
}
if (! ( ($start/$items_perpage) == ($pages - 1) ) && ($pages != 1) )
{$nextpag = $start+$items_perpage;
echo "<a href='".str_replace("{start}",$nextpag,$page_string)."'>></a>\n";}
echo "</p>";}
}    */


//------- array remove empty values --------//
function array_remove_empty_values($arr){
       for($i=0;$i<count($arr);$i++){
         $key = key($arr);
            $value = current($arr);
           if($value){
               $new_arr[$key] = $value;
           }
            next($arr);    
       }
       return  $new_arr;
   } 
   
   
   
   // ---- Date / Time -------
   function datetime($format="",$time=""){
       return date(iif($format,$format,"Y-m-d h:i:s"),iif($time,$time,time()));
   }
   
   
   //------- Error Handler ----------//
   function error_handler($errno, $errstr, $errfile, $errline,$vars) {
        global $display_errors,$log_errors;
       
       switch ($errno)
    {
        case E_WARNING:
        case E_USER_WARNING:
            /* Don't log warnings due to to the false bug reports about valid warnings that we suppress, but still appear in the log
            */

            if($log_errors){ 
            $message = "Warning: $errstr in $errfile on line $errline";
            do_error_log($message, 'php');
            }
           

            if (!$display_errors || !error_reporting())
            {
                return;
            }
            
            $errfile = str_replace(CWD.DIRECTORY_SEPARATOR, '', $errfile);
            echo "<br /><strong>Warning</strong>: $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
        break;

        case E_USER_ERROR:  
            
            if($log_errors){ 
            $message = "Fatal error: $errstr in $errfile on line $errline";
            do_error_log($message, 'php');
            }
            
            
            if ($display_errors)
            {
                $errfile = str_replace(CWD.DIRECTORY_SEPARATOR, '', $errfile);
                echo "<br /><strong>Fatal error:</strong> $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br />";
            }
            exit;
        break;
    }
}

//--------- Error Log ---------//
function do_error_log($msg , $type='php'){ 
global $logs_path,$log_max_size,$custom_error_handler;

$trace =  @debug_backtrace() ;
 
 //$args = (array) $trace[1]['args'];
  //".implode(",",$args)."
       
$dt = date("Y-m-d H:i:s (T)");
$err = $dt." : ".$msg."\r\n";
if($trace[1]['function']){
$err .=$trace[1]['function']."() in : ".$trace[1]['file'].":".$trace[1]['line']."\r\n";
}
$err .= "-------------- \r\n";

 if($custom_error_handler){
if(!file_exists($logs_path)){@mkdir($logs_path);}
 
  if($type=="db"){
  $log_file =  "$logs_path/error_db.log" ;
  $log_file_new  = "$logs_path/error_db_".date("Y_m_d_h_i_s").".log" ;  
  }else{
       $log_file =  "$logs_path/error.log" ;
  $log_file_new  = "$logs_path/error_".date("Y_m_d_h_i_s").".log" ;              
  }  
                
    if(@filesize($log_file) >= $log_max_size){
    @rename($log_file,$log_file_new);   
    }
    
      error_log($err, 3, $log_file);  
 }else{
      error_log($err);   
 } 
  
}
                  
//------- print block banners -----------//
  function print_block_banners($data_array,$pos="block"){
         global $data;
              foreach($data_array as $data){
              
                  
                  $ids[] = $data['id'] ;
                  
              if($pos=="block"){
                print "<tr>
                <td  width=\"100%\" valign=\"top\">";
              }
              
     if($data['c_type']=="code"){
    run_php($data['content']);
    }else{              
   $template = iif($pos=="center","center_banners","blocks_banners");
    run_template($template);
   }
    if($pos=="block"){  
                print "</td>
        </tr>";
    }
        }
        
        if(is_array($ids)){
        db_query("update movies_banners set views=views+1 where id IN (".implode(",",$ids).")");
        
        }        
       }
       
       
  //----- movie file icons ----//
  function get_movie_file_icons($id){
      global $phrases,$links,$player_data,$file_id;
      
  $players = get_players_data();  
           $file_id = db_qr_fetch("select id,url,url_watch,downloads,views from movies_files where cat='$id'");
          $url = iif($file_id['url_watch'],$file_id['url_watch'],$file_id['url']);    
          $player_data = get_file_player($url,$players);
          
          if(is_array($player_data)){
        run_template('movie_file_icons');
          
          }else{
              print "Error : no player";
          }
  }     
  //---- movie files list -----
  function get_movie_files_list($id,$limit=0){
      global $phrases,$links,$global_align_x,$settings,$style,$player_data,$data,$tr_color;



$qr = db_query("select * from movies_files where cat='$id' order by ord asc".iif($limit," limit $limit"));
$count_files = db_qr_fetch("select count(*) as count from movies_files where cat='$id'");
open_table("$phrases[the_files]");
if(db_num($qr)){
    
  $players = get_players_data();    
                       
print "<table width=100%>";
        while($data = db_fetch($qr)){
            
                $player_data = get_file_player(iif($data['url_watch'],$data['url_watch'],$data['url']),$players);
          
                
      if($tr_color=="row_1"){
        $tr_color = "row_2";  
      }else{
            $tr_color = "row_1";    
      }
      
       
       $data['name'] = trim($data['name']);
       if(!$data['name']){$data['name'] =   basename($data['url']);}
       
run_template('movie_files_list');
 
       }
print "</table>";

if($limit > 0 && $count_files['count'] > $limit){ 
print "<p align='$global_align_x'><a href=\"".str_replace("{id}",$id,$links['links_movie_files'])."\">$phrases[more]</a></p>";  
} 



}else{
 print "<center>$phrases[err_wrong_url]</center>" ;
        }
 
 
close_table();

  }
  
  
  //-------- movie subtitles list ----------//
  function get_movie_subtitles_list($id,$limit=0){
 
  global $phrases,$links,$global_align_x,$style,$data,$tr_color;   
    
  $id = (int) $id;
    
      open_table($phrases['subtitles']);
      
$qr = db_query("select * from movies_subtitles where cat='$id' order by ord asc".iif($limit," limit $limit"));
if(db_num($qr)){
print "<table width=100%>";
        while($data = db_fetch($qr)){
            
         if($tr_color=="row_1"){
        $tr_color = "row_2";  
      }else{
            $tr_color = "row_1";    
      }
      
      
run_template('movie_subtitles_list');

       }
print "</table>";

if($limit){ 
print "<p align='$global_align_x'><a href=\"".str_replace("{id}",$id,$links['links_movie_subtitles'])."\">$phrases[more]</a></p>";  
} 


}else{
 print "<center>$phrases[err_no_subtitles]</center>" ;
        }
close_table();
  }
  //----- rating stars --------
  function print_rating($type,$id,$rating=0,$readonly=false){
      global $style;
      
      print "
       <div id=\"rating_div\" dir=ltr></div> 
         <div id=\"rating_status_div\" dir=ltr></div>
         <div id='rating_loading_div' style=\"display:none;\"><img src='images/loading.gif'></div>";
        
      ?>     
    <script>
 jQuery('#rating_div').raty({
     start:     <?=$rating?>,
     showHalf:  true,
     readOnly: <?=iif($readonly,"true","false")?>,
     hintList:        ['1/5', '2/5', '3/5', '4/5', '5/5'],
     path: '<?=$style['images']?>/',
     onClick: function(score) {
    rating_send('<?=$type?>',<?=$id?>,score);
  }
 });   
 
</script>
<?
}

//---- time duration ----       
function time_duration($seconds, $use = null, $zeros = false)
{
    global $phrases;
    // Define time periods
    $periods = array (
        'years'     => 31556926,
        'Months'    => 2629743,
        'weeks'     => 604800,
        'days'      => 86400,
        'hours'     => 3600,
        'minutes'   => 60,
        'seconds'   => 1
        );
        
        $periods_names = array (
        'years'     => $phrases['year_ago'],
        'Months'    => $phrases['months_ago'],
        'weeks'     => $phrases['weeks_ago'],
        'days'      => $phrases['days_ago'],
        'hours'     => $phrases['hours_ago'],
        'minutes'   => $phrases['minutes_ago'],
        'seconds'   => $phrases['seconds_ago']
        );
        
        

    // Break into periods
    $seconds = (float) $seconds;
    $segments = array();
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[$period] = $count;
        $seconds = $seconds % $value;
    }

     if(count($segments)==0){$segments['seconds']=1;}
    // Build the string
    $string = array();
    
    foreach ($segments as $key => $value) {
        
  
    
        $segment = $value . ' ' . $periods_names[$key];
      
        $string[] = $segment;
        break;
    }

    return "$phrases[since] ".implode(', ', $string);  
}


//---- Js Redirect -----//
function js_redirect($url,$with_body=false){
    global $phrases;
    
if($with_body){
    print "<html>
    <body>";
}

print "<script>
var url = \"$url\";
 var a = document.createElement(\"a\");
 if(!a.click) { 
  window.location = url;
 }else{
 a.setAttribute(\"href\", url);
 a.style.display = \"none\";
 document.body.appendChild(a);
 a.click();
 }
 </script>
 
 <center> $phrases[redirection_msg] <a href=\"$url\">$phrases[click_here]</a></center>";
   
 }
 
  //-------------------------------------------- 
 function create_thumb($filename,$width=65,$height=65,$fixed=false,$suffix='',$replace_exists=false,$save_filename=''){
  require_once(CWD .'/includes/class_img_resize.php');

  if(function_exists("ImageCreateTrueColor") && file_exists(CWD . "/$filename")){

  
      
   if($fixed){$option = 'crop';}else{$option='auto';}
   
    $resizeObj = new resize(CWD . "/". $filename);

    // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
    $resizeObj -> resizeImage($width, $height, $option);


    
$imtype = file_extension(CWD . "/$filename");
if($save_filename){
 $save_name  =  $save_filename ;
 $save_path = str_replace("/".basename($filename),'',$filename);   
   
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

    // *** 3) Save image
  
    $resizeObj -> saveImage(CWD . "/" .$save_path."/".$save_name, 100);
    return ($save_path."/".$save_name) ; 
  }else{
      return false;
  }
 }
 //--------- Generate Random String -----------
function rand_string($length = 8){

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz";

  // set up a counter
  $i = 0;

  // add random characters to $password until $length is reached
  while ($i < $length) {

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) {
      $password .= $char;
      $i++;
    }

  }

  // done!
  return $password;

}



 
  //--------- load plugins function --------     
   function load_plugins($file){
       $dhx = @opendir(CWD ."/plugins");
while ($rdx = @readdir($dhx)){
         if($rdx != "." && $rdx != "..") {
                 $cur_fl = CWD ."/plugins/" . $rdx . "/".$file ;
        if(@file_exists($cur_fl)){
             $pl_files[] =     $cur_fl ; 
                }
          }

    }
@closedir($dhx);

return $pl_files;
   }
   
   
   
 //--------------- Load Global Plugins --------------------------
  $pls = load_plugins("global.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}

  