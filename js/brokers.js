$(function(){
	
	if ( $('#brokerSave').length ) { 
		$('#brokerSave').click(function(){
			saveBroker();
//			alert('you mama is a broker');
			return false;
		});
	}
	
	$('.alert-autocloseable-success').hide();
	
	$('#addEmailField').click(function(){
		emailField ='<div class="form-group">' +
			'<label class="col-sm-2 control-label" for="textinput">Email</label>' +
			'<div class="col-sm-9">' +
			'<input type="text" placeholder="Email" class="form-control memberEmail" eid="0">' +
			'</div>' +
			'</div>';

		$('#memberEmails').append(emailField);
	});
	
	$('#addPhoneField').click(function(){
		phoneField = '<div class="form-group">' +
			'<label class="col-sm-2 control-label" for="textinput">Phone</label>' +
			'<div class="col-sm-9">' +
			'<input type="text" placeholder="Phone" class="form-control memberPhone" pid="0">' +
			'</div>' +
			'</div>';

		$('#memberPhones').append(phoneField);
	});
	
});

function selCountry(sel) {
	var country	= $(sel).val();
	$('#country').val(country);
	
	if (country)
	{
		$.ajax({
			type: "POST",
			url: "/ajax/locations.php",
			data: {country:	country, opt: '1'},
			success:
			function(info)
			{
				if (0 != info)
				{
					$('#state_list').html(info);	
				}else
				{
					
				}
			}
		});	
	}
}

function selState(sel) {
	var state	= $(sel).val();
	
	$('#mState').val(state);
	
	if (state)
	{
		$.ajax({
			type: "POST",
			url: "/ajax/locations.php",
			data: {state:	state, opt: '2'},
			success:
			function(info)
			{
				if (0 != info)
				{
					$('#city_list').html(info);
					
				}else
				{
					
				}
			}
		});	
	}
}

function selCity(sel) {
	var city	= $(sel).val();
	$('#city').val(city);
}

function saveBroker()
{
	var memberId 		= $('#member-id').val();
	var memberName 		= $('#member-name').val(); 
	var memberLastName	= $('#member-last-name').val();
	var memberAddress	= $('#member-address').val();
	var country		 	= $('#country').val();
	var mState		 	= $('#mState').val();
	var city		 	= $('#city').val();
	var notes		 	= $('#notes').val();
	
	$.ajax({
    type: "POST",
    url: "/ajax/brokers.php",
    data: {
    	memberId: 		memberId,
    	memberName: 	memberName, 
    	memberLastName:	memberLastName,
    	memberAddress: 	memberAddress,
    	country: 		country,
    	mState: 		mState,
    	city: 			city,
    	notes:			notes,
    	opt:			'1'
    },
    success:
        function(info)
        {
        	if (info != '0')
        	{
        		$('#member-id').val(info);
        		saveBrokerEmails();
        		saveBrokerPhones();
    			$('.alert-autocloseable-success').show();

    			$('.alert-autocloseable-success').delay(3000).fadeOut( "slow", function() {
    				// Animation complete.
    			});
        	}else
			{
				alert('Errr..');
			}
        }
    });
}

function saveBrokerEmails()
{
	emailId 	= 0;
	emailVal 	= '';
	memberId 	= $('#member-id').val();
	
	$('.memberEmail').each(function(){
		emailId 	= 0;
		if ($(this).attr('eid'))
		{
			emailId		= $(this).attr('eid');
			emailVal	= $(this).val();
			
			$.ajax({
		        type:   'POST',
		        url:    '/ajax/brokers.php',
		        data:{  memberId: 	memberId,
		        	emailId: 		emailId,
		        	emailVal: 		emailVal,
		            opt: 		2
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
	});
}

function saveBrokerPhones()
{
	phoneVal 	= '';
	memberId 	= $('#member-id').val();
	
	$('.memberPhone').each(function(){
		phoneId		= $(this).attr('pid');
		phoneVal	= $(this).val();
		
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/brokers.php',
	        data:{  memberId: 	memberId,
	        	phoneId: 		phoneId,
	        	phoneVal: 		phoneVal,
	            opt: 			3
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	
	            }
	        }
	    });
	});
}
