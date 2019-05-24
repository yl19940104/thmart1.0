<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="/thmartAdmin/css/bootstrap.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/font-awesome.min.css">
  @include('thmartAdmin::Common.css')
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
      <section class="content" id="app">
        <div>
          <div class="box-header with-border">
            <h3 class="box-title">用户管理编辑</h3>
          </div>
          <form class="form-horizontal" style="margin-top:2%">
            <div class="form-group">
              @if (isset($compactData['data']))
                <input type="hidden" name='id' value="{{$compactData['data']['0']->id}}" id="staffId">
              @endif
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">用户名</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" @if (isset($compactData['data'])) value="{{$compactData['data']['0']->username}}" @endif>
              </div>
            </div>
            <div class="form-group">
              @if (!isset($compactData['data']))
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">密码</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" id="password" name="password" placeholder="password" @if (isset($compactData['data'])) value="{{$compactData['data']['0']->username}}" @endif>
              </div>
              @endif
            </div>
            <div class="form-group">
              <label for="inputPassword3" class="col-xs-12 col-md-3 control-label">组别</label>
              <div class="col-sm-12 col-md-6">
                <div class="checkbox">
                  @if (!isset($compactData['data']))
                    @foreach ($compactData['roleGroup'] as $v) 
                      <div class="col-md-3"><label><input type="checkbox" name="checkRole" value="{{$v->id}}">{{$v->roleName}}</label></div>
                    @endforeach
                  @else
                    @foreach ($compactData['roleGroup'] as $v) 
                      @if (in_array($v->id, $compactData['staffRoleGroup']))
                        <div class="col-md-3"><label><input type="checkbox" name="checkRole" value="{{$v->id}}" checked="checked">{{$v->roleName}}</label></div>
                      @else
                        <div class="col-md-3"><label><input type="checkbox" name="checkRole" value="{{$v->id}}">{{$v->roleName}}</label></div>
                      @endif
                    @endforeach
                  @endif
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">绑定供应商</label>
              <div class="col-xs-12 col-md-6">
                <button type="button" class="btn btn-primary" @click="addSupplierList">+添加</button>
              </div>
            </div>
            <div v-for="(supplier,index) in supplierList">
              <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-12 col-md-6">
                  <label class="col-xs-2 control-label">供应商</label>
                  <div class="col-md-8">
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" v-model="supplierList[index].id">
                      <option></option>
                      @foreach ($compactData['supplierList'] as $v)
                        <option @click="addSupplier" value="{{$v->id}}">{{$v->supplier_name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <button type="button" class="btn btn-danger" @click="deleteSupplierList(index)">删除</button>
                </div>
              </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-12 col-md-6">
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
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/vue.js"></script>
  @include('thmartAdmin::Common.js')
  <script src="/thmartAdmin/js/api/userEdit.js"></script>
</body>
</html>
