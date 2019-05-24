$(function(){
	$("#submit").on("click",function() {
		var username = $("#username").val();
		var password = $("#password").val();
		var param = {	
			username: username,
			password: password
		}
		th.request("POST", "Staff/login", param, function(data){
			setTimeout(function(){
				window.location.href = '/thmartAdmin/homepage';
			},1000);
		},function(err){
			console.log(err);
		});
	});
	document.onkeydown = function(e){
        var ev = document.all ? window.event : e;
        if (ev.keyCode == 13){
            $("#submit").click();
        }
    }
});
