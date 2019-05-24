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
        <!-- <h1>用户管理编辑</h1> -->
      </section>
      <section class="content">
        <div>
          <div class="box-header with-border">
            <h3 class="box-title">角色管理编辑</h3>
          </div>
          <form class="form-horizontal" style="margin-top:2%" id="formData" method="post">
            @if (isset($compactData['data']))
              <input type="hidden" name='id' value="{{$compactData['data']['0']['id']}}" id="roleId">
            @endif
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">角色名</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" name="roleName" placeholder="roleName" id="roleName" @if (isset($compactData['data'])) value="{{$compactData['data']['0']['roleName']}}" @endif>
              </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label">权限</label>
                <div class="col-xs-12 col-md-6">
                  <div class="checkbox">
                    @foreach ($compactData['auth'] as $v)
                      @if (isset($compactData['data']))
                        @if (in_array($v->id, $compactData['data']['0']['authIdArray']))
                          <div class="col-md-3"><label><input type="checkbox" name="checkAuth" value="{{$v->id}}" checked="checked">{{$v->authName}}</label></div>
                        @else
                          <div class="col-md-3"><label><input type="checkbox" name="checkAuth" value="{{$v->id}}">{{$v->authName}}</label></div>
                        @endif
                      @else 
                        <div class="col-md-3"><label><input type="checkbox" name="checkAuth" value="{{$v->id}}">{{$v->authName}}</label></div>
                      @endif
                    @endforeach
                  </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-12 col-md-6">
                  <button type="button" class="btn btn-primary pull-right" id="submit">提交</button>
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
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/jquery.form.js"></script>
  <script src="/thmartAdmin/js/api/roleEdit.js"></script>
</body>
</html>
