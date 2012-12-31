<?
//----------- Database Settings --------------
$db_host = "localhost" ;
$db_name = "movies200" ;
$db_username = "root" ;
$db_password = "" ;
$db_charset = "utf8";


//---------- Script Settings ---------- 

$blocks_width = "17%" ;

$editor_path  = "ckeditor";       // no_editor : to remove editor 

$global_lang = "arabic" ;

$copyrights_lang = "arabic";

$preview_text_limit = 300 ;   //letters

$online_visitor_timeout = 800; // in seconds 

$use_editor_for_pages = 1 ;  // 1 enable - 0 disable
                       
$access_log_expire=90 ; // days
 

$admin_referer_check = true;


//$disable_backup = "عفوا , هذه الخاصية غير مفعلة في النسخة التجريبية" ;
//$disable_repair = "عفوا , هذه الخاصية غير مفعلة في النسخة التجريبية" ;
//----------- Error Handling  ---------
$custom_error_handler = false;

$display_errors = false;
$log_errors  = true;
 
 
$show_mysql_errors = false ;
$log_mysql_errors = false;


$logs_path = "uploads/logs";
$log_max_size = 1024*1024;

$debug =false;

//---------- to use remote members database ----------
$members_connector['enable'] = 0;
$members_connector['db_host'] = "localhost";
$members_connector['db_name'] = "forum";
$members_connector['db_username'] = "root";
$members_connector['db_password'] = "";
$members_connector['db_charset'] = "utf8";  
$members_connector['custom_members_table'] = "";
$members_connector['connector_file'] = "vbulliten.php";

//--------------- to use SMTP Server ---------
$smtp_settings['enable'] = 0;
$smtp_settings['host_name']="mail.allomani.com";
$smtp_settings['host_port']= 25;
$smtp_settings['ssl']=0;
$smtp_settings['username'] = "info@allomani.com";
$smtp_settings['password'] = "password_here";
$smtp_settings['timeout'] = 10;
$smtp_settings['debug'] = 0;
$smtp_settings['show_errors'] = 0;


//-------- Cookies Settings  -----------
$cookies_prefix = "movies_";
$cookies_timemout = 365 ; //days
$cookies_path = "/" ;
$cookies_domain = "";


?>