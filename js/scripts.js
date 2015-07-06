$(function(){	
	if ( $('#sign-in').length ) {
		
		$('#login').click(function(){
			
			$('#slick-login').submit();
			return false;
		});
	}
});

