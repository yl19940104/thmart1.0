$(function(){
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
			th.request("post", "Coupon/deleteCouponSku", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
    })
    $('#excelModal').on('show.bs.modal', function (e) {})
	$('#excelSubmitData').on('click',function(){
		var query = window.location.search.substring(1);
       	var vars = query.split("&");
        var pair = vars[0].split("=");
        var param = {
        	id : pair[1],
        	skuList : excelData
        }
        th.request("post", "Coupon/editSkuList", param, function(data){
        	setTimeout(function(){
                window.location.reload();
            },1000);
		});
	})
});
var wb;//读取完成的数据
var rABS = false; //是否将文件读取为二进制字符串
var excelData;
function importf(obj) {//导入
    if(!obj.files) {
        return;
    }
    var f = obj.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var data = e.target.result;
        if(rABS) {
            wb = XLSX.read(btoa(fixdata(data)), {//手动转化
                type: 'base64'
            });
        } else {
            wb = XLSX.read(data, {
                type: 'binary'
            });
        }
        //wb.SheetNames[0]是获取Sheets中第一个Sheet的名字
        //wb.Sheets[Sheet名]获取第一个Sheet的数据
        excelData = JSON.stringify( XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]) );
    };
    if(rABS) {
        reader.readAsArrayBuffer(f);
    } else {
        reader.readAsBinaryString(f);
    }
}

function fixdata(data) { //文件流转BinaryString
    var o = "",
        l = 0,
        w = 10240;
    for(; l < data.byteLength / w; ++l) o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w, l * w + w)));
    o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w)));
}

