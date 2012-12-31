<?


function get_statics_info($sql,$count_name,$count_data,$type=2){

global $if_img,$year,$global_align,$style,$PG,$ph_skin,$phrases ;







 $qr_stat=db_query($sql);
if (db_num($qr_stat)){
    
 $PG = new PowerGraphic;

$PG->type      = $type;
$PG->skin      = $ph_skin;
$PG->credits   = 0;



$PG->axis_x    = $count_name;
$PG->axis_y    = ' Hits';


    $ixx = 0;
while($data_stat=db_fetch($qr_stat)){
    
$PG->x[$ixx] = $data_stat[$count_name];
$PG->y[$ixx] = $data_stat[$count_data];
$ixx++;


$total = $total + $data_stat[$count_data];
}

print "<center><img src=\"chart.php?" . $PG->create_query_string() . "\" border=\"1\" alt=\"\" /></center>";

       
}else{
        print "<center>$phrases[no_results]</center>" ;
        }

}
?>