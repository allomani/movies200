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
 
 include "global.php" ;
$qr = db_query("select url from movies_banners where id='$id'");
if(db_num($qr)){
$data = db_fetch($qr);
db_query("update movies_banners set clicks=clicks+1 where id='$id'");

header("Location: $data[url]");
}else{
 header("Location: index.php");
 }
?>
