function banner_pop_open(url,name){
msgwindow=window.open(url,name,"toolbar=yes,scrollbars=yes,resizable=yes,width=650,height=300,top=200,left=200");
}

function banner_pop_close(url,name){
msgwindow=window.open(url,name,"toolbar=yes,scrollbars=yes,resizable=yes,width=650,height=300,top=200,left=200");
}




function hide_div(name)
{
$(name).style.display = "none";
}


function enlarge_pic(sPicURL,title) { 
msgwindow=window.open(scripturl+"/enlarge_pic.php?url="+sPicURL+"&title="+title, "","resizable=1,scrollbars=1,HEIGHT=10,WIDTH=10"); 
} 


/* ---------------- AJAX --------------- */

function show_player(file_id,player_id,dialog)
{
 if(dialog === undefined){dialog = 0 ;}
 
if(dialog == 2){return true;}

if(dialog == 3){
window.open(scripturl+"/watch.php?id="+file_id, "","resizable=1,scrollbars=1,HEIGHT=400,WIDTH=600");
return false;   
    
}


if(dialog == 0){
$('player_loading_div').style.display = "inline";
}

var url="ajax.php";
url=url+"?action=get_player&id="+file_id+"&player_id="+player_id+"&dialog="+dialog;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){

if(dialog==0){
$('player_loading_div').style.display = "none";   
$('player_div').innerHTML=t.responseText;
$('player_div').style.display = "inline";
window.scroll(0,$('player_div').offsetTop+100);
}else{
var json = t.responseText.evalJSON();
setContent = new Boxy("<div>"+json.content+"</div>"
 ,{modal:true,title:json.title,unloadOnHide: true }); 
}
 
}
 }); 

 return false;
}

function hide_player()
{
$('player_div').style.display = "none";
$('player_div').innerHTML = "";
}

function report(id,report_type){

var url="ajax.php?sid="+Math.random();

new Ajax.Request(url, {   
method: 'post',
parameters : {action:'report',id: id,report_type: report_type},   
onSuccess: function(t){

setContent = new Boxy("<div>"+t.responseText+"</div>"
 ,{modal:true,title:"<br>",unloadOnHide: true }); 
 
 
}
});
}


function report_send(){

$('send_button').disabled=true;


$('report_submit').request({
  onSuccess: function(t){ 
  setContent.setContent("<div>"+t.responseText+"</div>");   
  }
});

}



/*
function send(id)
{
$('snd2friend_loading_div').style.display = "inline";

var url="ajax.php";
url=url+"?action=send2friend_form&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('snd2friend_loading_div').style.display = "none";   
$('snd2friend_div').innerHTML=t.responseText;
$('snd2friend_div').style.display = "inline";
window.scroll(0,$('snd2friend_div').offsetTop+100); 
}
 }); 

} */   

function add_to_fav(id){
 
var url="ajax.php";
url=url+"?action=add_to_fav&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){

 
setContent = new Boxy("<div>"+t.responseText+"</div>"
 ,{modal:true,title:"<br>",unloadOnHide: true});           
    
}
 }); 

}


function add_to_fav_confirm(id){
setContent.hide();

var url="ajax.php";
url=url+"?action=add_to_fav&confirm=1&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){

 
setContent = new Boxy("<div>"+t.responseText+"</div>"
 ,{modal:true,title:"<br>",unloadOnHide: true});           
    
}
 }); 
}


function send(id){
    
 
var url="ajax.php";
url=url+"?action=send2friend_form&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
 
setContent = new Boxy("<div>"+t.responseText+"</div>"
 ,{modal:true,title:"<br>",unloadOnHide: true});  
 //setContent.centerAt(600 ,342);  
   

  
}
 }); 

} 
 

 
function send_submit(){

if($('name_from').value != "" && $('name_from').value != ""  && $('email_to').value != "" ){  
$('send_button').disabled=true;


$('submit_form').request({
  onSuccess: function(t){ 
  setContent.setContent("<div>"+t.responseText+"</div>");   
  //$('snd2friend_div').innerHTML= t.responseText; 
  }
});
}else{
alert('Please Fill All Fields');
}
return false;
}   


function show_files_list(id)
{
$('files_list_loading_div').style.display = "inline";

var url="ajax.php";
url=url+"?action=get_movie_files_list&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('files_list_loading_div').style.display = "none";   
$('files_list_div').innerHTML=t.responseText;
window.scroll(0,$('files_list_div').offsetTop+100);  
}
 }); 

}


function show_subtitles_list(id)
{
$('subtitles_list_loading_div').style.display = "inline";

var url="ajax.php";
url=url+"?action=get_movie_subtitles_list&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){
$('subtitles_list_loading_div').style.display = "none";   
$('subtitles_list_div').innerHTML=t.responseText;
window.scroll(0,$('subtitles_list_div').offsetTop+100);
}
 }); 

}



function ajax_check_register_username(str)
{
var url="ajax.php";
url=url+"?action=check_register_username&str="+str;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){ $('register_username_area').innerHTML=t.responseText;}
 }); 

}

function ajax_check_register_email(str)
{
var url="ajax.php";
url=url+"?action=check_register_email&str="+str;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){$('register_email_area').innerHTML=t.responseText;}
 }); 

}





//---------- Comments Functions -------------------------------

function comments_add(type,id){
$('comment_add_button').disabled = true;
$('comment_content').disabled = true; 

var url="ajax.php?sid="+Math.random();

new Ajax.Request(url, {   
method: 'post',
parameters : {action:'comments_add',type: type,id: id,content: $('comment_content').value},   
onSuccess: function(t){

//$('comment_status').innerHTML = t.responseText; 
$('comment_add_button').disabled = false;
$('comment_content').disabled = false; 

 // alert(t.responseText);
          
                
var json = t.responseText.evalJSON();

if(json.status == 1){
$('comment_content').value = ''; 
$('comment_content').focus();
$('no_comments').innerHTML = "";

if(json.content == ""){
//$('comment_status').innerHTML = json.msg;
alert(json.msg); 
}else{
$('comments_div').innerHTML = $('comments_div').innerHTML + json.content;
}
}else{
alert(json.msg);
//$('comment_status').innerHTML = json.msg;
}

}
 }); 
 
   
}


function comments_delete(id){
    
var url="ajax.php";
url=url+"?action=comments_delete&id="+id;
url=url+"&sid="+Math.random();

new Ajax.Request(url, {   
method: 'get',   
onSuccess: function(t){

 $('comment_'+id).style.display="none";     

}
 });  
 
 
}
    
var comments_offset = 1;

function comments_get(type,id){
 $('comments_loading_div').style.display = "inline"; 
 $('comments_older_div').style.display = "none";    

var url="ajax.php?sid="+Math.random();

new Ajax.Request(url, {   
method: 'post',
parameters : {action:'comments_get',type: type,id: id,offset: comments_offset},      
onSuccess: function(t){
$('comments_div').innerHTML = t.responseText + $('comments_div').innerHTML; 
 $('comments_loading_div').style.display = "none"; 
 comments_offset++;   


}
 }); 
}


function rating_send(type,id,score){ 

$('rating_loading_div').style.display = "inline"; 
$('rating_status_div').style.display = "none";    

var url="ajax.php?sid="+Math.random();

new Ajax.Request(url, {   
method: 'post',
parameters : {action:'rating_send',type: type,id: id,score: score},      
onSuccess: function(t){

$('rating_status_div').innerHTML = t.responseText; 

 $('rating_loading_div').style.display = "none"; 
$('rating_status_div').style.display = "inline"; 
  


}
 }); 
 
 
}


