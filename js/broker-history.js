$(function(){
	
	if ( $('#add-history').length ) { 
		$('#add-history').click(function(){
			addHistoryBroker();
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

function addHistoryBroker()
{
	var memberId 		= $('#member-id').val();
	var historyEntry	= $('#history-entry').val();
	
	if (historyEntry)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/brokers.php',
	        data:{  memberId: 	memberId,
	        	historyEntry: 	historyEntry,
	            opt: 			4
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	getHistoryBroker();
	            	$('#history-entry').val('');
	            }
	        }
	    });
	}
}

function getHistoryBroker()
{
	var memberId 		= $('#member-id').val();
	
	if (memberId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/brokers.php',
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