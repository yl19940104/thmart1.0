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
        <h1>角色管理</h1>
      </section>
      <section class="content">
        <div class="editButtom clearfix">
          <a href="/thmartAdmin/User/Role/edit"><button id="addStaff" type="button" class="btn btn-primary pull-right">添加</button></a>
        </div>
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>角色</th>
              <th>权限</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($compactData['list']['data'] as $v)
              <tr>
                <td class="col-xs-4">{{$v['roleName']}}</td>
                <td class="col-xs-4">
                  @foreach ($v['authArray'] as $value)
                    <small class="label pull-left" style="background: #ccc; color: #444">{{$value->authName}}</small></br>
                  @endforeach
                </td>
                <td class="col-xs-4">
                  <a href="/thmartAdmin/User/Role/edit?id={{$v['id']}}"><button type="button" class="btn btn-primary pull-left">修改</button></a>
                  <button type="button" class="btn btn-primary btn-danger deleteButton" roleId="{{$v['id']}}" id='deleteButton' style="margin-left: 5%">删除</button>
                </td>
              </tr>
            @endforeach
            </tfoot>
          </table>
        </div>
        <div class="pageStyle">
          {{$compactData['page']->links()}}
        </div>
      </section>
    </div>
  </div>
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/moment.js"></script>
  <script src="/thmartAdmin/js/daterangepicker.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/roleList.js"></script>
</body>
</html>
