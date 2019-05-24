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
        <h1>文章列表</h1>
      </section>
      <section class="content">
        <div class="editButtom clearfix">
          <a href="/thmartAdmin/Article/detail"><button type="button" class="btn btn-primary pull-right">添加</button></a>
        </div>
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>id</th>
              <th>文章标题</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($compactData['data'] as $v)
              <tr>
                <td class="col-xs-3">{{$v->id}}</td>
                <td class="col-xs-3">{{$v->title}}</td>
                <td class="col-xs-3">
                  <a href="/thmartAdmin/Article/detail?id={{$v->id}}"><button type="button" class="btn btn-primary pull-left col-xs-3">编辑</button></a>
                  <div class="col-xs-1"></div>
                  <button type="button" class="btn btn-primary btn-danger deleteButton col-xs-3" articleId="{{$v->id}}">删除</button>
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
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/jquery.form.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/moment.js"></script>
  <script src="/thmartAdmin/js/daterangepicker.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/articleList.js"></script>
</body>
</html>
