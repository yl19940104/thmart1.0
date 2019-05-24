$(function(){
	$(".clearHomepageData").on("click",function() {
		var param = '';
		layer.confirm('是否要清空首页？', {
		  	btn: ['是','否'] //按钮
		}, function(){
			th.request("POST", "Ads/Home/clearHomepageData", param, function(data){
				
			});
		});
	})
})