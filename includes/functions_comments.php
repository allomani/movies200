<?
function get_comments_box($type='',$id){
            global $phrases,$settings,$global_align_x,$global_align;
            
   print "
    <div id='comments_loading_div' style=\"display:none;\"><img src=\"images/loading.gif\"></div>
    
    <div id='comments_div'>
    <div id='comments_older_div'>
    </div>
    </div>";
   
 //  if(check_member_login()){
 ?>
 <script>
 function CheckFieldLength() {
 var mc = <?=$settings['comments_max_letters']?>;
  var len = $('comment_content').value.length;
                                        
  if (len > mc) {
  
    $('comment_content').value = $('comment_content').value.substring(0,mc);
    len = mc;
  }
  $('remaining_letters').innerHTML = mc - len;
}

function textarea_focus(){
   // if($('comment_content').rows ==1){
      $('comment_controls').style.display = "inline"; 
      $('content_mask').style.display = "none"; 
      $('comment_content').style.display = "inline"; 
      $('comment_content').focus();
  //  }
}

function textarea_blur(){
    if($('comment_content').value.length == 0){
    $('comment_controls').style.display = "none"; 
      $('content_mask').style.display = "inline"; 
      $('comment_content').style.display = "none"; 
    }
}


</script>
<? 
    print "  <br>
    <form name='send_comment'>
    <table><tr><td colspan='2'>
    <input type='text' id='content_mask' style=\"color:#ACACAC;\" size='40' value=\"$phrases[write_your_comment] ...\" onfocus=\"textarea_focus();\">
    <textarea cols=30 rows=3 style=\"display:none;\" dir='$global_align' id='comment_content' name='comment_content' onblur=\"textarea_blur();\" onkeyup=\"CheckFieldLength();\" onkeydown=\"CheckFieldLength();\"></textarea>
  </tr>
    <tr id='comment_controls' style=\"display:none;\">
    <td align='$global_align' width=210>
      <div><span id='remaining_letters'>$settings[comments_max_letters]</span> $phrases[remaining_letters]</div>
      </td>
      <td align='$global_align_x'><input type=button id='comment_add_button' value=' $phrases[add] ' onClick=\"comments_add('".$type."','".$id."');\" style=\"width:60;height:25;\">  
   </td>
   <tr><td colspan=2>
   <div id='comment_status'></div></td> 
    </tr></table>
    </form> ";
 //  }else{
    //   print "يرجى تسجيل الدخول لاضافة تعليق";
 //  }
   
   
    
    
    print "
    <script language=\"javascript\">
    comments_get('".$type."','".$id."');
    </script>";
}


function get_comment($data){
    global $tr_class,$data,$links,$settings,$phrases,$member_data,$check_admin_login,$style;
    
     $c = "<table width=100%>";         
        $c .= "<tr class=\"$tr_class\" id='comment_{$data['id']}'>";
        
        
        if($settings['members_profile_pictures']){
     $c .= "<td><a href=\"".str_replace("{id}",$data['uid'],$links['links_profile'])."\" title=\"$data[username]\"><img src=\"".get_image($data['thumb'],$style['images']."/profile_no_pic_thumb".iif($data['gender'],"_".$data['gender']).".gif")."\" title=\"$data[username]\" border=0 width='$settings[profile_pic_thumb_width]' height='$settings[profile_pic_thumb_height]'></a></td>";
        }
        
        
        $c .= "
        <td width='75%'><a href=\"".str_replace("{id}",$data['uid'],$links['links_profile'])."\" title=\"$data[username]\"><b>$data[username]</b></a>  : $data[content]  
       ".iif($check_admin_login || $data['uid'] == $member_data['id'],"&nbsp; &nbsp; &nbsp;[<a href=\"javascript:;\" onClick=\"if(confirm('$phrases[are_you_sure]')){comments_delete($data[id]);}\">$phrases[delete]</a>]")."
       </td><td width='25%'>
         ".time_duration((time()-$data['time']))." </td>
         ".iif($settings['reports_enabled'],"<td><a href=\"javascript:;\" onClick=\"report($data[id],'comment');\"><img src=\"$style[images]/report.gif\" title=\"$phrases[report_do]\" border=0></a></td>")."</tr>";
          $c .= "</table>"; 
          
        return $c;   
} 
?>