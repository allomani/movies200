var temp, temp2, cookieArray, cookieArray2, cookieCount;
function expanded_initiate(){
  cookieCount=0;
  if(document.cookie){
  /*
    cookieArray=document.cookie.split(";");
    cookieArray2=new Array();
    for(i in cookieArray){
      cookieArray2[cookieArray[i].split("=")[0].replace(/ /g,"")]=cookieArray[i].split("=")[1].replace(/ /g,"");
    }  */
    cookieArray2 = Get_Cookie("cats_menu_state");  
  }
  
  cookieArray=(document.cookie.indexOf("cats_menu_state=")>=0)?cookieArray2.split(","):new Array();
//cookieArray=new Array(); 
  temp=document.getElementById("cats_menu");
  for(var o=0;o<temp.getElementsByTagName("li").length;o++){
    if(temp.getElementsByTagName("li")[o].getElementsByTagName("ul").length>0){
      temp2 = document.createElement("span");
      temp2.className = "symbols";
      temp2.style.backgroundImage = (cookieArray.length>0)?((cookieArray[cookieCount]=="true")?"url(images/minus.png)":"url(images/plus.png)"):"url(images/plus.png)";
      temp2.onclick=function(){
        expanded_showhide(this.parentNode);
        expanded_writeCookie();
      }
      temp.getElementsByTagName("li")[o].insertBefore(temp2,temp.getElementsByTagName("li")[o].firstChild)
      temp.getElementsByTagName("li")[o].getElementsByTagName("ul")[0].style.display = "none";
      if(cookieArray[cookieCount]=="true"){
        expanded_showhide(temp.getElementsByTagName("li")[o]);
      }
      cookieCount++;
    }
    else{
      temp2 = document.createElement("span");
      temp2.className = "symbols";
  //    temp2.style.backgroundImage = "url(images/page.png)";
      temp.getElementsByTagName("li")[o].insertBefore(temp2,temp.getElementsByTagName("li")[o].firstChild);
    }
  }
}

function expanded_showhide(el){
  el.getElementsByTagName("ul")[0].style.display=(el.getElementsByTagName("ul")[0].style.display=="block")?"none":"block";
  el.getElementsByTagName("span")[0].style.backgroundImage=(el.getElementsByTagName("ul")[0].style.display=="block")?"url(images/minus.png)":"url(images/plus.png)";
}

function expanded_writeCookie(){ // Runs through the menu and puts the "states" of each nested list into an array, the array is then joined together and assigned to a cookie.
  cookieArray=new Array()
  for(var q=0;q<temp.getElementsByTagName("li").length;q++){
    if(temp.getElementsByTagName("li")[q].childNodes.length>0){
      if(temp.getElementsByTagName("li")[q].childNodes[0].nodeName=="SPAN" && temp.getElementsByTagName("li")[q].getElementsByTagName("ul").length>0){
        cookieArray[cookieArray.length]=(temp.getElementsByTagName("li")[q].getElementsByTagName("ul")[0].style.display=="block");
      }
    }
  }
  document.cookie="cats_menu_state="+cookieArray.join(",")+";expires="+new Date(new Date().getTime() + 365*24*60*60*1000).toGMTString();
}


function Get_Cookie( check_name ) {
    // first we'll split this cookie up into name/value pairs
    // note: document.cookie only returns name=value, not the other components
    var a_all_cookies = document.cookie.split( ';' );
    var a_temp_cookie = '';
    var cookie_name = '';
    var cookie_value = '';
    var b_cookie_found = false; // set boolean t/f default f

    for ( i = 0; i < a_all_cookies.length; i++ )
    {
        // now we'll split apart each name=value pair
        a_temp_cookie = a_all_cookies[i].split( '=' );


        // and trim left/right whitespace while we're at it
        cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

        // if the extracted name matches passed check_name
        if ( cookie_name == check_name )
        {
            b_cookie_found = true;
            // we need to handle case where cookie has no value but exists (no = sign, that is):
            if ( a_temp_cookie.length > 1 )
            {
                cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
            }
            // note that in cases where cookie is initialized but no value, null is returned
            return cookie_value;
            break;
        }
        a_temp_cookie = null;
        cookie_name = '';
    }
    if ( !b_cookie_found )
    {
        return null;
    }
}