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
	
	$('#pricePerNight').change(function(){
		totalDays = $('#totalDays').html();
		pricePerNight = $('#pricePerNight').val();
		
		totalReservation = totalDays * pricePerNight;
		$('#totalReservation').val(totalReservation);
	});
	
	$('.reservation-options div').click(function(){
		var optRes = $(this).attr('opt-res');
		var singleRes =  $(this).attr('single-res');
		$('.reservation-options div').removeClass('checked');
		$(this).addClass('checked');
		$('#res-option-'+singleRes).val(optRes);
		
	});
	
	$('.save-single-res-a').click(function(){
		var singleRes =  $(this).attr('single-res');
		updateMemberReservation(singleRes);
	});
	
	$('.add-extra-pay').click(function(){
		var resId = $(this).attr('res-id');
		processExtraPayment(resId);
	});
	
	$('.btn-status-money').click(function(){
		var info  = $(this).parent();
		var resId = $(info).attr('res-id');
		var payId = $(info).attr('pay-id'); 
		setPaymentStatus(resId, payId);
	});
});

function setPaymentStatus(resId, payId)
{
	if (resId && payId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
		        	reservationId:	resId,
		        	paymentId: payId,
		            opt: 			11
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	getAllPayments(resId);
	            	getGrandTotal(resId);
	            	getPaid(resId);
	            	getPending(resId);
	            	
	            }
	        }
	    });
	}
}

/**
 * processExtraPayment
 * 
 * add and calculate the accounts of a payment reservation
 * 
 * @param resId
 */
function processExtraPayment(resId)
{
	addExtraPayment(resId);
	getAllPayments(resId);
	getGrandTotal(resId);
	getPaid(resId);
	getPending(resId);
}

function getAllPayments(resId)
{
	if (resId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
		        	reservationId:	resId,
		            opt: 			10
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#payment-items-'+resId).html(xml);
	            	$('.btn-status-money').click(function(){
	            		var info  = $(this).parent();
	            		var resId = $(info).attr('res-id');
	            		var payId = $(info).attr('pay-id'); 
	            		setPaymentStatus(resId, payId);
	            	});
	            }
	        }
	    });
	}
}

/**
 * addExtraPayment
 * 
 * add a payment related to a reservation
 * 
 * @param resId
 */
function addExtraPayment(resId)
{
	var extraDes 	= $('#extra-pay-des-'+resId).val();
	var extraCost 	= $('#extra-pay-cost-'+resId).val();
	
	if (extraDes && extraCost)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
		        	reservationId:	resId,
		        	description: 	extraDes,
					cost: 			extraCost,
		            opt: 			6
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#extra-pay-des-'+resId).val('');
	            	$('#extra-pay-cost-'+resId).val('');
	            }
	        }
	    });
	}
}

function getGrandTotal(resId)
{
	if (resId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
		        	reservationId:	resId,
		            opt: 			7
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#payment-grand-total-'+resId).html(xml);
	            }
	        }
	    });
	}
}

function getPaid(resId)
{
	if (resId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
		        	reservationId:	resId,
		            opt: 			8
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#payment-paid-total-'+resId).html(xml);
	            }
	        }
	    });
	}
}

function getPending(resId)
{
	if (resId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
		        	reservationId:	resId,
		            opt: 			9
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('#payment-pending-total-'+resId).html(xml);
	            }
	        }
	    });
	}
}

function searchReservation()
{
	var memberId 	= $('#member-id').val();
	var checkIn 	= $('#checkIn').val();
	var checkOut	= $('#checkOut').val();
	
	var reservationAdults 	= $('#reservationAdults').val();
	var reservationChildren = $('#reservationChildren').val();

	if (checkIn && checkOut )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  memberId: 			memberId,
	        	checkIn: 				checkIn,
				checkOut: 				checkOut,
				reservationAdults: 		reservationAdults,
				reservationChildren: 	reservationChildren,
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
	        			return false;
	        		});
	            }
	        }
	    });
	}
}

function processReservation(node)
{
	var roomName 	= $(node).attr('rn');
	var roomId 		= $(node).attr('ri');
	var checkIn 	= $('#checkIn').val();
	var checkOut	= $('#checkOut').val();
	var totalNights = 0;
	$('.rightSideReservations').show();
	$('#roomName').html(roomName);
	$('#roomId').val(roomId);
	
	if (checkIn){
		$('#checkInReservation').html(checkIn);
	}
	
	if (checkOut){
		$('#checkOutReservation').html(checkOut);
	}
	
	if (checkIn && checkOut)
	{
		totalNights = restaFechas(checkIn, checkOut);
	}

	$('#totalDays').html(totalNights);
}

/**
 * restaFechas
 * 
 * Returns the number of days between two dates
 * 
 * @param f1
 * @param f2
 * @returns int dias returns he difernce between f1 and f2
 */
function restaFechas(f1,f2)
{
	var aFecha1 = f1.split('/'); 
	var aFecha2 = f2.split('/'); 
	var fFecha1 = Date.UTC(aFecha1[2],aFecha1[0]-1,aFecha1[1]); 
	var fFecha2 = Date.UTC(aFecha2[2],aFecha2[0]-1,aFecha2[1]); 
	var dif 	= fFecha2 - fFecha1;
	var dias	= Math.floor(dif / (1000 * 60 * 60 * 24)); 
	return dias;
}

/**
 * addReservationMember
 * 
 * Creates a member and also add a reservation under that member
 */
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
	        	memberName: 	memberName,
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
	var memberId 			= $('#memberId').val();
	var reservationAdults 	= $('#reservationAdults').val();
	var reservationChildren = $('#reservationChildren').val();
	var checkIn 			= $('#checkIn').val();
	var checkOut			= $('#checkOut').val();
	var roomId 				= $('#roomId').val();
	var price 				= $('#totalReservation').val();
	var pricePerNight 		= $('#pricePerNight').val();
	var agency 				= $('#agencyList').val();
	var externalId			= $('#externalId').val();
	
	if (memberId && roomId )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
	        	memberId: 				memberId,
	        	reservationAdults: 		reservationAdults,
	        	reservationChildren: 	reservationChildren,
	        	checkIn: 				checkIn,
	        	checkOut: 				checkOut,
	        	roomId: 				roomId,
	        	agency: 				agency,
	        	pricePerNight: 			pricePerNight,
	        	price: 					price,
	        	externalId:				externalId,
	            opt: 					3
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {

	            }
	        }
	    });
	}
}

function addReservationMemberPanel()
{
	
	var memberId 			= $('#member-id').val();
	var reservationAdults 	= $('#reservationAdults').val();
	var reservationChildren = $('#reservationChildren').val();
	var checkIn 			= $('#checkIn').val();
	var checkOut			= $('#checkOut').val();
	var roomId 				= $('#roomId').val();
	var price 				= $('#totalReservation').val();
	var agency 				= $('#agencyList').val();
	var externalId			= $('#externalId').val();
	
	if (memberId && roomId )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
	        	memberId: 				memberId,
	        	reservationAdults: 		reservationAdults,
	        	reservationChildren: 	reservationChildren,
	        	checkIn: 				checkIn,
	        	checkOut: 				checkOut,
	        	roomId: 				roomId,
	        	price: 					price,
	        	agency: 				agency,
	        	externalId: 			externalId,
	            opt: 					4
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

function updateMemberReservation(reservationId)
{
	var optRes = $('#res-option-'+reservationId).val();
	
	if (reservationId )
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/reservations.php',
	        data:{  
	        	reservationId: reservationId,
	        	optRes : optRes,
	            opt: 			5
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
//	            	$('#memberReservations').html(xml);
	            }
	        }
	    });
	}
}