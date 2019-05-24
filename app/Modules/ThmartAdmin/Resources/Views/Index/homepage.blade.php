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
        <h1>系统信息</h1>
        <ol class="breadcrumb">
          <!-- <li><a href="#">首页</a></li>
          <li class="active">促销管理</li>
          <li class="active">促销价设置</li> -->
        </ol>
      </section>
      <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
              <tr>
                <th>名称</th>
                <th>值</th>
              </tr>
              <tr>
                <th>服务器名称及IP地址</th>
                <th>{{$compactData['host']}}({{$compactData['ip']}})</th>
              </tr>
              <tr>
                <td>系统及PHP版本</td>
                <td>{{$compactData['system']}}/PHP v{{$compactData['phpVersion']}}</td>
              </tr>
              <tr>
                <td>WEB服务器</td>
                <td>{{$compactData['software']}}</td>
              </tr>
              <tr>
                <td>上传</td>
                <td>{{$compactData['upload']}}</td>
              </tr>
              <tr>
                <td>服务器时间</td>
                <td>{{$compactData['time']}}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </section>
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/moment.js"></script>
  <script src="/thmartAdmin/js/daterangepicker.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
</body>
</html>
