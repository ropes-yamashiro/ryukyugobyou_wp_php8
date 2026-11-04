deftab();

function openWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function isCheck(word){
	return window.confirm(word); 
}

function selbox(selected,id_max) {
  if (! document.getElementById) return;
  for (i = 0; i <= id_max; i++) {
    if (! document.getElementById("box" + i)) continue;
//	document.getElementById("box" + i).style.height = "0px";

    if (i == selected) {
      document.getElementById("box" + i).style.visibility = "visible";
      document.getElementById("box" + i).style.display = "block";
      document.getElementById("box" + i).style.position = "";
    } else {
      document.getElementById("box" + i).style.visibility = "hidden";
      document.getElementById("box" + i).style.display = "none";
      document.getElementById("box" + i).style.position = "absolute";
    }
  }
}

function seltab(selected,id_max,sp) {
  if (! document.getElementById) return;
  for (i = 0; i <= id_max; i++) {
    if (! document.getElementById("box" + i)) continue;
	
//	if (!sp) {
//	    document.getElementById("box" + i).style.height = "0px";
//	}

    if (i == selected) {
//		if (sp) {
//			document.getElementById("box" + i).style.display = "block";
//		}
//		else {
			document.getElementById("box" + i).style.visibility = "visible";
			document.getElementById("box" + i).style.position = "";
//		}

		if(document.getElementById("tab" + i))
		  document.getElementById("tab" + i).className = "open";
    }
	else {
//		if (sp) {
//			document.getElementById("box" + i).style.display = "none";
//		}
//		else {
			document.getElementById("box" + i).style.visibility = "hidden";
			document.getElementById("box" + i).style.position = "absolute";
//		}
		
		if(document.getElementById("tab" + i))
		  document.getElementById("tab" + i).className = "close";
    }
  }
  
  if(document.getElementById("box" + i))
    document.cookie = "box=" + selected + ";";
}
function deftab(first) {
	var def_sel = getCookie("box");
	if (!def_sel) def_sel = first;
	
	var sp = $("body").attr("data-ui") == "sp";	//スマホテンプレートかどうか
	
	seltab(def_sel,10, sp);
}
function getCookie(name){
  var search = name + '=';
  if (document.cookie.length>0) {
    offset = document.cookie.indexOf(search);
    if (offset != -1){
      offset += search.length;
      end     = document.cookie.indexOf(';',offset);
      if(end == -1) end = document.cookie.length;
      return unescape(document.cookie.substring(offset,end));
    }
  }
  return null;
}


var bgcolors = new Array("", "#FFDDDD");
//----------------------------------------------------------
//  Bgcolor
//----------------------------------------------------------
function chBG(obj, color){
	document.getElementById(obj).style.backgroundColor = bgcolors[color];
	//tagList = document.getElementById(obj).all.tags("td");
	//for (i=0; i<tagList.length; i++){
	//	tagList[i].style.backgroundColor = bgcolors[color];
	//}
}

$(function(){
  $('.js_tab a').click(function() {
    $(this).parent().addClass("is_active").siblings(".is_active").removeClass("is_active");
    var tabContents = $('[data-id="' + $(this).data('link') + '"]');
    tabContents.addClass("is_active").siblings(".is_active").removeClass("is_active");
    return false;
  });
});