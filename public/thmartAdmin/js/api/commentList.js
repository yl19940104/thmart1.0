$(function(){
    $('.editComment').on("click", function(){
        $(this).attr('commentId') ? param = "?commentId=" + $(this).attr('commentId') : param = '';
        window.location.href = "/thmartAdmin/Comment/edit" + param;
    })
    $('#searchComment').on("click",function() {
        var param = $('#formData').serialize();
        var url = window.location.origin;
        window.location.href = url + '/thmartAdmin/Comment/list?' + param;
    })
    $('.deleteComment').on("click",function() {
        var id = $(this).attr('commentId');
        param = {
            id : id
        };

        layer.confirm('是否要删除？', {
            btn: ['是','否'] //按钮
        }, function() {
            th.request("POST", "Comment/delete", param, function (data) {
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            });
        })
    })
})