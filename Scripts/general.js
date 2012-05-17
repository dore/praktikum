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

//** DROPDOWN MENI **
$(document).ready(function () {
    $('#dropDown').click(function () {
	   $('#folders').fadeToggle('fast');
    });
    $('#folders').click(function () {
	   $('#folders').fadeToggle('fast');
    });
});

//****

//** CHARMS BOTTOM **
$(document).ready(function () {
    $('#charmsBottomActivate').click(function () {
	   $('#charmsBottom').slideToggle();
    });
    $('#charmsBottom').mouseleave(function () {
	   $('#charmsBottom').slideToggle();
    });
});

//****

//** ADD FOLDER MENU **
$(document).ready(function () {
    $('#addMapButton').click(function () {
		$('#addMap').animate({width: 'toggle'});
	});

    $('.addMapSubmit').click(function () {
		$('#addMap').animate({width: 'toggle'});
	});
});

//****


//** ADD LINK MENU **
$(document).ready(function () {
    $('#addLinkButton').click(function () {
		$('#addLink').animate({width: 'toggle'});
	});

    $('.addLinkSubmit').click(function () {
		$('#addLink').animate({width: 'toggle'});
	});
});

//****

//** SEND LINK MENU **
$(document).ready(function () {
    $('#sendLinkButton').click(function () {
		$('#sendLink').animate({width: 'toggle'});
	});

    $('.sendLinkSubmit').click(function () {
		$('#sendLink').animate({width: 'toggle'});
	});
});

//****

//** CHARMS RIGHT **
$(document).ready(function () {
    $('#charmsRightActivate').click(function () {
		$('#charmsRight').animate({width: 'toggle'});
	});

    $('#charmsRight').click(function () {
		$('#charmsRight').animate({width: 'toggle'});
	});
});

//****
		
//**menjava med registracijami**       
$(document).ready(function(){
    $(".regular").hide();
    $(".show_hide_regular").show();
    $('.show_hide_regular').click(function(){
        if ($(".facebook").hide() == true) {
            $(".facebook").slideToggle();
        }
        $(".regular").slideToggle();
    });
});

$(document).ready(function(){
    $(".facebook").hide();
    $(".show_hide_facebook").show();
    $('.show_hide_facebook').click(function(){
        if ($(".regular").hide() == false) {
            $(".regular").slideToggle();
        }             
        $(".facebook").slideToggle();
    });
});

//****

//** menjava med screenshoti https warningi **
$(document).ready(function () {
    $("#ieError").hide();
    $('#ieErrorShow').click(function(){ 
        if ($("#chromeError").hide() == false) {
            $("#chromeError").slideToggle();
        } 
        if($("#firefoxError").hide() == false) {
             $("#firefoxError").slideToggle();
        }
        $("#ieError").slideToggle();
    });
});

$(document).ready(function () {
    $("#chromeError").hide();
    $('#chromeErrorShow').click(function(){
        if ($("#ieError").hide() == false) {
            $("#ieError").slideToggle();
        } 
        if($("#firefoxError").hide() == false) {
             $("#firefoxError").slideToggle();
        }
        $("#chromeError").slideToggle();
    });
});

$(document).ready(function () {
    $("#firefoxError").hide();
    $('#firefoxErrorShow').click(function(){
        if ($("#chromeError").hide() == false) {
            $("#chromeError").slideToggle();
        } 
        if($("#ieError").hide() == false) {
             $("#ieError").slideToggle();
        }
        $("#firefoxError").slideToggle();
    });
});

//****