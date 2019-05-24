$(function(){
	$(".deleteButton").on("click",function() {
		var id = $(this).attr('brandId');
		var param = {	
			id: id,
			isDelete: 1
		}
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("post", "Item/Brand/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	});
	$(".skipEdit").on("click",function() {
		var id = $(this).attr('brandId');
		window.location.href = "/thmartAdmin/Item/Brand/detail?id="+id;
	});
});
