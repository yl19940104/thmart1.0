$(function(){
	$(".select2").select2();
	var id;
	$(".deleteButton").on("click",function() {
		var id = $(this).attr('itemId');
		var param = {	
			id: id
		}
		layer.confirm('是否要删除？', {
		  	btn: ['是', '否'] //按钮
		}, function(){
			th.request("post", "Item/delete", param, function(data){
				setTimeout(function(){
					window.location.reload();
				},1000);
			});
		});
	});
	$('#searchItem').on("click",function() {
		var param = $('#formData').serialize();
		var url = window.location.origin;
		window.location.href = url + '/thmartAdmin/Item/list?' + param;
	})
	//全选与取消全选
    $('#allChecked').on('click',function(){
        if ($('#allChecked')[0].checked) {
            $('.check').each(function(){
                $(this)[0].checked = true;
            })
        } else {
            $('.check').each(function(){
                $(this)[0].checked = false;
            })
        }
    })
    $('.selectPage').change(function(){
        console.log(123)
        var pageSize = $('.selectPage').val();
        var url = window.location.search;
        if (Boolean(url)) {
            var urlJson = queryURL(url);
            urlJson['pageSize'] = pageSize;
            var finalUrl = '?';
            for (var key in urlJson) {
                finalUrl += key + '=' +urlJson[key]+ '&';
            }
            var param = finalUrl.substring(0, finalUrl.length - 1);
            window.location.href = "/thmartAdmin/Item/list" + param;
        } else {
            window.location.href = "/thmartAdmin/Item/list?pageSize=" + pageSize;
        }
    })
    function queryURL(url){
        var arr1 = url.split("?");
        var params = arr1[1].split("&");
        var obj = {};//声明对象
        for(var i=0;i<params.length;i++){
            var param = params[i].split("=");
            obj[param[0]] = param[1];//为对象赋值
        }
        return obj;
    }
});
var jsono = [];
var tmpDown; //导出的二进制对象
function downloadExl(json, type) {
    var idArray = [];
    $("input[type='checkbox']:checked").each(function(index,element){   //jquery的each方法遍历所有匹配元素
        idArray[index] = $(this).attr('value');
    })
    if (!idArray[0]) idArray.shift();
    var param = {
        idArray : idArray,
    }
    th.request("post", "Item/excel", param, function(data){
        json = data.data;
        var tmpdata = json[0];
        json.unshift({});
        var keyMap = []; //获取keys
        for (var k in tmpdata) {
            keyMap.push(k);
            json[0][k] = k;
        }
        var tmpdata = [];//用来保存转换好的json 
        json.map((v, i) => keyMap.map((k, j) => Object.assign({}, {
            v: v[k],
            position: (j > 25 ? getCharCol(j) : String.fromCharCode(65 + j)) + (i + 1)
        }))).reduce((prev, next) => prev.concat(next)).forEach((v, i) => tmpdata[v.position] = {
            v: v.v,
        });
        
        var outputPos = Object.keys(tmpdata); //设置区域,比如表格从A1到D10
        for (var i = 1; i < json.length+1; i++) {
            tmpdata['I'+ i].t = 'n';
            tmpdata['J'+ i].t = 'n';
            tmpdata['K'+ i].t = 'n';
        }
        // return false;
        var tmpWB = {
            SheetNames: ['mySheet'], //保存的表标题
            Sheets: {
                'mySheet': Object.assign({},
                    tmpdata, //内容
                    {
                        '!ref': outputPos[0] + ':' + outputPos[outputPos.length - 1] //设置填充区域
                    })
            }
        };
        tmpDown = new Blob([s2ab(XLSX.write(tmpWB, 
            {bookType: (type == undefined ? 'xlsx':type),bookSST: false, type: 'binary'}//这里的数据是用来定义导出的格式类型
            ))], {
            type: ""
        }); //创建二进制对象写入转换好的字节流
        var href = URL.createObjectURL(tmpDown); //创建对象超链接
        document.getElementById("hf").href = href; //绑定a标签
        document.getElementById("hf").click(); //模拟点击实现下载
        setTimeout(function() { //延时释放
            URL.revokeObjectURL(tmpDown); //用URL.revokeObjectURL()来释放这个object URL
        }, 100);
    });
}

function s2ab(s) { //字符串转字符流
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}
 // 将指定的自然数转换为26进制表示。映射关系：[0-25] -> [A-Z]。
function getCharCol(n) {
    let temCol = '',
    s = '',
    m = 0
    while (n > 0) {
        m = n % 26 + 1
        s = String.fromCharCode(m + 64) + s
        n = (n - m) / 26
    }
    return s
}
