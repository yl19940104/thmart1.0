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
	            $('#addPic').val(file.getUrl());
	          });
	          finder.on( 'file:choose:resizedImage', function( evt ) {
	            var output = document.getElementById('pic');
	            output.src = evt.data.resizedUrl;
	          });
	        }
	    })
	});
	$("#ckfinder-popup-2").on("click",function() {
		CKFinder.popup({
	        chooseFiles: true,
	        width: 800,
	        height: 600,
	        onInit: function( finder ) {
	          finder.on( 'files:choose', function( evt ) {
	            var file = evt.data.files.first();
	            var output = document.getElementById('picTwo');
	            output.src = file.getUrl();
	            $('#editPic').val(file.getUrl());
	          });
	          finder.on( 'file:choose:resizedImage', function( evt ) {
	            var output = document.getElementById('pic');
	            output.src = evt.data.resizedUrl;
	          });
	        }
	    })
	});
	$(".select2").select2();
	$('#myModal').on('show.bs.modal', function (e) {})
	$('#editAds').on('show.bs.modal', function (e) {
		var itemId = $(e.relatedTarget).attr("itemId");
		var order = $(e.relatedTarget).attr("order");
		var adsId = $(e.relatedTarget).attr("adsId");
		var url = $(e.relatedTarget).attr("url");
		var pic = $(e.relatedTarget).attr("pic");
		var merchantId = $(e.relatedTarget).attr("merchantId");
		$("#editItemId").val(itemId).select2();
		$("#editOrder").val(order);
		$("#adsId").val(adsId);
		$("#editUrl").val(url);
		$("#editPic").val(pic);
		$("#editMerchantId").val(merchantId).select2();
		$("body #picTwo").attr('src', pic);
	})
	$('#addData').on('click', function(){
		var param = {
			order : $('#addOrder').val(),
			type : $('#positionType').val(),
			adsPositionId : $('#positionId').val(),
		}
		if ($('#addItemId').val()) param.contentId = $('#addItemId').val();
		if ($('#addPic').val()) param.pic = $('#addPic').val();
		if ($('#addUrl').val()) param.url = $('#addUrl').val();
		if ($('#addMerchantId').val()) param.merchantId = $('#addMerchantId').val();
		th.request("POST", "Ads/edit", param, function(data){
			setTimeout(function(){
				window.location.reload();
			},1000);
		});
	})
	$('#updateData').on('click', function(){
		var param = {
			id : $('#adsId').val(),
			order : $('#editOrder').val(),
			type : $('#positionType').val(),
			adsPositionId : $('#positionId').val(),
		}
		if ($('#editItemId').val()) param.contentId = $('#editItemId').val();
		if ($('#editPic').val()) param.pic = $('#editPic').val();
		if ($('#editUrl').val()) param.url = $('#editUrl').val();
		if ($('#editMerchantId').val()) param.merchantId = $('#editMerchantId').val();
		th.request("POST", "Ads/edit", param, function(data){
			setTimeout(function(){
				window.location.reload();
			},1000);
		});
	})
	$('.deleteOne').on('click', function(){
		var id = $(this).attr('adsId');
		var param = {
			id : id
		};
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("POST", "Ads/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	})
});
