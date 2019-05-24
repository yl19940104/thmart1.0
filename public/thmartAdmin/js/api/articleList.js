$(function(){
	$(".deleteButton").on("click",function() {
		var param = {
			id : $(this).attr('articleId')
		}
		layer.confirm('是否要删除？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("post", "Article/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	});
});
