
//下層ページ 見出し画像アニメーション　マスク
$(document).ready(function(){
		$('#main_mask').animate({left: '3000px'},{duration: 800, easing: 'easeInExpo'});
});
	
//下層ページ 見出し画像アニメーション　テキスト表示
$(function(){
    setTimeout(function(){
		$('.main_title').animate({opacity: '1.0'},1000);
    },1000);
});

//会社案内 ファーストビューコンテンツ 時間差表示
$(function(){
    setTimeout(function(){
		$('.firstview-fade-in').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                });
		$('.firstview-fade-in-delay01').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.1s'
                });
		$('.firstview-fade-in-delay02').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.2s'
                });
		$('.firstview-fade-in-delay03').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.3s'
                });
		$('.firstview-fade-in-delay04').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.4s'
                });		
		$('.firstview-fade-in-delay05').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.5s'
                });		
		$('.firstview-fade-in-delay06').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.6s'
                });		
		$('.firstview-fade-in-delay07').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.7s'
                });		
		$('.firstview-fade-in-delay08').css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.8s'
                });		

    },800);
});




$(window).on('load',function(){
	
	//下から透明度を上げながら表示
    $(window).scroll(function (){
        $('.fade-up.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.1s'

                });
            } else {
                $(this).css({
                        'opacity':'0',
                        'transform':'translateY(50px)',
                        '-webkit-transform':'translateY(50px)',
                        '-moz-transform':'translateY(50px)',
                        '-ms-transform':'translateY(50px)'
                });
            }
        });
    });
	
	// fade-up
    $(window).scroll(function (){
        $('.fade-up-delay.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transform':'translateY(0)',
                        '-webkit-transform':'translateY(0)',
                        '-moz-transform':'translateY(0)',
                        '-ms-transform':'translateY(0)',
                        'transition-delay':'0.5s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                        'transform':'translateY(30px)',
                        '-webkit-transform':'translateY(30px)',
                        '-moz-transform':'translateY(30px)',
                        '-ms-transform':'translateY(30px)'
                });
            }
        });
    });
	
	// fade-up
    $(window).scroll(function (){
		$('.fade-in.toAnimate').css("opacity","0");
        $('.fade-in.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.1s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });
	



//スマホ用に
		// fade-up
	// fade-up
    $(window).scroll(function (){
        $('.fade-in-delay01sp.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.2s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });

	// fade-up
    $(window).scroll(function (){
        $('.fade-in-delay02sp.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.3s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });
	
		// fade-up
    $(window).scroll(function (){
        $('.fade-in-delay03sp.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.4s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });
	
		// fade-up
    $(window).scroll(function (){
        $('.fade-in-delay04sp.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.5s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });

		// fade-up
    $(window).scroll(function (){
        $('.fade-in-delay05sp.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.6s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });


		// fade-up
    $(window).scroll(function (){
        $('.fade-in-delay06sp.toAnimate').each(function(){
            var POS = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();

            if (scroll > POS - windowHeight + windowHeight/5){
				$(this).removeClass('toAnimate');
                $(this).css({
                        'opacity':'1',
                        'transition-delay':'0.7s'
                });
            } else {
                $(this).css({
                        'opacity':'0',
                });
            }
        });
    });

});


