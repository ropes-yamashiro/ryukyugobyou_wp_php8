$(function(){
var menu = $('#slide_menu'), // スライドインするメニューを指定
    menuBtn = $('.button'), // メニューボタンを指定
	mainHead = $('#main_head'),
	contents = $('#contents'),
    body = $(document.body),     
    menuWidth = menu.outerWidth();
	menu.css({'left' : -menuWidth,'display' : 'none'});
	contentsWidth = contents.outerWidth(); 
	bodyHeight = $(window).height();
	mainHead.css({'max-width' : contentsWidth});
	menu.css({'height' : bodyHeight + 'px'});
     
    // メニューボタンをクリックした時の動き
    menuBtn.on('click', function(){
    // body に open クラスを付与する
    body.toggleClass('open');
        if(body.hasClass('open')){
            // open クラスが body についていたらメニューをスライドインする
            body.animate({'left' : menuWidth }, 300);      
            menu.css({'display' : 'block'}).animate({'left' : 0 }, 300); 

        } else {
            // open クラスが body についていなかったらスライドアウトする
            menu.animate({'left' : -menuWidth }, 150).delay(300).queue(function() {$(this).css({'display' : 'none'}).dequeue();});
            body.animate({'left' : 0 }, 300);
			menu.scrollTop(0);           
        }      
    });
	 //リンク先が同一ページの場合、クリックしたときにメニューが消す為に追記
	  $('#hed_sp .menu a').on('click', function(){
		  body.removeClass('open');
		  menu.animate({'left' : -menuWidth }, 300).delay(300).queue(function() {$(this).css({'display' : 'none'}).dequeue();});
		  body.animate({'left' : 0 }, 300);  
	  });    
});




$(function(){
  $(".fot_teiten .method02").on("click", function() {
	   $(".tokuten_blk").slideToggle();
	   $("#cover").toggle();
	   $(".fot_teiten .method02").toggleClass("selected");
  });
  
  $(".close").on("click", function() {
	   $(".tokuten_blk").slideToggle();
	   $("#cover").toggle();
	   $(".fot_teiten .method02").toggleClass("selected");
  });
  
});