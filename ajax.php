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
 
     include_once("global.php") ;
    header("Content-Type: text/html;charset=$settings[site_pages_encoding]");
    //------------------------------------------
    if($action=="check_register_username"){
        if(strlen($str) >= $settings['register_username_min_letters']){
            $exclude_list = explode(",",$settings['register_username_exclude_list']) ;

            if(!in_array($str,$exclude_list)){
                //$num = db_num(member_query("select","id",array("username"=>"='$str'")));
                $num = db_qr_num("select ".members_fields_replace("id")." from ".members_table_replace("movies_members")." where ".members_fields_replace("username")."='".db_escape($str)."'",MEMBER_SQL);

                if(!$num){
                    print "<img src='images/true.gif'>";
                }else{
                    print "<img src='images/false.gif' title=\"".str_replace("{username}",$str,"$phrases[register_user_exists]")."\">";
                }
            }else{
                print "<img src='images/false.gif' title=\"$phrases[err_username_not_allowed]\">";
            }
        }else{
            print "<img src='images/false.gif' title=\"$phrases[err_username_min_letters]\">";
        }
    }


    //------------------------------------------
    if($action=="check_register_email"){
        if(check_email_address($str)){
            $num = db_qr_num("select ".members_fields_replace("id")." from ".members_table_replace("moveis_members")." where ".members_fields_replace("email")."='".db_escape($str)."'",MEMBER_SQL);
            if(!$num){
                print "<img src='images/true.gif'>";
            }else{
                print "<img src='images/false.gif' title=\"$phrases[register_email_exists]\">";
            }
        }else{
            print "<img src='images/false.gif' title=\"$phrases[err_email_not_valid]\">";
        }
    }

    //-------- add to fav ------------
    if($action=="add_to_fav"){
        open_table($phrases['add2favorite']);
        if(check_member_login()){
            if($confirm){ 
                $qr = db_query("select id from movies_members_favorites where fid='$id'");
                if(db_num($qr)){
                    print "<center>  $phrases[add2fav_already_exists]  </center>";      
                }else{
                    db_query("insert into movies_members_favorites (uid,fid) values('$member_data[id]','$id')");
                    print "<center>  $phrases[add2fav_success]  </center>";
                }
            }else{  
                $data = db_qr_fetch("select name from movies_data where id='$id'");
                print "<center>".str_replace("{name}",$data['name'],$phrases['add2fav_confirm_msg']);
                print "<br><br><input type=button value='$phrases[yes]' onClick=\"add_to_fav_confirm($id);\"> &nbsp;&nbsp;<input type=button value='$phrases[no]' onClick=\"setContent.hide();\">
                </center> "; 
            }
        }else{
            print "<center>$phrases[please_login_first]</center>";
        }
        close_table();

    }

    //----- send to friend action --------
    if($action=="send2friend_submit"){ 

        if($name_from && $email_from && $email_to){

            if(check_email_address($email_from) && check_email_address($email_to)){


                if($settings['send_sec_code']){

                    require(CWD . '/includes/class_security_img.php');
                    $sec_img = new sec_img_verification();


                    if($sec_img->verify_string($sec_string)){
                        $security_code_check = 1;
                    }else{
                        $security_code_check = 0;
                    }
                }else{
                    $security_code_check = 1;
                }



                if($security_code_check){                           
                    $url = "$scripturl/".str_replace("{id}",$id,$links['links_movie_info'])  ;

                    $data = db_qr_fetch("select * from movies_data where id='$id'");
                    $file_title = "$data[name]" ;

                    $datax = get_template("friend_msg");


                    $thumb_url = iif(strchr($data['thumb'],"://"),$data['thumb'],$scripturl."/$data[thumb]");
                    $img_url =   iif(strchr($data['img'],"://"),$data['img'],$scripturl."/$data[img]");    


                    $msg = str_replace(
                        array("{name_from}","{email_from}","{email_to}","{url}","{name}","{details}","{thumb}","{img}"),
                        array($name_from,$email_from,$name_to,$url,$data['name'],$data['details'],$thumb_url,$img_url),$datax);




                    open_table();

                    $email_result = send_email($name_from,$mailing_email,$email_to,$phrases['send2friend_subject'],$msg);
                    if($email_result)  {
                        print "<center>  $phrases[send2friend_done] </center>";
                    }else{
                        print "<center> $phrases[send2friend_failed] </center>";
                    }
                    close_table();


                }else{
                    open_table();
                    print  "<center>$phrases[err_sec_code_not_valid]</center>";
                    close_table();     
                }


            }else{
                open_table();
                print "<center> $phrases[invalid_from_or_to_email] </center>";
                close_table();   
            }

        }else{
            open_table();
            print "<center> $phrases[please_fill_all_fields] </center>";
            close_table();
        }    
    }
    //----------- send2friend form -----------------
    if($action=="send2friend_form"){

        if($settings['send_sec_code']){      
            require(CWD . '/includes/class_security_img.php');
            $sec_img = new sec_img_verification();
        }


        $id= (int) $id;
        check_member_login();
        // print_r($member_data);
        open_table($phrases['send2friend']);

        print "
        <form name='submit_form' method=post action='ajax.php' id='submit_form'>
        <input type=hidden name='action' value='send2friend_submit'>
        <input type=hidden name=id value='$id'> ";

        print "<table>
        <tr><td >
        $phrases[your_name] : </td>
        <td><input type=text name=name_from id='name_from' value=\"$member_data[username]\"></td></tr>

        <tr><td>
        $phrases[your_email] : </td>
        <td><input type=text name=email_from dir=ltr id='email_from'  value=\"$member_data[email]\"></td></tr>

        <tr><td>
        $phrases[your_friend_email] : </td>
        <td colspan=2><input type=text name=email_to dir=ltr id='email_to'></td></tr>";  


        if($settings['send_sec_code']){
            print "<tr><td>$phrases[security_code] :</td>
            <td>".$sec_img->output_input_box('sec_string','size=7')."
            <img src=\"sec_image.php\" alt=\"$phrases[security_code]\" /></td></tr>";
        } 

        print "<tr><td colspan=2 align=center>
        <input type=button id='send_button' name='send_button' value='$phrases[send]' style=\"height:70;width:60;\" onClick=\"send_submit();\"></td></tr> 

        </table></form>";
        close_table();
    }


    //----------- get player -----------------
    if($action=="get_player"){
        $player_id= (int) $player_id;

        $data = db_qr_fetch("select name,url,url_watch,cat from movies_files where id='$id'");
        $data_cat = db_qr_fetch("select movies_cats.watch_for_members from movies_data,movies_cats where movies_cats.id=movies_data.cat and movies_data.id='$data[cat]'");


        ob_start();  

        if(!$dialog){  
            open_table();
            print "<a href='javascript:;' onClick=\"hide_player();\"><img src='images/close.gif' border=0 title=\"$phrases[close]\"></a><br>";
        }


        //-------------------------
        if($data_cat['watch_for_members']){
            if(check_member_login()){ 
                $continue = 1;
            }else{
                $continue = 0;
                print   "<center>$phrases[please_login_first]</center>";
            }
        }else{
            $continue= 1;
        }

        //---------------------

        if($continue){ 
            $player_data = get_player_by_id($player_id);    

            $url = iif($data['url_watch'],$data['url_watch'],$data['url']);   
            if(!strchr($url,"://")){
                $url = $scripturl . "/" . $url;
            }


            db_query("update movies_files set views=views+1 where id='$id'");   
            run_php(str_replace("{url}",$url,$player_data['int_content']));
        }


        if(!$dialog){
            close_table();
        }

        $returned_data = ob_get_contents();
        ob_end_clean();

        if(!$dialog){
            print $returned_data; 
        }else{

            if($data['name']){
                $filename = $data['name'];
            }else{
                $data_name = db_qr_fetch("select name from movies_data where id='$data[cat]'");   
                $filename =$data_name['name'];    
            }

            print json_encode(array("title"=>$filename,"content"=>$returned_data));
        }

    }

    //---------- movie files list -------------
    if($action=="get_movie_files_list"){
        get_movie_files_list($id);
    }


    //---------- movie subtitles list -------------
    if($action=="get_movie_subtitles_list"){
        get_movie_subtitles_list($id);
    }


    //---------------------  Comments ---------------------------

    if($action=="comments_add"){
        if(check_member_login()){

            if(in_array($type,$comments_types)){

                $content = trim($content);

                if($content){  


                    /*
                    $bad_words = explode(",",$settings['comments_bad_words']);
                    if(count($bad_words)){    
                    foreach($bad_words as $word){
                    $word=trim($word);
                    if($word){
                    $bad_words_str .= "\b".$word."\b|";
                    }
                    }
                    if($bad_words_str){
                    $bad_words_str = substr($bad_words_str,0,strlen($bad_words_str)-1); 
                    //$bad_words_str = "\bط­ظ…ط§ط±\b|\bظƒظ„ط¨\b|\bط³ظƒط³\b|\bfuck\b|\bsex\b";
                    $content = preg_replace("/$bad_words_str/u", $settings['comments_bad_words_replacement'],$content); 
                    }
                    }       */





                    db_query("insert into movies_comments (uid,fid,comment_type,content,time,active) values ('".intval($member_data['id'])."','".intval($id)."','".db_escape($type)."','".db_escape($content)."','".time()."','".iif($settings['comments_auto_activate'],1,0)."')");

                    $new_id = mysql_insert_id();

                    if($settings['comments_auto_activate']){
                        //  print $content;   
                        $data_member = db_qr_fetch("select ".members_fields_replace("id")." as uid,".members_fields_replace("username").",".members_fields_replace("gender").",".members_fields_replace("thumb")." from ".members_table_replace("movies_members")." where ".members_fields_replace("id")."='".intval($member_data['id'])."'");

                        $data = $data_member;
                        $data['id'] = $new_id;
                        $data['time'] = time()-1;
                        $data['content'] = htmlspecialchars($content);


                        $rcontent =  get_comment($data);   

                        print json_encode(array("status"=>1,"content"=>$rcontent));
                    }else{
                        print json_encode(array("status"=>1,"content"=>"","msg"=>"$phrases[comment_is_waiting_admin_review]")); 
                    }


                }else{
                    print json_encode(array("status"=>0,"msg"=>"$phrases[err_empty_comment]"));
                }
            }else{
                print json_encode(array("status"=>0,"msg"=>"$phrases[err_wrong_url]")); 
            }

        }else{
            print json_encode(array("status"=>0,"msg"=>"$phrases[please_login]"));

        }


    }

    //--------------------------
    if($action=="comments_delete"){

        check_member_login();
        db_query("delete from movies_comments where id='".intval($id)."'".iif(!check_admin_login()," and uid='".$member_data['id']."'"));    

    }

    //------------------------------


    if($action=="comments_get"){

        $offset = (int) $offset;
        if(!$offset){$offset=1;}
        $perpage =  intval($settings['commets_per_request']);
        if(!$perpage){$perpage=10;}
        $start = (($offset-1) * $perpage) ;


        $check_admin_login = check_admin_login();
        $check_member_login =  check_member_login();
        $members_cache = array(); 


        $qr = db_query("select * from movies_comments where fid='".db_escape($id)."' and comment_type like '".db_escape($type)."' and active=1 order by id desc limit $start,$perpage");

        /*
        $qr = db_query("select movies_comments.*,movies_members.id as member_id,
        movies_members.username,movies_members.thumb,movies_members.gender 
        from movies_comments,movies_members where movies_comments.fid='".db_escape($id)."' and
        movies_comments.comment_type like '".db_escape($type)."' and movies_comments.active=1 
        and movies_members.id=movies_comments.uid order by movies_comments.id desc limit $start,$perpage");  */

        if(db_num($qr)){
            // print $offset;
            if($offset = 1){
                print "<div id='no_comments'></div>";
            }



            $c = 0;
            while($data=db_fetch($qr)){                                                                    
                $data_arr[$c] = $data;

                if($members_cache[$data['uid']]['username']){
                    $udata = $members_cache[$data['uid']];
                }else{
                    $udata = db_qr_fetch("select ".members_fields_replace('username')." as username ,thumb,gender from ".members_table_replace('movies_members')." where ".members_fields_replace('id')."='$data[uid]'",MEMBER_SQL);
                    $members_cache[$data['uid']] =  $udata ;
                }                                             

                $data_arr[$c]['username'] = $udata['username'];
                $data_arr[$c]['gender'] = $udata['gender'];
                $data_arr[$c]['thumb'] = $udata['thumb'];


                $c++;
            }



            //--- first row id ----
            $first_index = count($data_arr)-1;
            $data_first_row = db_qr_fetch("select id from movies_comments where fid='".db_escape($id)."' and comment_type like '".db_escape($type)."' and active=1 order by id limit 1");
            if($data_arr[$first_index]['id'] != $data_first_row['id']){
                print " <div id='comments_older_div' class='older_comments_div'><a href='javascript:;' onClick=\"comments_get('".$type."','".$id."');\"><img src=\"$style[images]/older_comments.gif\">&nbsp; $phrases[older_comments]</a></div> ";
            }                                                        
            //---------------------



            unset($data);
            for($i=count($data_arr)-1;$i>=0;$i--){
                //    print $i;
                $data= $data_arr[$i];

                if($tr_class=="row_2"){
                    $tr_class="row_1";
                }else{
                    $tr_class="row_2";
                }

                print get_comment($data);
            }


        }else{
            if($offset == 1){ 
                print "<div id='no_comments'>$phrases[no_comments]</div>";
            }
        }



    }

    //----------  Report -------
    if($action=="report"){

        if($settings['report_sec_code']){      
            require(CWD . '/includes/class_security_img.php');
            $sec_img = new sec_img_verification();
        }


        $id=intval($id);
        if($settings['reports_enabled']){

            $member_login = check_member_login();  

            if(!$settings['reports_for_visitors'] &&  !$member_login){
                open_table();
                print "<center>$phrases[please_login_first]</center>";
                close_table();

            }else{


                open_table($phrases['report_do']);
                print "<form action='ajax.php' method='post' name='report_submit' id='report_submit'>
                <input type='hidden' name='action' value='report_submit'>
                <input type='hidden' name='id' value='$id'> 
                <input type='hidden' name='report_type' value=\"".htmlspecialchars($report_type)."\"> 


                <table width=100%>";

                if(!$member_login){
                    print "<tr><td>

                    <b>$phrases[your_name] </b> </td>
                    <td><input type=text name=name id='name' value=\"$member_data[username]\"></td>
                    </td>
                    <tr><td>
                    <b>$phrases[your_email]</b> </td>
                    <td><input type=text name=email dir=ltr id='email'  value=\"$member_data[email]\"></td></tr>";
                }



                print "
                <tr><td><b>$phrases[the_explanation]</b></td><td>
                <textarea cols=30 rows=5 name='content'></textarea></td></tr>";

                if($settings['report_sec_code']){
                    print "<tr><td><b>$phrases[security_code]</b></td>
                    <td>".$sec_img->output_input_box('sec_string','size=7')."
                    <img src=\"sec_image.php\" alt=\"$phrases[security_code]\" /></td></tr>";
                }


                print 
                "<tr><td colspan=2 align=center><input type=button id='send_button' name='send_button' value='$phrases[send]' style=\"height:70;width:60;\" onClick=\"report_send();\"></td>
                </tr></table>
                </form>";

                close_table();
            }
        }
    }

    //--- report submit ----//
    if($action=="report_submit"){
        $id= (int) $id;

        if($settings['reports_enabled']){ 

            $member_login = check_member_login();  

            if(!$settings['reports_for_visitors'] &&  !$member_login){
                open_table();
                print "<center>$phrases[please_login_first]</center>";
                close_table();

            }else{


                if(in_array($report_type,$reports_types)){       

                    if($settings['report_sec_code']){

                        require(CWD . '/includes/class_security_img.php');
                        $sec_img = new sec_img_verification();


                        if($sec_img->verify_string($sec_string)){
                            $security_code_check = 1;
                        }else{
                            $security_code_check = 0;
                        }
                    }else{
                        $security_code_check = 1;
                    }





                    if($security_code_check){       
                        if($member_login){
                            $uid = $member_data['id'];
                            $name = $member_data['username'];
                            $email = $member_data['email']; 
                        }else{
                            $uid = 0 ;  
                        }


                        db_query("insert into movies_reports(fid,uid,name,email,content,date,report_type) values ('$id','$uid','".db_escape($name)."','".db_escape($email)."','".db_escape($content)."','".time()."','".db_escape($report_type)."')");

                        open_table();
                        print "<center>  $phrases[report_sent] </center>";
                        close_table();


                    }else{
                        open_table();
                        print  "<center>$phrases[err_sec_code_not_valid]</center>";
                        close_table(); 

                    }

                }else{     
                    open_table();
                    print "<center>  $phrases[err_wrong_url] </center>";
                    close_table();


                }
            }
        }  
    }



    //-------- Rating ---------------
    if($action=="rating_send"){

        $id = (int) $id;
        $score = (int) $score;

        $rating_types = array('movie','movie_photo','actor','actor_photo','news');


        if(in_array($type,$rating_types)){

            if($score > 0){
                $cookie_name = 'rating_'.$type.'_'.$id;
                $settings['rating_expire_hours'] = intval($settings['rating_expire_hours']);
                $settings['rating_expire_hours'] = iif($settings['rating_expire_hours'],$settings['rating_expire_hours'],1);

                if(get_cookie($cookie_name)){
                    print "<center>".str_replace('{hours}',$settings['rating_expire_hours'],$phrases['rating_expire_msg'])."</center>" ;                
                }else{

                    if($type=='movie'){
                        db_query("update movies_data set votes=votes+$score , votes_total=votes_total+1 where id='$id'");  
                    }elseif($type=='movie_photo'){
                        db_query("update movies_photos set votes=votes+$score , votes_total=votes_total+1 where id='$id'"); 
                    }elseif($type=='actor_photo'){
                        db_query("update movies_actors_photos set votes=votes+$score , votes_total=votes_total+1 where id='$id'"); 
                    }elseif($type=='actor'){
                        db_query("update movies_actors set votes=votes+$score , votes_total=votes_total+1 where id='$id'"); 
                    }elseif($type=='news'){
                        db_query("update movies_news set votes=votes+$score , votes_total=votes_total+1 where id='$id'"); 
                    }


                    set_cookie($cookie_name,1,time()+(60*60*$settings['rating_expire_hours']));    
                    print "$phrases[rating_done]"; 

                }

            }else{
                print "Wrong Rating Value !";
            }       
        }else{
            print "Wrong Reference !";    
        }


    }


?>
