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
    <link rel="stylesheet" href="/thmartAdmin/css/style.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    @include('thmartAdmin::Common.top')
    @include('thmartAdmin::Common.left')
    <div class="content-wrapper">
        <section class="content-header">
        </section>
        <section class="content" id="app">
            <div>
                <div class="box-header with-border">
                    <h3 class="box-title">评论</h3>
                </div>
                <div class="form-horizontal" style="margin-top:2%" id="formData" method="post">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商品</label>
                        <div class="col-xs-12 col-md-6">
                            <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="itemId">
                                <option selected="selected"></option>
                                @foreach ($compactData['item'] as $v)
                                    <option value="{{$v['id']}}">{{$v['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">规格</label>
                        <div class="col-xs-12 col-md-6">
                            <select class="form-control" style="width: 100%;" id="prop">

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group" v-if="seen">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">昵称</label>
                        <div class="col-xs-12 col-md-6">
                            <input type="text" class="form-control" v-model="username">
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">购买数量</label>
                        <div class="col-xs-12 col-md-6">
                            <input type="text" class="form-control" v-model="number">
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">图片</label>
                        <div class="col-xs-12 col-md-6">
                            <p class="boxImg" v-for="(item,index) in picList"><img id="pic" alt="" :src="item" @click="deletePicList(index)"></p>
                            <input type="button" value="浏览" id="ckfinder-popup" style="border-style: solid;" @click="selectPicList(index)">
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">评论</label>
                        <div class="col-xs-12 col-md-6">
                            <textarea class="form-control" style="height: 200px;" v-model="comment"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">回复评论</label>
                        <div class="col-xs-12 col-md-6">
                            <textarea class="form-control" style="height: 200px;" v-model="reply"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="clearfix">
                        <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                        <div class="col-xs-6">
                            <button type="button" class="btn btn-primary pull-right" @click="submitData">提交</button>
                            <button type="button" v-if="see" class="btn btn-danger pull-right" @click="changeStatus(0)">隐藏</button>
                            <button type="button" v-if="see" class="btn btn-success pull-right" @click="changeStatus(1)">显示</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
<script src="/thmartAdmin/js/bootstrap.min.js"></script>
<script src="/thmartAdmin/js/moment.js"></script>
<script src="/thmartAdmin/js/select2.full.min.js"></script>
<script src="/thmartAdmin/js/fastclick.js"></script>
<script src="/thmartAdmin/js/app.min.js"></script>
<script src="/thmartAdmin/js/vue.js"></script>
<script src="/thmartAdmin/js/layer.js"></script>
<script src="/thmartAdmin/js/htmlTemplate.js"></script>
<script src="/thmartAdmin/js/api/common.js"></script>
<script src="/thmartAdmin/js/api/commentEdit.js"></script>
<script src="/Public/plug/ckfinder/ckfinder.js"></script>
</body>
</html>
