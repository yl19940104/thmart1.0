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
          <div class="box-header with-border">
            <h3 class="box-title">品牌</h3>
          </div>
          <form class="form-horizontal" style="margin-top:2%" id="formData" method="post">
            <div class="form-group">
              @if (isset($compactData['data']['0']->id))
                <input type="hidden" id="id" name="id" value="{{$compactData['data']['0']->id}}">
              @endif
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">品牌英文名</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" id="name" name="name" @if (isset($compactData['data']['0']->name)) value="{{$compactData['data']['0']->name}}" @endif>
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">品牌中文名</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" id="name_cn" name="name_cn" @if (isset($compactData['data']['0']->name_cn)) value="{{$compactData['data']['0']->name_cn}}" @endif>
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">品牌图片(800*800,白底)</label>
              <div class="col-xs-12 col-md-6">
                <p class="boxImg"><img id="pic" alt="" @if (isset($compactData['data']['0']->pic)) src="{{$compactData['data']['0']->pic}}" @endif></p>
                <input type="hidden" id="newPic" name="pic" @if (isset($compactData['data']['0']->pic)) value="{{$compactData['data']['0']->pic}}" @endif>
                <input type="button" value="浏览" id="ckfinder-popup-1" style="border-style: solid;">
              </div>
            </div>
            <div class="col-xs-12 col-md-6">
              <!-- <div>
                <button type="button" class="btn pull-right btn-primary">确认通过审核</button>
              </div> -->
              <div>
                <button type="button" class="btn btn-primary pull-right dataSubmit">提交</button>
              </div>
            </div>
          </form>
      </section>
    </div>
  </div>
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/jquery.form.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/moment.js"></script>
  <script src="/thmartAdmin/js/daterangepicker.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/vue.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/brandEdit.js"></script>
  <script src="/thmartAdmin/plug/ckeditor/ckeditor.js"></script>
  <script src="/Public/plug/ckfinder/ckfinder.js"></script>
  <script type="text/javascript"></script>
</body>
</html>
