function move_img(id,width,height){
    document.formx.id.value = id;
    document.formx.submit();
    //window.resizeTo(width, height);
    return false;
}

function popup(id,group,width,height,gfix){
    var left= (window.screen.width-width) / 2 ;
    //var top = (window.screen.height- height) / 2;
    var top = 0;
    newwin = window.open("?id=" + id + "&g=" + group + "&gfix=" + gfix,"_blank","height=" + height
        + ",width=" + width 
        + ",menubar=0,resizable=0,scrollbars=1,status=0,titlebar=0,toolbar=0,left="+ left +",top=" + top);
}

function parent_open(url){
     window.opener.location.href = url;
     window.close();
     window.opener.focus();
}

window.onload = function() {
	var elements = document.getElementsByTagName("a");

	for (var i = 0; i < elements.length; i++)
	{
		if (elements[i].className == "gallery-anchor")
		{
			var href = elements[i].getAttribute('href');
			if (href != null && href == "")
			{
				elements[i].removeAttribute("href");
			}
		}
	}
};
