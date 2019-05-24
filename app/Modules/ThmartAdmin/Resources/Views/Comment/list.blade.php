<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/thmartAdmin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/thmartAdmin/css/font-awesome.min.css">
    <link rel="stylesheet" href="/thmartAdmin/css/select2.min.css">
    <link rel="stylesheet" href="/thmartAdmin/css/adminlte.min.css">
    <link rel="stylesheet" href="/thmartAdmin/css/_all-skins.min.css">
    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.6.0/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="/thmartAdmin/css/style.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    @include('thmartAdmin::Common.top')
    @include('thmartAdmin::Common.left')
    <div class="content-wrapper" id="app">
        <section class="content-header">
            <h1></h1>
        </section>
        <section class="content">
            <div class="editButtom clearfix">
                <form id="formData">
                    <div class="form-group col-xs-12">
                        <div class="form-group col-sm-4 form-horizontal">
                            <label for="inputEmail3" class="col-sm-5 control-label" style="">商品id</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="id" @if (isset($compactData['get']['id'])) value="{{$compactData['get']['id']}}" @endif>
                            </div>
                        </div>
                        <div class="form-group col-sm-4 form-horizontal">
                            <label for="inputEmail3" class="col-sm-5 control-label" style="">商品名称</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="title" @if (isset($compactData['get']['title'])) value="{{$compactData['get']['title']}}" @endif>
                            </div>
                        </div>
                        <div class="form-group col-sm-4 form-horizontal">
                            <label for="inputEmail3" class="col-sm-5 control-label" style="">状态</label>
                            <div class="col-sm-7">
                                <select class="form-control" name="audited">
                                    <option></option>
                                    <option value="1" @if (isset($compactData['get']['audited']) && $compactData['get']['audited'] == 1) selected="seelcted" @endif>显示</option>
                                    <option value="0" @if (isset($compactData['get']['audited']) && $compactData['get']['audited'] == 0) selected="seelcted" @endif>隐藏</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-4 form-horizontal">
                            <label for="inputEmail3" class="col-sm-5 control-label"></label>
                            <div class="col-sm-4">
                                <input type="button" class="form-control" id="searchComment" value="搜索">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="editButtom clearfix">
                <button type="button" class="btn btn-primary pull-right editComment">添加评论</button>
            </div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>商品名称</th>
                        <th>用户昵称</th>
                        <th>图片</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($compactData['data'] as $v)
                            <tr>
                                <td class="col-xs-1">
                                    @if (isset($v['info']['title']))
                                      {{$v['info']['title']}}
                                    @endif
                                </td>
                                <td class="col-xs-1">
                                    @if (isset($v['info']['username']))
                                        {{$v['info']['username']}}</td>
                                    @endif
                                <td class="col-xs-1">
                                    @if (isset($v['pic']))
                                        @foreach ($v['pic'] as $value)
                                            <img src="{{$value}}" style="height: 50px; weight: 50px">
                                        @endforeach
                                    @endif
                                </td>
                                <td class="col-xs-1">
                                    @if ($v['audited'] == '1')
                                        显示
                                    @else
                                        隐藏
                                    @endif
                                </td>
                                <td class="col-xs-1">{{$v['created_at']}}</td>
                                <td class="col-xs-1">
                                    <button type="button" class="btn btn-primary col-xs-3 editComment" commentId="{{$v['_id']}}">修改</button>
                                    <button type="button" class="btn btn-primary btn-danger deleteComment col-xs-3" commentId="{{$v['_id']}}">删除</button>
                                </td>
                            </tr>
                        @endforeach
                    </tfoot>
                </table>
            </div>
            <div class="pageStyle">
                {{$compactData['data']->appends(request()->all())->links()}}
            </div>
        </section>
    </div>
</div>
<script src="/thmartAdmin/js/vue.js"></script>
<script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
<script src="/Public/plug/ckfinder/ckfinder.js"></script>
<script src="/thmartAdmin/js/jquery.form.js"></script>
<script src="/thmartAdmin/js/bootstrap.min.js"></script>
<script src="/thmartAdmin/js/moment.js"></script>
<script src="/thmartAdmin/js/daterangepicker.js"></script>
<script src="/thmartAdmin/js/fastclick.js"></script>
<script src="/thmartAdmin/js/app.min.js"></script>
<script src="/thmartAdmin/js/layer.js"></script>
<script src="/thmartAdmin/js/select2.full.min.js"></script>
<script src="/thmartAdmin/js/htmlTemplate.js"></script>
<script src="/thmartAdmin/js/api/common.js"></script>
<script src="/thmartAdmin/js/api/commentList.js"></script>
</body>
</html>

