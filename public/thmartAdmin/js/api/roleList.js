$(function(){
	$(".deleteButton").on("click",function() {
		var id = $(this).attr('roleId');
		var param = {	
			id: id,
			isDelete: 1
		}
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("post", "Staff/Role/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	});
});
