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
 
 header('Content-type: text/xml');
include "global.php" ;
print "<?xml version=\"1.0\" encoding=\"$settings[site_pages_encoding]\" ?> \n";
?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" 
                   xmlns:av="http://www.searchvideo.com/schemas/av/1.0">

<channel>
<? print "<title><![CDATA[$sitename]]></title>\n
<description><![CDATA[$settings[header_description]]]></description>";?> 
<?print "<link>http://".$_SERVER['HTTP_HOST']."</link>\n";
print "<copyright><![CDATA[$settings[copyrights_sitename]]]></copyright>";


$qr=db_query("select movies_data.*,movies_cats.name as cat_name from movies_data,movies_cats where movies_cats.id=movies_data.cat order by id desc limit 200") ; 
 

while($data = db_fetch($qr)){

   print "  <item>
        <title><![CDATA[".$data["name"]."]]></title>
        <description><![CDATA[
        <img src=\"$scripturl/".get_image($data['thumb'])."\">]]></description>"; 
                print "
        <link>".htmlentities($scripturl."/".str_replace("{id}",$data['id'],$links['links_movie_info']))."</link>
        <pubDate>".date("d M Y h:s:i",$data['date'])."</pubDate>
       <category><![CDATA[$data[cat_name]]]></category>
     </item>\n";
     }

	
print "</channel>
</rss>";
