var app = new Vue({
    el: '#app',
    data: {
        picList: [],
        username: '',
        comment: '',
        seen: true,
        see: false,
        id : '',
        number : '',
        reply : '',
    },
    mounted(){
        $(".select2").select2();
        var that = this;
        var id = th.getUrlParam('commentId');
        if (id) {
            this.seen = false;
            this.see = true;
            this.id = id;
            var param = {
                id : id,
                notShowLay : 1,
            }
            th.request("POST", "Comment/detail", param, function(data){
                that.comment = data.data[0].info.comment;
                $("#itemId").val(data.data[0].goodsId).select2();
                that.picList = data.data[0].picList;
                that.reply = data.data[0].info.reply;
                that.number = data.data[0].info.number;
                var param2 = {
                    id:data.data[0].goodsId,
                    notShowLay:true
                }
                th.request("POST", "Item/Prop/list", param2, function(res){
                    var prop = res.data;
                    var str= "<option></option>";
                    for (var i = 0; i < prop.length; i++) {
                        if (data.data['0'].info.skuId == prop[i].skuId) {
                            str += '<option selected="selected" value="'+ prop[i].skuId +'">'+ prop[i].value +'</option>';
                        } else {
                            str += '<option value="'+ prop[i].skuId +'">'+ prop[i].value +'</option>';
                        }
                    }
                    $('#prop').html(str);
                },function(err){
                    console.log(err);
                });
            });
        }
    },
    methods: {
        //获取评论图片
        selectPicList(index){
            var that = this;
            CKFinder.popup({
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function( finder ) {
                    finder.on( 'files:choose', function( evt ) {
                        var file = evt.data.files.first();
                        var param = window.location.origin;
                        str = file.getUrl();
                        that.picList.push(str);
                    });
                    finder.on( 'file:choose:resizedImage', function( evt ) {
                    });
                }
            })
        },
        //删除评论图片
        deletePicList(index){
            this.picList.splice(index,1);
        },
        submitData(){
            var param = {
                'id' : this.id,
                'goodsId' : $('#itemId').val(),
                'picList' : this.picList,
                'comment' : this.comment,
                'username' : this.username,
                'skuId' : $('#prop').val(),
                'number' : this.number,
                'reply' : this.reply,
            }
            th.request("POST", "Comment/edit", param, function(data){
                setTimeout(function(){
                    window.location.href = document.referrer;
                },1000);
            });
        },
        changeStatus(index){
            var param = {
                'id' : this.id,
                'audited' : index,
            }
            th.request("POST", "Comment/changeStatus", param, function(data){
                setTimeout(function(){
                    window.location.href = document.referrer;
                },1000);
            });
        },
    },
})
$(function(){
    $("#itemId").on("change",function(e){
        var id = $("#itemId").val();
        var param = {
            id:id,
            notShowLay:true
        }
        th.request("POST", "Item/Prop/list", param, function(data){
            var prop = data.data;
            var str= "<option></option>";
            for (var i = 0; i < prop.length; i++) {
                str += '<option value="'+ prop[i].skuId +'">'+ prop[i].value +'</option>';
            }
            $('#prop').html(str);
        },function(err){
            console.log(err);
        });
    })
})