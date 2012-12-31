<?
require("global.php");

require(CWD . "/includes/framework_start.php");   
//---------------------------------------------------------


$year = intval($year);
$month = intval($month);
$ph_skin = 1;

require(CWD . '/includes/functions_statics.php');
require CWD . "/chart.php";






 //-------- browser and os statics ---------
if($settings['count_visitors_info']){
open_table("$phrases[operating_systems]");


get_statics_info("select * from info_os where count > 0 order by count DESC","name","count",2);
close_table();

open_table("$phrases[the_browsers]");
get_statics_info("select * from info_browser where count > 0 order by count DESC","name","count",2);
close_table();

$printed  = 1 ;
}

//--------- hits statics ----------
if($settings['count_visitors_hits']){
$printed  = 1 ;

if (!$year){$year = date("Y");}

$PG = new PowerGraphic;

$PG->type      = 2;
$PG->skin      = $ph_skin;
$PG->credits   = 0;


$PG->axis_x    = "Month";
$PG->axis_y    = ' Hits';


open_table("$phrases[monthly_statics_for] $year ");


 $ixx =0 ;

for ($i=1;$i <= 12;$i++){

$dot = $year;

if($i < 10){$x="0$i";}else{$x=$i;}


$sql = "select sum(hits) as sum from info_hits where date like '%-$x-$dot' order by date" ;
$data_stat=db_qr_fetch($sql);
$PG->x[$ixx] = date('M',mktime(0,0,0,$x));
$PG->y[$ixx] = intval($data_stat['sum']);

$ixx++;

 }
 
  
   
 
   print "<center><img src=\"chart.php?" . $PG->create_query_string() . "\" border=\"1\" alt=\"\" /></center>";
 
  

 
  print "<br><center>[ $phrases[the_year] : ";
  $yl = date('Y') - 3 ;
  while($yl != date('Y')+1){
      print "<a href='statistics.php?year=$yl'>$yl</a> ";
      $yl++;
      }
  print "]";
close_table();

if (!$month){
        $month =  date("m")."-$year" ;
        }else{
                $month= "$month-$year";
                }

open_table("$phrases[daily_statics_for] $month ");
$dot = $month;
get_statics_info("select * from info_hits where date like '%$dot' order by date","date","hits");

print "<br><center>
          [ $phrases[the_month] :
          <a href='statistics.php?year=$year&month=1'>1</a> -
          <a href='statistics.php?year=$year&month=2'>2</a> -
          <a href='statistics.php?year=$year&month=3'>3</a> -
          <a href='statistics.php?year=$year&month=4'>4</a> -
          <a href='statistics.php?year=$year&month=5'>5</a> -
          <a href='statistics.php?year=$year&month=6'>6</a> -
          <a href='statistics.php?year=$year&month=7'>7</a> -
          <a href='statistics.php?year=$year&month=8'>8</a> -
          <a href='statistics.php?year=$year&month=9'>9</a> -
          <a href='statistics.php?year=$year&month=10'>10</a> -
          <a href='statistics.php?year=$year&month=11'>11</a> -
          <a href='statistics.php?year=$year&month=12'>12</a>
          ]";
          close_table();
}

if(!$printed){
    open_table();
   print "<center>$phrases[no_results]</center>";
    close_table();
    }

    
//---------------------------------------------
require(CWD . "/includes/framework_end.php");  