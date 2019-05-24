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
        <h1>配置位置列表</h1>
      </section>
      <section class="content">
        <div class="box-body">
          <button class="btn btn-primary clearHomepageData" data-toggle="modal">清空首页缓存</button>
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>配置位置</th>
              <th>模块</th>
              <th>状态</th>
            </tr>
            </thead>
            <tbody>
              @foreach ($compactData['setPositionData'] as $v)
                <tr>
                  <td class="col-xs-1">{{$v->name}}</td>
                  <td class="col-xs-1">{{$v->modules}}</td>
                  <td class="col-xs-1">
                  @if ($v->status == 1)
                    开启
                  @else
                    关闭
                  @endif
                  </td>
                </tr>
              @endforeach
            </tfoot>
          </table>
        </div>
        <div class="pageStyle">
          {{$compactData['setPositionData']->appends(request()->all())->links()}}
        </div>
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
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/adsPosition.js"></script>
</body>
</html>
