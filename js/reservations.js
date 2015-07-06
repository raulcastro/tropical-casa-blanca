$(function(){
	
	$('#searchReservation').click(function(){
		searchReservation(this);
    });
	
	if ( $('.reservationResults .operator').length ) { 
		$('.reservationResults .operator a').click(function(){
			processReservation(this);
			return false;
		});
	}
	
	if ( $('#bookRoomMember').length ) { 
		$('#bookRoomMember').click(function(){
			addReservationMemberPanel();
			return false;
		});
	}
});

function searchReservation()
{
	var memberId 	= $('#member-id').val();
	var checkIn 	= $('#checkIn').val();
	var checkOut	= $('#checkOut').val();
	
	var reservationAdults = $('#reservationAdults').val();
	var reservationChildren = $('#reservationChildren').val();

	if (checkIn && checkOut )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  memberId: 	memberId,
	        	checkIn: checkIn,
				checkOut: checkOut,
				reservationAdults: reservationAdults,
				reservationChildren: reservationChildren,
	            opt: 			1
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#reservationResults').html(xml);
	            	$('.reservationResults .operator a').click(function(){
	        			processReservation(this);
	        			return false;
	        		});
	            	
	            	$('#bookRoom').click(function(){
	            		addReservationMember();
//	        			processReservation(this);
	        			return false;
	        		});
	            	/*getMemberTasks();
	            	$('#task_content').val('');
					$('#task-date').val('');
					$('#task-box').html('');
					$('#task-box').html(xml);*/
	            }
	        }
	    });
	}
}

function processReservation(node)
{
	var roomName = $(node).attr('rn');
	var roomId = $(node).attr('ri');
	var checkIn 	= $('#checkIn').val();
	var checkOut	= $('#checkOut').val();
	$('.rightSideReservations').show();
	$('#roomName').html(roomName);
	$('#roomId').val(roomId);
	
	if (checkIn){
		$('#checkInReservation').html(checkIn);
	}
	
	if (checkOut){
		$('#checkOutReservation').html(checkOut);
	}
}

function addReservationMember()
{
	var memberName = $('#member-name').val();
	var memberLastName = $('#member-last-name').val();
	
	
	if (memberName && memberLastName )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
	        	memberName: memberName,
	        	memberLastName: memberLastName,
	            opt: 			2
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#memberId').val(xml);
	            	addReservation();
	            	$('#completeProfileBtn').show();
	            	$('#bookRoom').hide();
	            	$('#completeProfileBtn').attr('href','/'+xml+'/new-reservation/');
	            }
	        }
	    });
	}
}

function addReservation()
{
	var memberId 	= $('#memberId').val();
	var reservationAdults = $('#reservationAdults').val();
	var reservationChildren = $('#reservationChildren').val();
	var checkIn 	= $('#checkIn').val();
	var checkOut	= $('#checkOut').val();
	var roomId = $('#roomId').val();
	var price = $('#totalReservation').val();
	
//	alert(roomId);
	
	if (memberId && roomId )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
	        	memberId: memberId,
	        	reservationAdults: reservationAdults,
	        	reservationChildren: reservationChildren,
	        	checkIn: checkIn,
	        	checkOut: checkOut,
	        	roomId: roomId,
	        	price: price,
	            opt: 			3
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
//	            	$('#memberId').val(xml);
//	            	addReservation();
//	            	$('#completeProfileBtn').show();
//	            	$('#bookRoom').hide();
//	            	$('#completeProfileBtn').attr('href','/'+xml+'/new-reservation/');
	            }
	        }
	    });
	}
}

function addReservationMemberPanel()
{
	
	var memberId 	= $('#member-id').val();
	var reservationAdults = $('#reservationAdults').val();
	var reservationChildren = $('#reservationChildren').val();
	var checkIn 	= $('#checkIn').val();
	var checkOut	= $('#checkOut').val();
	var roomId = $('#roomId').val();
	var price = $('#totalReservation').val();
	
	if (memberId && roomId )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
	        	memberId: memberId,
	        	reservationAdults: reservationAdults,
	        	reservationChildren: reservationChildren,
	        	checkIn: checkIn,
	        	checkOut: checkOut,
	        	roomId: roomId,
	        	price: price,
	            opt: 			4
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#memberReservations').html(xml);
//	            	$('#memberId').val(xml);
//	            	addReservation();
//	            	$('#completeProfileBtn').show();
//	            	$('#bookRoom').hide();
//	            	$('#completeProfileBtn').attr('href','/'+xml+'/new-reservation/');
	            }
	        }
	    });
	}
}