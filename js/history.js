$(function(){
	
	if ( $('#add-history').length ) { 
		$('#add-history').click(function(){
			addHistoryMember();
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

function addHistoryMember()
{
	var memberId 		= $('#member-id').val();
	var historyEntry	= $('#history-entry').val();
	
	if (historyEntry)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/members.php',
	        data:{  memberId: 	memberId,
	        	historyEntry: 	historyEntry,
	            opt: 			4
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	getHistoryMember();
	            	$('#history-entry').val('');
	            }
	        }
	    });
	}
}

function getHistoryMember()
{
	var memberId 		= $('#member-id').val();
	
	if (memberId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/members.php',
	        data:{  memberId: 	memberId,
	            opt: 			5
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('.history-list').html(xml);
	            }
	        }
	    });
	}
}