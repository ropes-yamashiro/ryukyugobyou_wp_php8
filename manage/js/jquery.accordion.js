 // アコーディオン
        $(function() {
    
        // アコーディオン
        $(".faq_list").each(function() {
            var accordion = $(this);
            $(this).find(".faq_txt").click(function() {
            //$("> .switch", this).click(function() { // 上段の別の書き方
                var targetContentWrap = $(this).next(".ans_txt");
                if ( targetContentWrap.css("display") === "none" ) {
                    accordion.find(".ans_txt").slideUp();
                    accordion.find(".faq_txt.open").removeClass("open");
                }
                targetContentWrap.slideToggle();
                $(this).toggleClass("open");
            });
        });
		
		if ( navigator.userAgent.indexOf('Android') > 0 ) {
    $(".size_tooltip").addClass("android");
	$(".roma_tooltip").addClass("android");
};
    
    });

$(function(){

	$(".acc_inner").hide();

	$(".displaytype").each(function() {
		var accordion = $(this);
		$(this).find(".acc_ttl").click(function() {
//			var targetContentWrap = $(this).next(".acc_inner");
//			if ( targetContentWrap.css("display") === "none" ) {
//				accordion.find(".acc_inner").slideUp();
//				accordion.find(".acc_ttl.open").removeClass("open");
//			}
//			targetContentWrap.slideToggle();
//			$(this).toggleClass("open");	
			switchType();
		});		
	});
	
	function switchType() {
		$("input[name='type']").each(function() {
			if ($(this).prop("checked")) {
				$(this).parent(".acc_ttl").next(".acc_inner").slideDown();
				$(this).parent(".acc_ttl").addClass("open");
			}
			else {
				$(this).parent(".acc_ttl").next(".acc_inner").slideUp();
				$(this).parent(".acc_ttl").removeClass("open");
			}
		});
	}
	
	switchType();
}); 


$(function(){
$("#hed_sp .acc").hide();

$("#hed_sp .btn_acc").click(function(){
$(this).next("ul").slideToggle();
$(this).next("ul").toggleClass("open");
$(this).toggleClass("open");	
});
}) 

$(function(){
	
	$(".footer_menu .btn_acc").click(function(){
	$(this).next(".acc").slideToggle();
	$(this).next(".acc").toggleClass("open");
	$(this).toggleClass("open");	
	});
})
