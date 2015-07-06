$(function(){
	
	$('.completeTask').click(function(){
		completeTask(this);
    });
	
	if ( $('#add-task').length ) { 
		$('#add-task').click(function(){
			addMemberTask();
			return false;
		});
	}
	
	if ( $('.display-add-task').length ) { 
		$('.display-add-task').click(function(){
			$('.create-task-box').fadeIn();
			return false;
		});
	}
	
	if ( $('#get-pending-tasks').length ) { 
		$('#get-pending-tasks').click(function(){
			getTasks(1);
			return false;
		});
	}
	
	if ( $('#get-today-tasks').length ) { 
		$('#get-today-tasks').click(function(){
			getTasks(2);
			return false;
		});
	}
	
	if ( $('#get-future-tasks').length ) { 
		$('#get-future-tasks').click(function(){
			getTasks(3);
			return false;
		});
	}
	
	if ( $('#get-completed-tasks').length ) { 
		$('#get-completed-tasks').click(function(){
			getTasks(4);
			return false;
		});
	}
});

function addMemberTask()
{
	var memberId 	= $('#member-id').val();
	var task_to 	= $('#task_to').val();
	var	task_date 	= $('#task-date').val();
	
	task_hour = $('#task_hour').val();
	task_content = $('#task_content').val();

	if (task_content)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/members.php',
	        data:{  memberId: 	memberId,
	        	task_to: task_to,
				task_date: task_date,
				task_hour: task_hour,
				task_content: task_content,
	            opt: 			6
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	getMemberTasks();
	            	$('#task_content').val('');
					$('#task-date').val('');
					$('#task-box').html('');
					$('#task-box').html(xml);
	            }
	        }
	    });
	}
}

function getMemberTasks()
{
	var memberId 		= $('#member-id').val();
	
	if (memberId)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/members.php',
	        data:{  memberId: 	memberId,
	            opt: 			7
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	$('.task-list').html(xml);
	            	$('.completeTask').click(function(){
	            		completeTask(this);
	                });
	            }
	        }
	    });
	}
}

function getTasks(opt)
{
	$.ajax({
        type:   'POST',
        url:    '/ajax/tasks.php',
        data:{
            opt: opt
             },
        success:
        function(xml)
        {
            if (0 != xml)
            {
            	$('.task-list').html(xml);
            	$('.completeTask').click(function(){
            		completeTask(this);
                });
            }
        }
    });
}

function completeTask(node)
{
	tid = $(node).attr('tid');
	$.ajax({
        type:   'POST',
        url:    '/ajax/tasks.php',
        data:{  tid: tid,
            opt: 	5
             },
        success:
        function(xml)
        {
            if (0 != xml)
            {
            	$(node).fadeOut();
            }
        }
    });
}