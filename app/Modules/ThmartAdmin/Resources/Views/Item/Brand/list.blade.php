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
        <h1>品牌列表</h1>
      </section>
      <section class="content">
        <div class="editButtom clearfix">
          <a href="/thmartAdmin/Item/Brand/detail"><button id="addStaff" type="button" class="btn btn-primary pull-right">添加</button></a>
        </div>
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>品牌名</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
              @foreach ($compactData['data'] as $v)
                <tr>
                  <td class="col-xs-1">{{$v->name}}</td>
                  <td class="col-xs-1">
                    @if ($v->status == 1)
                      已激活
                    @else
                      未激活
                    @endif
                  </td>
                  <td class="col-xs-1">
                    <button type="button" class="btn btn-primary col-xs-2 skipEdit" brandId="{{$v->id}}" id="editSkip">修改</button>
                    <div class="col-xs-1"></div>
                    @if (in_array(1, $compactData['roleArray']))
                    <button type="button" class="btn btn-primary btn-danger deleteButton col-xs-2" brandId="{{$v->id}}" id='deleteButton'>删除</button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tfoot>
          </table>
        </div>
        <div class="pageStyle">
          {{$compactData['data']->links()}}
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
  <script src="/thmartAdmin/js/api/brandList.js"></script>
</body>
</html>
