$(function(){
  var menuHeight = $("header").height();
	var body = $(document.body);
var startPos = 0;
$(window).scroll(function(){
  var currentPos = $(this).scrollTop();
  if (currentPos > startPos) {
    if($(window).scrollTop() >= 50) {
		if(body.hasClass('open')){
		}else{
      //$("header").css("top", "-" + menuHeight + "px");
		if($("#hed_wrp").hasClass("visible_header")){
    $("#hed_wrp").removeClass("visible_header");
  }
	$("#hed_wrp").addClass("hide_header");
			
	}
  }
  } else {
    //$("header").css("top", 0 + "px");
	  if($("#hed_wrp").hasClass("hide_header")){
    $("#hed_wrp").removeClass("hide_header");
  }
	  $("#hed_wrp").addClass("visible_header");
	  
  }
  startPos = currentPos;
});

});