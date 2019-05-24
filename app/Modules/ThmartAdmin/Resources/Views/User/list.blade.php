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
        <h1>用户管理</h1>
      </section>
      <section class="content">
        <div class="editButtom clearfix">
          <a href="/thmartAdmin/User/edit"><button id="addStaff" type="button" class="btn btn-primary pull-right">添加</button></a>
        </div>
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>用户名</th>
              <th>管理组</th>
              <th>绑定供应商</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($compactData['userList']['data'] as $v)
              <tr>
                <td class="col-xs-3">{{$v['username']}}</td>
                <td class="col-xs-3">
                  @foreach ($v['role'] as $value)
                    <small class="label pull-left" style="background: #ccc; color: #444">{{$value['roleName']}}</small></br>
                  @endforeach
                </td>
                <td class="col-xs-3">
                  @foreach ($v['supplierList'] as $value)
                    <small class="label pull-left" style="background: #ccc; color: #444">{{$value['supplier_name']}}</small></br>
                  @endforeach
                </td>
                <td class="col-xs-3">
                  <a href="/thmartAdmin/User/edit?id={{$v['id']}}"><button type="button" class="btn btn-primary pull-left col-lg-2 col-xs-12">修改</button></a>
                  <div class="col-xs-1"></div>
                  <button type="button" class="btn btn-primary pull-left col-lg-3 col-xs-12" data-toggle="modal" data-target="#myModal" userId="{{$v['id']}}">重置密码</button>
                  <div class="col-xs-1"></div>
                  <button type="button" class="btn btn-primary btn-danger deleteButton col-lg-2 col-xs-12" staffId="{{$v['id']}}" id='deleteButton'>删除</button>
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
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
              &times;
            </button>
            <h4 class="modal-title" id="myModalLabel">
              重置密码
            </h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" id="formData" style="margin-top:2%">
              <div class="form-group">
                <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">新密码</label>
                <div class="col-xs-12 col-md-6">
                  <input type="text" class="form-control" name="password" placeholder="new password" @if (isset($compactData['data'])) value="{{$compactData['data']['0']->username}}" @endif>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>
                <button type="button" class="btn btn-primary" id="submit">
                  提交
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
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
  <script src="/thmartAdmin/js/api/userList.js"></script>
</body>
</html>
