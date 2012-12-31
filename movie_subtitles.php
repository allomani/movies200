<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------
$qr = db_query("select id,cat,name from movies_data where id='$id'");
if(db_num($qr)){
    $data =db_fetch($qr);
    
    print_path_links($data['cat'],"<a href=\"".str_replace("{id}","$data[id]",$links['links_movie_info'])."\">$data[name]</a>" ." / $phrases[subtitles]");
 
   get_movie_subtitles_list($id);
  
}else{
    open_table();
    print "<center> $phrases[err_wrong_url] </center>";
    close_table();
}

//---------------------------------------------
require(CWD . "/includes/framework_end.php"); 
?>