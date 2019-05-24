$(function(){
	$(".select2").select2();
	$(".deleteButton").on("click",function() {
		var id = $(this).attr('couponId');
		var arrayData = [];
		arrayData.push(id);
		var param = {	
			idArray: arrayData,
		}
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("post", "Coupon/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	});
	$('#ckfinder-popup-1').on('click', function(){
		CKFinder.popup({
	        chooseFiles: true,
	        width: 800,
	        height: 600,
	        onInit: function( finder ) {
	          finder.on( 'files:choose', function( evt ) {
	            var file = evt.data.files.first();
	            var output = document.getElementById('picSelect');
	            output.src = file.getUrl();
	            var param = window.location.origin;
				str = output.src;
				str = str.replace(param, '');
	            $('#picAdd').val(str);
	          });
	          finder.on( 'file:choose:resizedImage', function( evt ) {
	            var output = document.getElementById('ckfinderImage'+index);
	            output.src = evt.data.resizedUrl;
	          });
	        }
	    })
	})
	$(".searchButton").on("click", function() {
		var param = $('#formData').serialize();
		window.location.href = "/thmartAdmin/Sale/typeOneList?"+param;
	})
	$("#addCoupon").on('show.bs.modal', function (e) {})
	$('#myModal').on('show.bs.modal', function (e) {
		var couponName = $(e.relatedTarget).attr("couponName");
		var startTime = $(e.relatedTarget).attr("startTime");
		var endTime = $(e.relatedTarget).attr("endTime");
		var couponId = $(e.relatedTarget).attr("couponId");
		var isOverLay = $(e.relatedTarget).attr("isOverLay");
		var amount = $(e.relatedTarget).attr("amount");
		var couponType = $(e.relatedTarget).attr("couponType");
		var over = $(e.relatedTarget).attr("over");
		var reduce = $(e.relatedTarget).attr("reduce");
		var pic = $(e.relatedTarget).attr("pic");
		if (couponType == 2) $('#amountHidden').css('display', 'none');
		$('#couponName').val(couponName);
		$('#startTime').val(startTime);
		$('#endTime').val(endTime);
		$('#couponId').val(couponId);
		$('#couponAmount').val(amount);
		$('#isOverLay').val(isOverLay);
		$('#couponType').val(couponType);
		$('#over').val(over);
		$('#reduce').val(reduce);
		$('#pic').val(pic);
	})
	$('#submitData').on("click", function() {
		var param = {
			id : $('#couponId').val(),
			startTime : $('#startTime').val(),
	    	endTime : $('#endTime').val(),
			name : $('#couponName').val(),
			amount : $('#couponAmount').val(),
			isOverlay : $('#isOverLay').val(),
			type : $('#couponType').val(),
			over : $('#over').val(),
			reduce : $('#reduce').val(),
			pic : $('#pic').val()
		}
		th.request("post", "Coupon/edit", param, function(data){
			setTimeout(function(){
				window.location.reload();
			},1000);
		});
	})
	$('#addData').on("click", function() {
		var param = {
			startTime : $('#startTimeAdd').val(),
	    	endTime : $('#endTimeAdd').val(),
			name : $('#couponNameAdd').val(),
			amount : $('#couponAmountAdd').val(),
			isOverlay : $('#isOverLayAdd').val(),
			type : $('#selectCouponType').val(),
			over : $('#overAdd').val(),
			reduce : $('#reduceAdd').val(),
			pic : $('#picAdd').val()
		}
		th.request("post", "Coupon/edit", param, function(data){
			setTimeout(function(){
				window.location.reload();
			},1000);
		});

	})
	//全选
	$('#allChecked').on('click',function(){
		$('.check').click();
	})
	//批量删除
	$('#deleteArray').click(function(event) {
    	var idArray = [];
    	$("input[type='checkbox']:checked").each(function(index,element){   //jquery的each方法遍历所有匹配元素
    	    idArray[index] = $(this).attr('value');
   		})
   		var param ={
   			idArray : idArray
   		}
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("post", "Coupon/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
    })
	$('#selectCouponType').change(function(){
		if ($('#selectCouponType').val() == 1) {
			$('#amountShow').show();
			$('#picShow').show();
		} else if($('#selectCouponType').val() == 2) {
			$('#amountShow').hide();
			$('#picShow').hide();
		}	
	})
    $('.skuList').on('click', function(data){
    	var couponId = $(this).attr('couponId');
    	window.location.href = "/thmartAdmin/Coupon/skuList?couponId="+ couponId;
    })
});

