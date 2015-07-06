$(function(){
	if ( $('.get-message-not-member').length ) { 
		$('.get-message-not-member').click(function(){
			getMessageNotMember(this)
			return false;
		});
	}
});

function getMessageNotMember(node)
{
	var message_id = $(node).attr('mid');

	$.ajax({
        type:   'POST',
        url:    '/ajax/emails.php',
        data:{  message_id: 	message_id,
            opt: 			1
             },
        success:
        function(xml)
        {
            if (0 != xml)
            {
            	$('#email-content').html(xml);
            }
        }
    });
}

