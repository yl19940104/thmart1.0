// th商城对象
function thFun(){
    this.host = window.location.host;
}
    
// ajax
thFun.prototype.request = function (method,path,para,backfun,failedfun){
    var self = this;
    var locurl = window.location.host;
    var url = 'http://' + document.domain + '/thmartApi/';
    $.ajax({
        url: url + path,
        type: method,
        data: para,
    })
    .done(function(response) {
        if(response.code == 118){
            layer.alert(response.message, {
              skin: 'layui-layer-molv'
              ,closeBtn: 0
            });
        } else if (response.code == 0){  
            if (!para.notShowLay) {
                layer.alert(response.message, {
                  skin: 'layui-layer-molv'
                  ,closeBtn: 0
                });
            }
        } else if (response.code == 1) {
            if (!para.notShowLay) {
                layer.msg(response.message);
            }
            if(backfun){
                backfun(response);
            }
        }
    })
    .fail(function(data) {
        if(failedfun) {
            failedfun(data);
        }
        else {  
            //alert('数据调用失败，默认错误处理'); 
            /*console.log(data); */
            layer.msg('网络错误'); 
        } 
    });
}
// thFun.prototype.getHost = function() {
//     return  window.location.host;
// }

thFun.prototype.getUrlParam = function (key){
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (key == pair['0']) return pair[1];
    }
}

var th = new thFun();