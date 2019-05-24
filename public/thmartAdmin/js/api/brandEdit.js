$(function(){
	$("#ckfinder-popup-1").on("click",function() {
		CKFinder.popup({
	        chooseFiles: true,
	        width: 800,
	        height: 600,
	        onInit: function( finder ) {
	          finder.on( 'files:choose', function( evt ) {
	            var file = evt.data.files.first();
	            var output = document.getElementById('pic');
	            output.src = file.getUrl();
	            $('#newPic').val(file.getUrl());
	            var image = $('#pic') ;
              	var size = file.attributes.imageResizeData.attributes.originalSize;
              	if (size != '800x800') {
              		output.src = '';
              		alert('图片尺寸必须是800*800')
              	}
	          });
	          finder.on( 'file:choose:resizedImage', function( evt ) {
	            var output = document.getElementById('pic');
	            output.src = evt.data.resizedUrl;
	          });
	        }
	    })
	});
	$(".dataSubmit").on("click",function() {
		var param = $('#formData').serialize();
		th.request("POST", "Brand/edit", param, function(data){
			setTimeout(function(){
	    		var url = window.location.origin;
				window.location.href = url+'/thmartAdmin/Item/Brand/list';
			},1000);
		})
	});
});
