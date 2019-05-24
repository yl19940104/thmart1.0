$(function(){
	var id;
	$(".deleteButton").on("click",function() {
		var id = $(this).attr('staffId');
		var param = {	
			id: id,
			isDelete: 1
		}
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("post", "Supplier/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	});
	$('#myModal').on('show.bs.modal', function (e) {
		id = $(e.relatedTarget).attr("userId");
	})
	$('#submit').on("click",function() {
		var param = $('#formData').serialize() + '&id=' + id;
		th.request("post", "Staff/resetPassword", param, function(data){
			setTimeout(function(){
				window.location.reload();
			},1000);
		});
	})
});
