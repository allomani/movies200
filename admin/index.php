<?
define('ADMIN_DIR', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));  
chdir('./../');
define('CWD', (($getcwd = str_replace("\\","/",getcwd())) ? $getcwd : '.'));
define('IS_ADMIN', 1);
define('THIS_PAGE', "admin"); 


require(CWD . "/global.php") ;
require(CWD ."/includes/functions_admin.php");
  



//----------- Login Script ----------------------------------------------------------
if ($action == "login" && $username && $password ){

     $result=db_query("select * from movies_user where username='".db_escape($username,false)."'");
     if(db_num($result)){
     $login_data=db_fetch($result);

 
       if($login_data['password']==$password){
 access_log_record($login_data['username'],"Login Done");
 
set_cookie('admin_id', $login_data['id']);
set_cookie('admin_username', $login_data['username']);
set_cookie('admin_password', md5($login_data['password']));

     print "<SCRIPT>window.location=\"index.php\";</script>";
      exit();
       }else{
            access_log_record($login_data['username'],"Login Invalid Password"); 
              print "<link href=\"smiletag-admin.css\" type=text/css rel=stylesheet>\n";
              print "<br><center><table width=60% class=grid><tr><td align=center> $phrases[cp_invalid_pwd] </td></tr></table></center>";

              }
            }else{
                  access_log_record($username,"Login Invalid Username");        
                 print " <link href=\"smiletag-admin.css\" type=text/css rel=stylesheet>    \n";
                    print "<br><center><table width=60% class=grid><tr><td align=center>   $phrases[cp_invalid_username] </td></tr></table></center>";

                    }
              }elseif($action == "logout"){
                  
                
                    set_cookie('admin_id');
                    set_cookie('admin_username');
                    set_cookie('admin_password');
                    


                  print "<SCRIPT>window.location=\"index.php\";</script>";

                      }
 //-------------------------------------------------------------------------------------------
//--------- add Main user --------//
if($op=="add_main_user"){
$users_num = db_qr_fetch("select count(id) as count from movies_user");
if($users_num['count'] == 0 && trim($cp_username) && trim($cp_password)){
db_query("insert into movies_user (username,password,email,group_id) values('".db_escape($cp_username,false)."','".db_escape($cp_password,false)."','".db_escape($cp_email)."','1')");
}
}
//-------- First time setup ----------//
$users_num = db_qr_fetch("select count(id) as count from movies_user");
if($users_num['count'] == 0){

if($global_lang=="arabic"){
$global_dir = "rtl" ;
print "<html dir=$global_dir>
<title>$sitename - لوحة التحكم </title>" ;
}else{
$global_dir = "ltr" ;
print "<html dir=$global_dir>
<title>$sitename - Control Panel </title>" ;
}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
<link href=\"images/style.css\" type=text/css rel=stylesheet>
<script src='$scripturl/js/StrongPassword.js'></script>";

print "<center>
<form action='index.php' method=post>
<input type=hidden name=op value='add_main_user'>
<br><br><table width=50% class=grid>
<tr><td colspan=2><h2>$phrases[create_main_user]</h2></td></tr>
<tr><td>$phrases[username]</td><td><input type=text name=cp_username dir=ltr></td></tr>
<tr><td>$phrases[password]</td><td><input type=text name='cp_password' id='cp_password' dir=ltr onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"document.getElementById('cp_password').value=GenerateAndValidate(12,1);passwordStrength(document.getElementById('cp_password').value);\"></td></tr>
<tr><td>$phrases[email]</td><td><input type=text name=cp_email dir=ltr></td></tr>

<tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
<tr><td colspan=2 align=center><input type=submit value=' $phrases[add_button] '></td></tr>
</table>
</form></center>";
die();   
}                     
//-------------------------------------------------------------------------------------------

if (check_admin_login()) {
 //--------------------------- Backup Job ------------------------------
if($action=="backup_db_do"){
if(!$disable_backup){
if_admin();
require(CWD. '/includes/class_mysql_db_backup.php');
$backup_obj = new MySQL_DB_Backup();
$backup_obj->server = $db_host ;
$backup_obj->port = 3306;
$backup_obj->username = $db_username;
$backup_obj->password = $db_password;
$backup_obj->database = $db_name;
$backup_obj->drop_tables = true;
$backup_obj->create_tables = true;
$backup_obj->struct_only = false;
$backup_obj->locks = true;
$backup_obj->comments = true;
$backup_obj->fname_format = 'm-d-Y-h-i-s';
$backup_obj->null_values = array( '0000-00-00', '00:00:00', '0000-00-00 00:00:00');
$backup_obj->backup_dir = CWD . '/'.$settings['uploader_path'].'/backups/'; 

if($op=="local"){
$task = MSX_DOWNLOAD;
$filename = "movies_".date('m-d-Y_h-i-s').".sql.gz";
}elseif($op=="server"){
$task = MSX_SAVE ;
$filename = basename($filename);
}
$use_gzip = true;
$result_bk = $backup_obj->Execute($task, $filename, $use_gzip);
    if (!$result_bk)
        {
                 $output = $backup_obj->error;
        }
        else
        {
                $output = $phrases['backup_done_successfully'];

        }
        }else{
        $output =  $disable_backup ;
                }
}


//---------- load editor --------//
require (CWD."/".$editor_path."/editor_init_functions.php") ;
editor_init();


//----- load headers ------//
if($global_lang=="arabic"){
$global_dir = "rtl" ;
print "<html dir=$global_dir>
<title>$sitename - لوحة التحكم </title>" ;
}else{
$global_dir = "ltr" ;
print "<html dir=$global_dir>
<title>$sitename - Control Panel </title>" ;
}
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">

<link href=\"images/style.css\" type=text/css rel=stylesheet />
<script src='js.js' type=\"text/javascript\" language=\"javascript\"></script>

<script src='$scripturl/js/prototype.js'></script>
<script src='ajax.js'></script> 
<script src='$scripturl/js/scriptaculous/scriptaculous.js'></script>
<script src='$scripturl/js/StrongPassword.js'></script>";
editor_html_init();

if(file_exists(CWD . "/install/")){
print "<div style=\"border:1px solid;color: #D8000C;background-color: #FFBABA;padding:3px;text-align:center;margin:0;\">Installation folder exists at /install , Please delete it</div>";
}
        
if($license_properties['expire']['value'] && $license_properties['expire']['value'] != "0000-00-00"){
    $remaining_days = floor((strtotime($license_properties['expire']['value']) - time()) / (24*60*60));
    print "<div style=\"border:1px solid;color: #9F6000;background-color: #F9F0B5;padding:3px;text-align:center;margin:0;direction:ltr;\">The license will expire on : {$license_properties['expire']['value']} ($remaining_days days)</div>";
}

//---------- Reffer Check -----------------
if($action && $admin_referer_check){
    /*
$rf = str_replace(basename($_SERVER['HTTP_REFERER']),"",$_SERVER['HTTP_REFERER']);
$cr =  "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$cr2 = str_replace(basename($cr),"",$cr); 

print $_SERVER['HTTP_REFERER']."<br>";
print basename($_SERVER['HTTP_REFERER'])."<br>";
*/
 if(!strchr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])){
 //    print "<br>$rf<br>$cr2";
     print_admin_table("<b><font color=red>Wrong Reffer</font></b> <br><br> The Page : <br> <font color='#ACACAC'>".iif($_SERVER['HTTP_REFERER'],htmlspecialchars($_SERVER['HTTP_REFERER']),"Direct Link")."</font> <br><br> Trying to Access the page : <br><font color='#ACACAC'>".htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."</font> <br><br>
      <form action=\"$cr\" method=post onSubmit=\"return confirm('Are You Sure ?!');\">
     <input type=submit value=\"Click here to Continue\"></form>","90%");
     die();
 }
}
//--------------------------------------------


?>
<table width=100% height=100%><tr><td width=20% valign=top>

<?
print str_replace("{username}",$user_info['username'],$phrases['cp_welcome_msg']); 
print " <br><br>";

 require("admin_menu.php") ;
?>


</td>
 <td width=1 background='images/dot.gif'></td>
<td valign=top> <br>
<?
//----------------------Start -------------------------------------------------------
if(!$action){
require(CWD ."/counter.php"); 


   $cnt_users = db_qr_fetch("select count(*) as count from movies_user");
   $cnt_movies = db_qr_fetch("select count(*) as count from movies_data");
   $cnt_files = db_qr_fetch("select count(*) as count from movies_files");  
   $cnt_photos = db_qr_fetch("select count(*) as count from movies_photos"); 
   $cnt_actors = db_qr_fetch("select count(*) as count from movies_actors");   
  $cnt_actors_photos = db_qr_fetch("select count(*) as count from movies_actors_photos"); 
   
      
    $cnt_cats = db_qr_fetch("select count(*) as count from movies_cats");
    $cnt_members = db_qr_fetch("select count(*) as count from ".members_table_replace("movies_members"),MEMBER_SQL);

  $cnt_comments = db_qr_fetch("select count(*) as count from movies_comments");

  $cnt_reports = db_qr_fetch("select count(*) as count from movies_reports");

  
        
        

 print "<center><table width=50% class=grid><tr><td align=center><b>$phrases[welcome_to_cp] <br><br>";


   if($global_lang=="arabic"){
  print "مرخص لـ : $_SERVER[HTTP_HOST]" ;
  if(COPYRIGHTS_TXT_ADMIN){
      print "   من <a href='http://allomani.com/' target='_blank'>  اللوماني للخدمات البرمجية </a> " ;
      }

      print "<br><br>

   إصدار : ".SCRIPT_VER." <br><br>";
  }else{
  print "Licensed For : $_SERVER[SERVER_NAME]" ;
  if(COPYRIGHTS_TXT_ADMIN){
      print "   By  <a href='http://allomani.com/' target='_blank'>Allomani&trade;</a> " ;
      }

      print "<br><br>

   Version : ".SCRIPT_VER." <br><br>";
      }

 print " $phrases[cp_statics] : </b>
 
 <br> $phrases[cats_count] : $cnt_cats[count] 
 <br>  $phrases[movies_count]  : $cnt_movies[count]
 <br>  $phrases[files_count]  : $cnt_files[count] 
 <br>  $phrases[movies_photos]  : $cnt_photos[count]    <br>
 
  <br> $phrases[the_actors] : $cnt_actors[count] 
 <br>  $phrases[actors_photos]  : $cnt_actors_photos[count]  <br>
 
 
  <br> $phrases[members_count] : $cnt_members[count] 
  <br> $phrases[comments_count] : $cnt_comments[count] 
  <br> $phrases[reports_count] : $cnt_reports[count]   <br>
  
  
  
  <br>  $phrases[users_count] : $cnt_users[count] </font></td></tr></table></center>";

    print "<br><center><table width=50% class=grid><td align=center>";
    
    print "<span dir='$global_dir'><b>$phrases[php_version] : </b></span> <br><span dir='ltr'>".@phpversion()." </span><br><br> ";

      print "<b><span dir=$global_dir>$phrases[mysql_version] :</span> </b><br><span dir=ltr>" .@mysql_get_server_info() ."</span><br><br>";
  
   if(extension_loaded('ionCube Loader')){
   print "<b><span dir=$global_dir>$phrases[ioncube_version] :</span> </b><br><span dir=ltr>" . ioncube_loader_version()  ."</span><br><br>";
    }

   if(@function_exists("gd_info")){
   $gd_info = @gd_info();
   print "
   <b>  $phrases[gd_library] : </b> <font color=green> $phrases[cp_available] </font><br>
  <b>$phrases[the_version] : </b> <span dir=ltr>".$gd_info['GD Version'] ."</span>";
  }else{
  print "
  <b>  $phrases[gd_library] : </b> <font color=red> $phrases[cp_not_available] </font><br>
  $phrases[gd_install_required] ";
          }
          
   print "<br><br><b>Safe Mode : </b> ".iif(@ini_get('safe_mode'),
   "<font color=green>ON</font>",
   "<font color=red>OFF</font><br>".iif($global_lang=="arabic"," يوصى بتفعيل Safe Mode لضمان مستوى حماية افضل","it's recommended to enable SafeMode for better Security Level"));
   
   print "</td></tr></table>";

  print "<br><center><table width=50% class=grid><td align=center>
  <p><b> $phrases[cp_addons] </b></p>";

   //--------------- Check Installed Plugins --------------------------
$dhx = @opendir(CWD ."/plugins");
  $plgcnt = 0 ;
while ($rdx = @readdir($dhx)){
         if($rdx != "." && $rdx != "..") {
                 $cur_fl = CWD ."/plugins/" . $rdx . "/index.php" ;
        if(@file_exists($cur_fl)){
                print $rdx ."<br>" ;
                $plgcnt = 1 ;
                }
          }

    }
@closedir($dhx);
if(!$plgcnt){
    print "<center> $phrases[no_addons] </center>";
    }
 print "</td></tr></table>";
//--------------------------------------------------------------------


if($global_lang=="arabic"){
    print "<br><center><table width=50% class=grid><td align=center>
     يتصفح الموقع حاليا $counter[online_users] زائر
                                               <br><br>
   أكبر تواجد كان  $counter[best_visit] في : <br> $counter[best_visit_time] <br></td></tr></table>";
 }else{
         print "<br><center><table width=50% class=grid><td align=center>
     Now Browsing : $counter[online_users] Visitor
                                               <br><br>
   Best Visitors Count : $counter[best_visit] in : <br> $counter[best_visit_time] <br></td></tr></table>";

     }
     
 print "<br>";  
 $reports_cnt = db_qr_fetch("select count(*) as count from movies_reports where opened=0");  
 print_admin_table("$phrases[new_reports] : <a href='index.php?action=reports'>$reports_cnt[count]</a>");
 
 
  print "<br>";  
 $comments_cnt = db_qr_fetch("select count(*) as count from movies_comments where active=0");  
 print_admin_table("$phrases[comments_waiting_admin_review] : <a href='index.php?action=comments'>$comments_cnt[count]</a>");
 
 
   }


 //-------------- Blocks  ---------------
require(ADMIN_DIR ."/blocks.php");
 //-------------- Players  ---------------
require(ADMIN_DIR ."/players.php");
//-------- Phrases ------------
require(ADMIN_DIR ."/phrases.php");   
//---------- Pages -----------
require(ADMIN_DIR ."/pages.php");
//---------- News -----------
require(ADMIN_DIR ."/news.php");
//---------- Banners -----------
require(ADMIN_DIR ."/banners.php");
//---------- Templates -----------
require(ADMIN_DIR ."/templates.php");
//---------- Votes -----------
require(ADMIN_DIR ."/votes.php");
//---------- Movies -----------
require(ADMIN_DIR ."/movies.php");
//---------- Movie Files -----------
require(ADMIN_DIR ."/movie_files.php");
//---------- Movie Actors -----------
require(ADMIN_DIR ."/movie_actors.php");
//---------- Movie Photos -----------
require(ADMIN_DIR ."/movie_photos.php");
//---------- Movie Fields -----------
require(ADMIN_DIR ."/movies_fields.php");

//--------- Subtitles ------------
require(ADMIN_DIR ."/movie_subtitles.php");  
//---------- Actors -----------
require(ADMIN_DIR ."/actors.php");
//---------- Actors Photos-----------
require(ADMIN_DIR ."/actors_photos.php");

//---------- Members -----------
require(ADMIN_DIR ."/members.php");
//---------- Members Fields -----------
require(ADMIN_DIR ."/members_fields.php");
//---------- Members Remote DB -----------
require(ADMIN_DIR ."/members_remote_db.php");
//---------- New Menu -----------
require(ADMIN_DIR ."/new_menu.php");
//---------- Comments -----------
require(ADMIN_DIR ."/comments.php");
//---------- Reports -----------
require(ADMIN_DIR ."/reports.php");

//---------- Settings -----------
require(ADMIN_DIR ."/settings.php");
//---------- SEO -----------
require(ADMIN_DIR ."/seo.php");
//---------- hooks -----------
require(ADMIN_DIR ."/hooks.php");






 
//-------------------- Permisions------------------------
if($action=="permisions"){

    if_admin();
    $data =db_qr_fetch("select * from movies_user where id='$id'");     
    
    print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $phrases[permissions_manage]  / $data[username] <br><br>
    <form method=post action=index.php>
           <input type=hidden value='$id' name='user_id'>
               <input type=hidden value='permisions_edit' name='action'>";


         print "<center><span class=title>$phrases[permissions_manage]</span><br><br>
           <table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">

        <tr><td>
        <center><span class=title>$phrases[cats_permissions]</span> <br><br>
$phrases[cats_permissions_note]";
          
           print " </center></td></tr>
           </table><br>";

     //------------------------------------------------------------------------------

    

    


      print "<table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">
     <tr> <td colspan=5 align=center><span class=title>$phrases[cp_sections_permissions]</span></td></tr>
            <tr><td><table width=100%><tr>";

            $prms = explode(",",$data['cp_permisions']);
                      

  if(is_array($permissions_checks)){

  $c=0;
 for($i=0; $i < count($permissions_checks);$i++) {

        $keyvalue = current($permissions_checks);

if($c==4){
    print "</tr><tr>" ;
    $c=0;
    }

if(in_array($keyvalue,$prms)){$chk = "checked" ;}else{$chk = "" ;}

print "<td width=25%><input  name=\"cp_permisions[$i]\" type=\"checkbox\" value=\"$keyvalue\" $chk>".key($permissions_checks)."</td>";


$c++ ;

 next($permissions_checks);
}
  }
print "</tr></table></td>

            </tr></table>";

          print "<center> <br><input type=submit value='$phrases[edit]'></form>" ;

        }
//---------------------------- Users ------------------------------------------
if ($action == "users" or $action=="edituserok" or $action=="adduserok" or $action=="deluser" || $action=="permisions_edit"){


if($action=="permisions_edit"){

        if_admin();

$user_id = intval($user_id);

if($cp_permisions){
foreach ($cp_permisions as $value) {
       $perms .=  "$value," ;
     }
       }else{
               $perms = '' ;
               }

 db_query("update movies_user set cp_permisions='$perms' where id='$user_id'");
 

           }

        //---------------------------------------------
        if ($action=="deluser" && $id){
        if($user_info['groupid']==1 ){
db_query("delete from movies_user where id='$id'");
}else{
        print_admin_table("<center>$phrases[access_denied]</center>");
                          die();
        }
        }
        //---------------------------------------------
        if ($action == "adduserok"){
        if($user_info['groupid']==1){
                if(trim($username) && trim($password)){
                if(db_qr_num("select username from movies_user where username='".db_escape($username,false)."'")){
                        print "<center> $phrases[cp_err_username_exists] </center>";
                        }else{
        db_query("insert into movies_user (username,password,email,group_id) values ('".db_escape($username,false)."','".db_escape($password,false)."','".db_escape($email)."','".intval($group_id)."')");
        }
        }else{
                print "<center>  $phrases[cp_plz_enter_usr_pwd] </center>";
                }
                }else{
                          print_admin_table("<center>$phrases[access_denied]</center>");
                          die();
        }
        }
        //------------------------------------------------------------------------------
        if ($action == "edituserok"){
                if ($password){
                $ifeditpassword = ", password='".db_escape($password,false)."'" ;
                }

        if ($user_info['groupid'] == 1){
        db_query("update movies_user set username='".db_escape($username,false)."'  , email='".db_escape($email)."' ,group_id='".intval($group_id)."' $ifeditpassword where id='$id'");
        }else{
         if($user_info['id'] == $id){
        db_query("update movies_user set username='".db_escape($username,false)."'  , email='".db_escape($email)."'  $ifeditpassword where id='$id'");

                 }else{
                   print_admin_table("<center>$phrases[access_denied]</center>");
                   die(); 
                         }
                }
     
                print "<center>  $phrases[cp_edit_user_success]  </center>";
       
        }

if ($user_info['groupid'] == 1){
print "<img src='images/add.gif'><a href='index.php?action=useradd'>$phrases[cp_add_user]</a>";

//----------------------------------------------------
     print "<p align=center class=title>$phrases[the_users]</p>";
       $result=db_query("select * from movies_user order by id asc");


  print " <center> <table cellpadding=\"0\" border=0 cellspacing=\"0\" width=\"80%\" class=\"grid\">

        <tr>
             <td height=\"18\" width=\"134\" valign=\"top\" align=\"center\">$phrases[cp_username]</td>
                <td height=\"18\" width=\"240\" valign=\"top\">
                <p align=\"center\">$phrases[cp_email]</td>
                <td height=\"18\" width=\"105\" valign=\"top\">
                <p align=\"center\">$phrases[cp_user_group]</td>
                <td height=\"18\" width=\"193\" valign=\"top\" colspan=2>
                <p align=\"center\">$phrases[the_options]</td>
        </tr>";

      while($data = db_fetch($result)){


        if ($data['group_id']==1){$groupname="$phrases[cp_user_admin]";
             $permision_link="";
      }elseif($data['group_id']==2){$groupname="$phrases[cp_user_mod]";
       $permision_link="<a href='index.php?action=permisions&id=$data[id]'>$phrases[permissions_manage]</a>";

      }


        print "<tr>
                <td  width=\"134\" >
                <p align=\"center\">$data[username]</p></td>
                <td  width=\"240\" >
                <p align=\"center\">$data[email]</p></td>
                <td  width=\"105\"><p align=\"center\">$groupname</p></td>
                 <td  width=\"105\"><p align=\"center\">$permision_link</p></td>
                <td  width=\"193\"><p align=\"center\">
                 <a href='index.php?action=edituser&id=$data[id]'> $phrases[edit] </a> ";
        if ($data['id'] !="1"){
                print "- <a href='index.php?action=deluser&id=$data[id]' onClick=\"return confirm('".$phrases['are_you_sure']."');\"> $phrases[delete] </a>";
        }
                print " </p>
                </td>
        </tr>";
          }

print "</table></center>\n";




        }else{

                print "<br><center><table width=70% class=grid><tr><td align=center>
                $phrases[edit_personal_acc_only] <br>
                <a href='index.php?action=edituser'> $phrases[click_here_to_edit_ur_account] </a>
                </td></tr></table></center>";
        }
        }
//-------------------------Edit User------------------------------------------

if ($action=="edituser"){
       $id = intval($id);

if($user_info['groupid']!=1){
        $id=$user_info['id'];
}

$qr=db_query("select * from movies_user where id='$id'") ;
if (db_num($qr)){

$data = db_fetch($qr) ;

print "<img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $data[username] <br><br>


<center>
<FORM METHOD=\"post\" ACTION=\"index.php\">

 <TABLE width=70% class=grid>
    <TR>

    <INPUT TYPE=\"hidden\" NAME=\"id\" \" value=\"$data[id]\" >
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"edituserok\" >

   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_username] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\" value=\"$data[username]\" > </TD>
  </TR>
    <TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_password] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" name=\"password\" id='password' size=\"32\" onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"$('password').value=GenerateAndValidate(12,1);passwordStrength($('password').value);\">
    <br>* $phrases[leave_blank_for_no_change] </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_email] : </b></font> </TD>
   <TD width=\"614\"><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" value=\"$data[email]\" > </TD>
  </TR>\n";

  if($user_info['groupid'] != 1){
          print "<input type='hidden' name='group_id' value='2'>";
  }else {
   print "<TR>
   <TD width=\"100\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD width=\"614\">\n";


if ($data['group_id'] == 1){$ifselected1 = "selected" ; }else{$ifselected2 = "selected";}

print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' $ifselected1> $phrases[cp_user_admin] </option>
  <option value='2' $ifselected2>$phrases[cp_user_mod] </option>" ;


 print "  </select>";
  }

   print "</TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" width=\"685\">
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"usereditbutton\" VALUE=\"$phrases[edit]\"></TD>
  </TR>
 </TABLE>
</FORM>
</center>\n";


}else{
    print "<center> $phrases[err_wrong_url]</center>" ;
    }
}
//--------------------- Add User Form -------------------------------------------------------
if($action=="useradd"){
print "   <img src='images/arrw.gif'>&nbsp;<a href='index.php?action=users'>$phrases[the_users]</a> / $phrases[add_button] <br><br>

   <center>

<FORM METHOD=\"post\" ACTION=\"index.php\">

 <TABLE width=\"70%\" class=grid>
    <TR>
   <td colspan=2 align=center><span class=title> $phrases[cp_add_user] </span></td></tr>
   <tr>
<INPUT TYPE=\"hidden\" NAME=\"action\"  value=\"adduserok\" >

   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_username]: </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"username\" size=\"32\"  </TD>
  </TR>
    <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_password] : </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" name=\"password\" id='password' size=\"32\" onChange=\"passwordStrength(this.value);\" onkeyup=\"passwordStrength(this.value);\"> &nbsp; <input type=button value=\"Generate\" onClick=\"$('password').value=GenerateAndValidate(12,1);passwordStrength($('password').value);\"> </TD>
  </TR>
  <tr><td></td><td>
<div id=\"passwordDescription\">-</div>
<div id=\"passwordStrength\" class=\"strength0\"></div>
</td></tr>
   <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_email] : </b></font> </TD>
   <TD ><INPUT TYPE=\"text\" NAME=\"email\" size=\"32\" > </TD>
  </TR>

   <TR>
   <TD width=\"150\"><font color=\"#006699\"><b>$phrases[cp_user_group]: </b></font> </TD>
   <TD >\n";


print "  <p><select size=\"1\" name=group_id>\n
        <option value='1' > $phrases[cp_user_admin] </option>
  <option value='2' > $phrases[cp_user_mod]</option>" ;


 print "  </select>";


  print " </TD>
  </TR>


  <TR>
   <TD COLSPAN=\"2\" >
   <p align=\"center\"><INPUT TYPE=\"submit\" name=\"useraddbutton\" VALUE=\"$phrases[add_button]\"></TD>
  </TR>
 </TABLE>
</FORM>
</center><br><br>\n";
}

 
//------------------- DATABASE BACKUP --------------------------
if($action=="backup_db_do"){
    $output = htmlspecialchars($output) ;
print "<br><center> <table width=50% class=grid><tr><td align=center>  $output </td></tr></table>";
}

  if($action=="backup_db"){

   if_admin();
      print "<br><center>
      <p align=center class=title> $phrases[cp_db_backup] </p>

      <form action=index.php method=post>
      <input type=hidden name=action value='backup_db_do'>
      <table width=50% class=grid><tr><td>
      <input type=\"radio\" name=op value='local' checked onclick=\"document.getElementById('backup_server').style.display = 'none';\"> $phrases[db_backup_saveto_pc]
      <br><input type=\"radio\" name=op value='server' onclick=\"document.getElementById('backup_server').style.display = 'inline';\" > $phrases[db_backup_saveto_server]
      </td></tr>
      <tr><td>
      <div id=backup_server style=\"display: none; text-decoration: none\">
      <b> $phrases[the_file_path] : &nbsp; </b> <div dir=ltr>uploads/backups/<input type=text name=filename dir=ltr size=40 value='movies_".date("d-m-Y-h-i-s").".sql.gz'></div>
      </div>
     </td></tr><tr> <td align=center>
      <input type=submit value=' $phrases[cp_db_backup_do] '>
      </form></td></tr></table></center>";

          }
// ----------------- Repair Database -----------------------

if($action=="db_info"){

    if_admin();

if(!$disable_repair){
print "<script language=\"JavaScript\">\n";
print "function checkAll(form){\n";
print "  for (var i = 0; i < form.elements.length; i++){\n";
print "    eval(\"form.elements[\" + i + \"].checked = form.elements[0].checked\");\n";
print "  }\n";
print "}\n";
print "</script>\n";

        $tables = db_query("SHOW TABLE STATUS");
        print "<form name=\"form1\" method=\"post\" action=\"index.php\"/>
        <input type=hidden name=action value='repair_db_ok'>
        <center><table width=\"96%\"  class=grid>";
        print "<tr><td colspan=\"5\"> <font size=4><b>$phrases[the_database]</b></font> </td></tr>
        <tr><td>
        <input type=\"checkbox\" name=\"check_all\" checked=\"checked\" onClick=\"checkAll(this.form)\"/></td>
        ";
        print "<td><b>$phrases[the_table]</b></td><td><b>$phrases[the_size]</b></td>
        <td><b>$phrases[the_status]</b></td>
            </tr>";
        while($table = db_fetch($tables))
        {
            $size = round($table['Data_length']/1024, 2);
            $status = db_qr_fetch("ANALYZE TABLE `$table[Name]`");
            print "<tr>
            <td  width=\"5%\"><input type=\"checkbox\" name=\"check[]\" value=\"$table[Name]\" checked=\"checked\" /></td>
            <td width=\"50%\">$table[Name]</td>
            <td width=\"10%\" align=left dir=ltr>$size KB</td>
            <td>$status[Msg_text]</td>
            </tr>";
        }

        print "</table><br> <center><input type=\"submit\" name=\"submit\" value=\"$phrases[db_repair_tables_do]\" /></center> <br>
        </form>";
        }else{
              print_admin_table("<center> $disable_repair </center>") ;
            }
    }
//------------------------------------------------
    if($action=="repair_db_ok"){
       if_admin();

    if(!$disable_repair){
        if(!$check){
            print "<center><table width=50% class=grid><tr><td align=center> $phrases[please_select_tables_to_rapair] </td></tr></table></center>";
    }else{
        $tables = $_POST['check'];
        print "<center><table width=\"60%\"  class=grid>";

        foreach($tables as $table)
        {
            $query = db_query("REPAIR TABLE `". $table . "`");
            $que = db_fetch($query);
            print "<tr><td width=\"20%\">";
            print "$phrases[cp_repairing_table] " . $que['Table'] . " , <font color=green><b>$phrases[done]</b></font>";
            print "</td></tr>";
        }

        print "</table></center>";

        }

        }else{
              print_admin_table("<center> $disable_repair </center>") ;
            }
    }


//---------------------------------- Counters ---------------------
if($action=="counters"){
        if_admin();


  if(is_array($counters)){
  foreach($counters as $op){
 //---------------------
 if($op=="statics_rest"){
        db_query("delete from info_hits");
        db_query("update info_browser set count=0");
        db_query("update info_os set count=0");
        db_query("update info_best_visitors  set v_count=0");
                }
 //---------------------
  if($op=="movies_watch_rest"){
        db_query("update movies_files  set views=0");
                }
 //---------------------
  if($op=="movies_downloads_rest"){
        db_query("update movies_files  set downloads=0");
                }
 //---------------------
  if($op=="movies_votes_rest"){
         db_query("update movies_data  set votes=0");
         db_query("update movies_data  set votes_total=0");
                }
                
   if($op=="movies_actors_votes"){
         db_query("update movies_votes  set votes=0");
         db_query("update movies_votes  set votes_total=0");
                }
    
     if($op=="movies_photos_votes"){
         db_query("update movies_photos  set votes=0");
         db_query("update movies_photos  set votes_total=0");
                }
                            
     if($op=="actors_photos_votes"){
         db_query("update movies_actors_photos  set votes=0");
         db_query("update movies_actors_photos  set votes_total=0");
                }
                            
    if($op=="news_votes"){
         db_query("update movies_news  set votes=0");
         db_query("update movies_news  set votes_total=0");
                }
                
                
  if($op=="movies_views_rest"){
         db_query("update movies_data  set views=0");
     
                }
                
  if($op=="movies_photos_views_rest"){
         db_query("update movies_photos  set views=0");
     
                }
                
    if($op=="movies_actors_views"){
         db_query("update movies_actors  set views=0");
     
                }             
                
    if($op=="movies_actors_photos"){
         db_query("update movies_actors_photos  set views=0");
     
                }    
                         
      if($op=="news_views"){
         db_query("update movies_news  set views=0");
     
                }  
                           
                
 //---------------------
          }
          
          
 print_admin_table("<center> $phrases[done] </center>");
          }
$data_frstdate = db_qr_fetch("select * from info_hits order by date asc limit 1");
 if(!$data_frstdate['date']){$data_frstdate['date']= "$phrases[cp_not_available]"; }
 $qr_total=db_query("select hits from info_hits");
 $total_hits = 0 ;
 while($data_total = db_fetch($qr_total)){
 $total_hits += $data_total['hits'];
         }

print "<center><p class=title> $phrases[the_counters] </p>
<table width=50% class=grid>
<tr><td><b> $phrases[cp_counters_start_date] </b></td><td>$data_frstdate[date]
</td></tr>
<tr><td><b> $phrases[cp_total_visits] </b></td><td>$total_hits
</td></tr>
</table>
<br>
 <p class=title> $phrases[counters_reset] </p>
<form action='index.php' method=post onSubmit=\"return confirm('$phrases[are_you_sure]');\">
<input type=hidden name=action value='counters'>
<table width=50% class=grid><tr><td>
<input type='checkbox' value='statics_rest'  name='counters[]' >$phrases[cp_visitors_statics] <br><br>

<input type='checkbox' value='movies_watch_rest'  name='counters[]' >$phrases[movies_files_views]<br>
<input type='checkbox' value='movies_downloads_rest'  name='counters[]' >$phrases[movies_files_downloads]<br>
<input type='checkbox' value='movies_views_rest'  name='counters[]' >$phrases[movies_views]<br>
<input type='checkbox' value='movies_votes_rest'  name='counters[]' >$phrases[movies_votes]<br>
<input type='checkbox' value='movies_photos_views_rest'  name='counters[]' >$phrases[movies_photos_views]<br>
<input type='checkbox' value='movies_photos_votes'  name='counters[]' >$phrases[movies_photos_votes]<br><br>




<input type='checkbox' value='movies_actors_views'  name='counters[]' >$phrases[actors_views]<br>
<input type='checkbox' value='movies_actors_votes'  name='counters[]' >$phrases[actors_votes]<br>  
<input type='checkbox' value='movies_actors_photos'  name='counters[]' >$phrases[actors_photos_views]<br>
<input type='checkbox' value='actors_photos_votes'  name='counters[]' >$phrases[actors_photos_votes]<br><br>    


<input type='checkbox' value='news_views'  name='counters[]' >$phrases[news_views]<br>
<input type='checkbox' value='news_votes'  name='counters[]' >$phrases[news_votes]<br>






</td></tr><tr><td align=center>
<input type=submit value=' $phrases[cp_rest_counters_do] '>
</table></center>
</form>";
        }
        
 //-------------------- Access Log -------------
 if($action=="access_log"){
     if_admin();
     
     $qr=db_query("select * from movies_access_log order by id desc");
     print "<center>
     <p class=title>$phrases[access_log]</p>
     <table width=90% class=grid>";
       print "<tr><td><b>$phrases[username]</b></td><td><b>$phrases[the_date]</b></td><td><b>$phrases[the_status]</b></td><td><b>IP</td></tr>";   
     while($data = db_fetch($qr)){
         print "<tr><td>$data[username]</td><td>$data[date]</td><td>$data[status]</td><td>$data[ip]</td></tr>";
     }
     print "</table></center>";
 }


 
 //--------------- Load Admin Plugins --------------------------
$pls = load_plugins("admin.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}                    
//--------------------------------------------------


//-----------------------------------------------------------------------------

?>
</td></tr></table>
<?

}else{
if(!$disable_auto_admin_redirect){
if(strchr($_SERVER['HTTP_HOST'],"www.")){
  print "<SCRIPT>window.location=\"http://".str_replace("www.","",$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']."\";</script>";
  die();
  }
 }

if($global_lang=="arabic"){
print "<html dir=$global_dir>
<title>$sitename  - لوحة التحكم </title>";
}else{
    print "<html dir=$global_dir>
<title>$sitename  - Control Panel </title>";
    }
print "<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">";
print "<link href=\"images/style.css\" type=text/css rel=stylesheet>
<center>
<br>
<table width=60% class=grid><tr><td align=center>

<form action=\"index.php\" method=\"post\"\">
                 <table><tr><td><img src='images/users.gif'></td><td>

                <table dir=$global_dir cellpadding=\"0\" cellspacing=\"3\" border=\"0\">
                <tr>
                        <td class=\"smallfont\">$phrases[cp_username]</td>
                        <td><input type=\"text\" class=\"button\" name=\"username\"  size=\"10\" tabindex=\"1\" ></td>
                        <td class=\"smallfont\" colspan=\"2\" nowrap=\"nowrap\"></td>
                </tr>
                <tr>
                        <td class=\"smallfont\">$phrases[cp_password]</td>
                        <td><input type=\"password\"  name=\"password\" size=\"10\" tabindex=\"2\" /></td>
                        <td>
                        <input type=\"submit\" class=\"button\" value=\"$phrases[cp_login_do]\" tabindex=\"4\" accesskey=\"s\" /></td>
                </tr>

</td>
</tr>
                </table>
                <input type=\"hidden\" name=\"s\" value=\"\" />
                <input type=\"hidden\" name=\"action\" value=\"login\" />
                </td></tr></table>
                </form> </td></tr></table>
                </center>\n";


if(COPYRIGHTS_TXT_ADMIN_LOGIN){
if($global_lang=="arabic"){
    print "<br>
                <center>
<table width=60% class=grid><tr><td align=center>
  جميع حقوق البرمجة محفوظة <a href='http://allomani.com' target='_blank'> للوماني للخدمات البرمجية </a>  © 2011
</td></tr></table></center>";
}else{
print "<br>
                <center>
<table width=60% class=grid><tr><td align=center>
  Copyright © 2011 <a href='http://allomani.com' target='_blank'>Allomani&trade;</a>  - All Programming rights reserved
</td></tr></table></center>";
}
}

if(file_exists("demo_msg.php")){
include_once("demo_msg.php");
}
}