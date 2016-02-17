$(document).ready(function()
{
	$('.saveSlider').click(function(){
		saveSlider(this);
		return false;
	});
	
	$('.deleteSlider').click(function(){
		deleteSlider(this);
		return false;
	});
	
	$(".upload-slider").uploadFile({
		url:		"/ajax/media.php",
		fileName:	"myfile",
		multiple: 	true,
		doneStr:	"uploaded!",
		formData: {
				opt: 1 
			},
		onSuccess:function(files, data, xhr)
		{
			obj 			= JSON.parse(data);
			imageGallery 	= obj.fileName;
			lastIdGallery 	= obj.lastId;
			
			itemGallery = '<div class="image-box" id="cgid-'+lastIdGallery+'">'
			+'<div class="image">'
			+'<img src="/img-up/companies_pictures/galery/'+imageGallery+'" />' 
			+'</div>'
			+'<a href="javascript:void(0);" cgid="'+lastIdGallery+'" class="deleteGallery" >delete</a>'
			+'</div>';
			
			itemGallery = '<div class="col-sm-12 slider-item" id="sId-'+lastIdGallery+'">'+
			'	<div class="col-sm-12">'+
			'		<div class="col-sm-4">'+
			'			<img alt="" src="/images-system/sliders/'+imageGallery+'" />'+
			'		</div>'+
			'		<div class="col-sm-offset-7 col-sm-1">'+
			'			<a href="javascript:void(0);" class="btn btn-info btn-xs saveSlider" sId="'+lastIdGallery+'">Save</a>'+
			'			<a href="javascript:void(0);" class="btn btn-danger btn-xs deleteSlider" sId="'+lastIdGallery+'">Delete</a>'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-sm-12 slider-section">'+
			'		<div class="col-sm-6">'+
			'			<input type="text" placeholder="Title" class="form-control" id="titleSlider-'+lastIdGallery+'" value="">'+
			'		</div>'+
			'		<div class="col-sm-6">'+
			'			<input type="text" placeholder="Link" class="form-control" id="linkSlider-'+lastIdGallery+'" value="">'+
			'		</div>'+
			'	</div>'+
			'	<div class="col-sm-12 slider-section">'+
			'		<textarea rows="2" cols="" class="form-control" placeholder="Info" id="infoSlider-'+lastIdGallery+'"></textarea>'+
			'	</div>'+
			'</div>';
			
			$('#slidersBox').prepend(itemGallery);
			
			$('.saveSlider').click(function(){
				saveSlider(this);
				return false;
			});
			
			$('.deleteSlider').click(function(){
				deleteSlider(this);
				return false;
			});
		}
	});
});

function saveSlider(node)
{
	var sid	= $(node).attr('sId');
	var titleSlider	= $('#titleSlider-'+sid).val();
	var linkSlider 	= $('#linkSlider-'+sid).val();
	var infoSlider 	= $('#infoSlider-'+sid).val();
	
	if (sid)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/media.php',
	        data:{  sId: 		sid,
	        	titleSlider:	titleSlider,
	        	linkSlider: 	linkSlider,
	        	infoSlider: 	infoSlider,
	            opt: 			2
	             },
	        success:
	        function(xml)
	        {
	            if (0 != xml)
	            {
	            	alert('Slider updated!');
	            }
	        }
	    });
	}
}

function deleteSlider(node)
{
	var sid	= $(node).attr('sId');
	
	if (sid)
	{
		$.ajax({
	        type:   'POST',
	        url:    '/ajax/media.php',
	        data:{  sId: 		sid,
	            opt: 			3
	             },
	        success:
	        function(xml)
	        {
	            if (1 == xml)
	            {
	            	$('#sId-'+sid).hide();
	            }
	        }
	    });
	}
}