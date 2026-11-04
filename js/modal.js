  $(document).ready(function(){
  
  		//通常ヘッダーの場合
        $('#global .menu__mega.no1,#global .menu__mega.no2,#global .menu__mega.no3,#global .menu__mega.no4').mouseover(
            function () {
				 $('#global').append('<div class="modal"></div>');	
                 $('#global').css('box-shadow', 'none');
                 $('#global').css('border-bottom', 'none');
		});
				  

        $('#global .menu__mega.no1,#global .menu__mega.no2,#global .menu__mega.no3,#global .menu__mega.no4').mouseout(
            function () {
                 $('#global').css('box-shadow', '0px 0 3px rgba(0,0,0,0.08),0px 3px 4px rgba(204,204,204,1)');
                 $('#global').css('border-bottom', '1px solid #ccc;');
				 
			$('div.modal').remove();
		});
		
		
  		//隠しヘッダーの場合
        $('.h_global .menu__mega.no1,.h_global .menu__mega.no2,.h_global .menu__mega.no3,.h_global .menu__mega.no4').mouseover(
            function () {
				 $('.h_global').append('<div class="modal"></div>');	
                 $('.h_global').css('box-shadow', 'none');
                 $('.h_global').css('border-bottom', 'none');
		});

        $('.h_global .menu__mega.no1,.h_global .menu__mega.no2,.h_global .menu__mega.no3,.h_global .menu__mega.no4').mouseout(
            function () {
                 $('.h_global').css('box-shadow', '0px 0 3px rgba(0,0,0,0.08),0px 3px 4px rgba(204,204,204,1)');
                 $('.h_global').css('border-bottom', '1px solid #ccc;');
				 
			$('div.modal').remove();
		});		
		
		
		
		
		
		
  });