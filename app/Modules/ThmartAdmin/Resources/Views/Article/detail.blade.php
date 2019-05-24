<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="/thmartAdmin/css/bootstrap.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/font-awesome.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/adminlte.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/_all-skins.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/select2.min.css">
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
            <h3 class="box-title">文章</h3>
          </div>
          <form class="form-horizontal" style="margin-top:2%" id="formData" method="post">
            <div class="form-group">
              @if (isset($compactData['id']))
              <input type="hidden" id="supplierId" value="{{$compactData['id']}}">
              @endif
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">文章标题</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="title">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">图片</label>
              <div class="col-xs-12 col-md-6">
                <p class="boxImg"><img id="picUrl" alt=""></p>
                <input type="hidden" id="pic" name="pic" v-model="pic">
                <input type="button" value="浏览" style="border-style: solid;" @click="selectPic">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">描述</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="description">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">内容</label>
              <div class="col-xs-12 col-md-6">
                <textarea id="detail" name="detail" cols="80"></textarea>
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">推荐商品</label>
              <div class="col-xs-12 col-md-6">
                <button type="button" class="btn btn-primary" @click="addItemList">+添加商品</button>
              </div>
            </div>
            <div v-for="(item,index) in itemIdList">
              <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-12 col-md-6">
                  <label class="col-xs-1 control-label">商品</label>
                  <div class="col-md-9">
                    <select class="form-control" id="selectItem" style="width: 100%;" tabindex="-1" aria-hidden="true" v-model="itemIdList[index].id">
                      <option></option>
                      <option v-for="(item2,index2) in itemList" :value="item2.id">@{{item2.title}}</option>
                    </select>
                  </div>
                  <button type="button" class="btn btn-danger" @click="deleteItemList(index)">删除</button>
                </div>
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
                <!-- <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                @if (!in_array('/thmartAdmin/Supplier/list', $compactData['authArray']))
                  @if (isset($compactData['id']))
                  <div class="col-xs-12 col-md-6">
                    <div v-if="is_effective == 0">
                      <button type="button" class="btn btn-default pull-right">审核中</button>
                    </div>
                    <div v-else>
                      <button type="button" class="btn btn-success  pull-right">审核通过</button>
                    </div>
                  </div>
                  @else 
                  <div class="col-xs-12 col-md-6">
                    <button type="button" class="btn btn-primary pull-right" id="submit" @click="submitData">提交</button>
                  </div>
                  @endif
                @else 
                  <div class="col-xs-12 col-md-6">
                    <div v-if="is_effective == 0">
                      <button type="button" class="btn pull-right btn-primary" @click=submitEffective>确认通过审核</button>
                    </div>
                    <div v-else>
                      <button type="button" class="btn btn-success pull-right">审核通过</button>
                    </div>
                  </div>
                @endif -->
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-6">
                  <button type="button" class="btn btn-primary pull-right" @click="submitData">提交</button>
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
  <script src="/thmartAdmin/js/daterangepicker.js"></script>
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/vue.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/articleDetail.js"></script>
  <script src="/thmartAdmin/plug/ckeditor/ckeditor.js"></script>
  <script src="/Public/plug/ckfinder/ckfinder.js"></script>
  <script type="text/javascript">
    $(function () {
      CKEDITOR.replace('detail');
      $(".select2").select2();
    });
  </script>
</body>
</html>
