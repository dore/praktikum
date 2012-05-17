tinyMCE.init({
// General options
mode : "textareas",
theme : "simple",
plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

// Theme options
theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
//theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,

// Example content CSS (should be your site CSS)
content_css : "css/content.css",

// Drop lists for link/image/media/template dialogs
template_external_list_url : "lists/template_list.js",
external_link_list_url : "lists/link_list.js",
external_image_list_url : "lists/image_list.js",
media_external_list_url : "lists/media_list.js",

// Style formats
style_formats : [
	{title : 'Bold text', inline : 'b'},
	{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
	{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
	{title : 'Example 1', inline : 'span', classes : 'example1'},
	{title : 'Example 2', inline : 'span', classes : 'example2'},
	{title : 'Table styles'},
	{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
],

// Replace values for the template plugin
/*template_replace_values : {
	username : "Some User",
	staffid : "991234"
}*/
});
// getTitle
$(document).ready(function() {
$("#driver").click(function(event){
  $.get( 
     "include/getTitle.php",
     { getLink: $('#fieldUrl').val() },
     function(data) {
        $('#fieldTitle').val(data);
     }
  );
});
});
// autofill
function showHint(str) {
$('#txtHint').show();
 if (str.length==0) { 
    document.getElementById("txtHint").innerHTML="";
    return;
 }
 if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
 }
 else {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
 }
 xmlhttp.onreadystatechange=function() {
     if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
     }
 }
 xmlhttp.open("GET","include/gethint.php?q="+str,true);
 xmlhttp.send();
}
function fill(thisValue) {
$('#inputString').val(thisValue);
setTimeout("$('#txtHint').hide();", 200);
}
//
// for login form
function userField_Focus(el) {

    el.value = el.value != "e-mail" ? el.value : "";
}

function userField_Blur(el) {
    el.value = el.value != "" ? el.value : "e-mail";
}

function passwordField_Focus(el) {
    if (el.value == "Geslo") {
        el.value = "";
    }
}

function passwordField_Blur(el) {
    if (el.value == "") {
        el.value = "Geslo";
    }
}

function checkKey(e) {
    if (typeof e == undefined) {
        e = window.event;
    }

    if (e.keyCode == 13) {
        document.form1.submit();
    }
}
//

//menu
function menu(layer){
    var myLayer = document.getElementById(layer);
    if(myLayer.style.display == "none"){ 
        myLayer.style.display = "block";
    } else { 
        myLayer.style.display = "none";
    }
}
//

// Add to favourites
function bookmarksite(title, url){
if (document.all)
    window.external.AddFavorite(url, title);
else if (window.sidebar)
    window.sidebar.addPanel(title, url, "")
}
//

// ShowMap
function selectMap(dropdown, id){
    window.location = "index.php?page=allMaps&action=setMap&id=" + id + "&value=" + dropdown.value;
}
//

// Add link to front page
function toHomePage(box, id) {
    if(box.checked == true) var value1 = 1;
    else var value1 = 0;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("GET","index.php?page=allMaps&action=setToHomePage&id=" + id + "&value=" + value1,true);
    xmlhttp.send();
}

function makesure() {
  if (confirm('Are you sure that you want to continue?')) {
     //dosomething();
    return true;
  }
  else {
    return false;
  }
}