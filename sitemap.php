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
 
 require("global.php");
print "<?xml version=\"1.0\" encoding=\"$settings[site_pages_encoding]\" ?> \n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?


//---------- cats -------------
$qr=db_query("select id from movies_cats order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['links_cats']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}
//---------- Movies -------------
$qr=db_query("select id from movies_data order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['links_movie_info']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}

//---------- Movies Photos-------------
$qr=db_query("select id from movies_photos order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['links_movie_photo']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}




//---------- Actors  -------------
$qr=db_query("select id from movies_actors order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['links_actor_details']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}


//---------- Actors photos -------------
$qr=db_query("select id from movies_actors_photos order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['links_actor_photo']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}



//---------- Movies Files -------------
$qr=db_query("select id from movies_files order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['file_watch']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}




//---------- News cats  -------------
$qr=db_query("select * from  movies_news_cats order by ord asc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{cat}",$data['id'],$links['links_browse_news']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}

    
//---------- News  -------------
$qr=db_query("select id from movies_news order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['news_details']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}


//---------- Pages  -------------
$qr=db_query("select id from movies_pages order by id desc");
while($data = db_fetch($qr)){
print "<url>
<loc>".htmlspecialchars("$scripturl/".str_replace("{id}",$data['id'],$links['links_pages']))."</loc>
<changefreq>daily</changefreq>
<priority>0.50</priority>
</url>";    
}







print "</urlset>";

