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
        <h1>
          新建商品
        </h1>
      </section>
      <section class="content home-content">
        <form role="form">
          <div class="form-group">
            <label class="control-label">供应商</label>
            <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="supplier">
              <option selected="selected"></option>
              @foreach ($compactData['supplierList'] as $v)
                 <option value="{{$v->id}}">{{$v->supplier_name}}</option>
              @endforeach 
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">一级分类</label>
            <select class="form-control select2 select2-hidden-accessible changeCatTwo" style="width: 100%;" tabindex="-1" aria-hidden="true" id="categoryOne">
              <option selected="selected"></option>
              @foreach ($compactData['catOneList'] as $v)
                <option value="{{$v['id']}}">{{$v['title_cn']}}</option>
              @endforeach 
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">二级分类</label>
            <select class="form-control select2 select2-hidden-accessible catTwo" style="width: 100%;" tabindex="-1" aria-hidden="true" id="categoryTwo">
            </select>
          </div>
          <div class="form-group categoryThree">

          </div>
          <div class="nextStep" id="nextStep">下一步</div>      
        </form>
      </section>
    </div>
    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 2.3.12
      </div>
      <strong>© 2017 SH Aoyang Advertising Co., Ltd. 沪ICP备12038926号-5</strong>
    </footer>
  </div>
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/first.js"></script>
</body>
</html>
