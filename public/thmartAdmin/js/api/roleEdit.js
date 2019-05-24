$(function(){
	$("#submit").on("click",function() {
		/*var a = $('#formData input[type=checkbox]:checked').fieldValue();
		console.log(a)*/
		var $id = $('#roleId').val();
		var $roleName = $('#roleName').val();
		var arrElement = $(".checkbox input[type=checkbox]:checked");
		var arrId = [];
		for (var i = 0; i < arrElement.length; i++) {
			arrId.push($(arrElement[i]).val());
		}
		var param = {
			id: $id,
			roleName : $roleName,
			authNameIdArray : arrId
		}
		th.request("POST", "Staff/Role/edit", param, function(data){
			setTimeout(function(){
				window.location.href = document.referrer;
			},1000);
		});
	});
});
