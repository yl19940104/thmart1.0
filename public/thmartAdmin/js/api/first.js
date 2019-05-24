$(function(){
	$(".select2").select2();
	$(".changeCatTwo").change(function(){
    	var fname = $(".changeCatTwo").val();
    	var param = {
    		fname:fname,
    		notShowLay:true
    	}
    	th.request("POST", "Category/list", param, function(data){
	        var catTwoArr = data.data;
	        var str= "<option></option>";
	        for (var i = 0; i < catTwoArr.length; i++) {
	          	str += '<option value="'+ catTwoArr[i].id +'">'+ catTwoArr[i].title_cn +'</option>';
	        }
	        $('.catTwo').html(str);
		},function(err){
			console.log(err);
		});
		if (fname != 160) {
			var str = '';
			$('.categoryThree').html(str);
		}
    });
    
    $("#nextStep").on("click",function() {
    	var supplier = $("#supplier").val();
    	var categoryOne = $("#categoryOne").val();
    	var categoryTwo = $("#categoryTwo").val();
		var categoryThree = $("#categoryThree").val();
		if (categoryTwo != 161) {
			if (!supplier || !categoryOne || !categoryTwo) {
				layer.alert('请填写完整信息', {
					skin: 'layui-layer-molv'
					,closeBtn: 0
				});
			} else {
				window.location.href = "/thmartAdmin/Item/second?supplier="+supplier+"&categoryOne="+categoryOne+"&categoryTwo="+categoryTwo;
			}
		} else if (categoryTwo == 161)  {
			if (!supplier || !categoryOne || !categoryTwo || !categoryThree) {
				layer.alert('请填写完整信息', {
					skin: 'layui-layer-molv'
					,closeBtn: 0
				});
			} else {
				window.location.href = "/thmartAdmin/Item/second?supplier="+supplier+"&categoryOne="+categoryOne+"&categoryTwo="+categoryTwo+"&itemId=&categoryThree="+categoryThree;
			}
		}
    	/*if (!supplier || !categoryOne || !categoryTwo) {
    		layer.alert('请填写完整信息', {
              skin: 'layui-layer-molv'
              ,closeBtn: 0
            });
    	} else {
    		window.location.href = "/thmartAdmin/Item/second?supplier="+supplier+"&categoryOne="+categoryOne+"&categoryTwo="+categoryTwo;
    	}*/
    });

	$(".catTwo").change(function(){
		var fname = $(".catTwo").val();
		if (fname == 161) {
			var param = {
				fname : fname,
				notShowLay : true
			}
			th.request("POST", "Category/list", param, function(data){
				var catTwoArr = data.data;
				var str= "<option></option>";
				for (var i = 0; i < catTwoArr.length; i++) {
					str += '<option value="'+ catTwoArr[i].id +'">'+ catTwoArr[i].title_cn +'</option>';
				}
				var str2 = "<label>三级分类</label><select class=\"form-control\" style=\"width: 100%;\" tabindex=\"-1\" aria-hidden=\"true\" id=\"categoryThree\">"+ str + "</select>";
				$('.categoryThree').html(str2);
			},function(err){
				console.log(err);
			});
		} else {
			var str = '';
			$('.categoryThree').html(str);
		}
	});
});
