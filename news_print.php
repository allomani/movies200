<?
include "global.php";

print "<HTML dir=\"$settings[html_dir]\">
<head>
<META http-equiv=Content-Language content=\"$settings[site_pages_lang]\">
<META http-equiv=Content-Type content=\"text/html; charset=$settings[site_pages_encoding]\">
</head>";


   $id = intval($id);

       $qr = db_query("select * from movies_news where id='$id'");
              if(db_num($qr)){
              $data = db_fetch($qr);
   print "<title>$data[title]</title>";



   $img_url = get_image($data['img']) ;
   $template = get_template('browse_news_print');
   $news_date = date($settings['date_format'],$data['date']);
   $template = str_replace(array('{id}','{title}','{img}','{content}','{details}','{writer}','{date}'),array("$data[id]","$data[title]","$img_url","$data[content]","$data[details]","$data[writer]","$news_date"),$template);

       print "$template";



     }else{

     print "<center>$phrases[err_wrong_url]</center>";

             }

 print "</HTML>";            
