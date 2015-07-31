$(function(){
	
	if ( $('#addAgency').length ) { 
		$('#addAgency').click(function(){
			addAgency();
			return false;
		});
	}
	
	if ( $('.display-add-history').length ) { 
		$('.display-add-history').click(function(){
			$('.history-member-box').fadeIn();
			return false;
		});
	}
});

function addAgency()
{
	var agency	= $('#agency').val();
	
	if (agency)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/agencies.php',
	        data:{  agency: 	agency,
	            opt: 			1
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	getAgencies();
	            	$('#agency').val('');
	            }
	        }
	    });
	}
}

function getAgencies()
{
	$.ajax({
        type:   'POST',
        url:    '/ajax/agencies.php',
        data:{ 
            opt: 			2
             },
        success:
        function(xml)
        {
            if (0 != xml)
            {
            	$('#agenciesList').html(xml);
            }
        }
    });
}