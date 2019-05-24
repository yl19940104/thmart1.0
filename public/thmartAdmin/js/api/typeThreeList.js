$(function(){
    $(".select2").select2();
    $(".deleteButton").on("click",function() {
        var id = $(this).attr('saleId');
        var arrayData = [];
        arrayData.push(id);
        var param = {
            array: arrayData,
        }
        layer.confirm('是否要删除？', {
            btn: ['是','否'] //按钮
        }, function(){
            th.request("post", "ItemSale/delete", param, function(data){
                setTimeout(function(){
                    window.location.reload();
                },1000);
            });
        });
    });
    $(".searchButton").on("click", function() {
        var param = $('#formData').serialize();
        window.location.href = "/thmartAdmin/Sale/typeThreeList?"+param;
    })
    $('#myModal').on('show.bs.modal', function (e) {
        var skuNumber = $(e.relatedTarget).attr("skuNumber");
        var price = $(e.relatedTarget).attr("price");
        var goodsName = $(e.relatedTarget).attr("goodsName");
        var salePrice = $(e.relatedTarget).attr("salePrice");
        var startTime = $(e.relatedTarget).attr("startTime");
        var endTime = $(e.relatedTarget).attr("endTime");
        var saleEditId = $(e.relatedTarget).attr("saleEditId");
        var amount = $(e.relatedTarget).attr("amount");
        $('#skuNumber').val(skuNumber);
        $('#price').val(price);
        $('#goodsName').val(goodsName);
        $('#salePrice').val(salePrice);
        $('#startTime').val(startTime);
        $('#endTime').val(endTime);
        $('#saleEditId').val(saleEditId);
        $('#amount').val(amount);
    })
    $('#submitData').on("click", function() {
        var param = {
            array : [
                {
                    id : $('#saleEditId').val(),
                    startTime : $('#startTime').val(),
                    endTime : $('#endTime').val(),
                    skuNumber : $('#skuNumber').val(),
                    salePrice : $('#salePrice').val(),
                    rule : $('#typeValue').val(),
                    amount : $('#amount').val()
                }
            ],
            type : 3
        }
        th.request("post", "ItemSale/edit", param, function(data){
            setTimeout(function(){
                window.location.reload();
            },1000);
        });
    })
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
            array : idArray
        }
        layer.confirm('是否要删除？', {
            btn: ['是','否'] //按钮
        }, function(){
            th.request("post", "ItemSale/delete", param, function(data){
                setTimeout(function(){
                    window.location.reload();
                },1000);
            });
        });
    })

    $('#excelModal').on('show.bs.modal', function (e) {})

    $('#selectExcel').on('click',function(){
        CKFinder.popup( {
            chooseFiles: true,
            width: 800,
            height: 600,
            onInit: function( finder ) {
                finder.on( 'files:choose', function( evt ) {
                    var file = evt.data.files.first();
                    var output = document.getElementById('excelName');
                    output.value = file.getUrl();
                } );

                finder.on( 'file:choose:resizedImage', function( evt ) {
                    var output = document.getElementById('excelName');
                    output.value = evt.data.resizedUrl;
                } );
            }
        });
    })
    $('#excelSubmitData').on('click', function(data){
        var param = {
            excelData : excelData,
            type : 3
        }
        th.request("post", "ItemSale/excelEdit", param, function(data){
            window.location.reload();
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

