$(function(){
	$("#submit").on("click",function() {
		var param = $('#formData').serialize();
		th.request("POST", "Staff/Auth/edit", param, function(data){
			setTimeout(function(){
				window.location.href = document.referrer;
			},1000);
		});
	});
});
