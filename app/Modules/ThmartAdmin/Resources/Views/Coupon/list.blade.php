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
        <h1>活动设置</h1>
      </section>
      <section class="content deals">
        <div class="order-form">
          <!-- <form id="formData">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="onlySalePrice" @if (isset($compactData['get']['onlySalePrice']) && $compactData['get']['onlySalePrice'] == 'on') checked="" @endif>仅显示优惠券
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="audited"  @if (isset($compactData['get']['audited']) && $compactData['get']['audited'] == 'on') checked="" @endif>仅显示满减活动
              </label>
            </div>
            <div class="bottomBtn">
              <button type="button" class="btn btn-default searchButton">搜索</button>
            </div>
          </form> -->
        </div>
        <div class="tableBox">
          <button type="button" class="btn btn-default" id="allChecked">全选</button>
          <button type="button" class="btn btn-default" id="deleteArray">删除</button>
          <button type="button" class="btn btn-default" data-toggle="modal" data-target="#addCoupon"  style="float: right; color: grey">+添加活动</button>
          <table class="orderTable">
            <tr>
              <td>选择</td>
              <td>活动名字</td>
              <td>类别</td>
              <td>时间</td>
              <td>优惠门槛</td>
              <td>优惠金额</td>
              <td>剩余数量</td>
              <td>操作</td>
            </tr>
            @if (isset($compactData['data']))
              @foreach ($compactData['data'] as $v)
                <tr>
                  <td>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" class="check" value="{{$v->id}}">
                      </label>
                    </div>
                  </td>
                  <td>{{$v->name}}</td>
                  <td>
                    @if ($v->type == 1)
                      优惠券
                    @else
                      满减
                    @endif
                  </td>
                  <td><p>{{$v->startTime}}</p><p>--</p><p>{{$v->endTime}}</p></td>
                  <td>{{$v->over}}</td>
                  <td>{{$v->reduce}}</td>
                  <td>
                    @if ($v->type == 1)
                      {{$v->amount}}
                    @endif
                  </td>
                  <td>
                    <button class="btn skuList" couponId="{{$v->id}}">白名单</button>
                    <button type="submit" class="btn add" data-toggle="modal" data-target="#myModal" couponName="{{$v->name}}" startTime="{{$v->startTime}}" endTime="{{$v->endTime}}" couponId="{{$v->id}}" amount="{{$v->amount}}" couponType="{{$v->type}}" over="{{$v->over}}" reduce="{{$v->reduce}}" isOverLay="{{$v->isOverLay}}" pic="{{$v->pic}}">修改</button>
                    <button type="submit" class="btn delete deleteButton" couponId="{{$v->id}}">删除</button>
                  </td>
                </tr>
              @endforeach
            @endif
          </table>
        </div>
        @if (isset($compactData['data']))
          <div class="pageStyle">
            {{$compactData['data']->appends(request()->all())->links()}}
          </div>
        @endif
      </section>
      <!-- 模态框（Modal） -->
      <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
              </button>
              <h4 class="modal-title" id="myModalLabel">
                修改活动信息
              </h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="excelData" style="margin-top:2%" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" id="couponId"> 
                <input type="hidden" id="isOverLay"> 
                <input type="hidden" id="type"> 
                <!-- <input type="hidden" id="over"> 
                <input type="hidden" id="reduce">  -->
                <input type="hidden" id="pic"> 
                <input type="hidden" id="couponType"> 
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">活动名字</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="couponName">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">开始时间</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="startTime" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">结束时间</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="endTime" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">优惠门槛</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="over" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">优惠金额</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="reduce" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group" id="amountHidden">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">剩余数量</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="couponAmount">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                  </button>
                  <button type="button" class="btn btn-primary" id="submitData">
                    提交
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- 模态框（Modal） -->
      <div class="modal fade" id="addCoupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
              </button>
              <h4 class="modal-title" id="myModalLabel">
                添加活动信息
              </h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="excelData" style="margin-top:2%" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" id="pic"> 
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">活动类型</label>
                  <div class="col-xs-12 col-md-6">
                    <select class="form-control" id="selectCouponType">
                      <option></option>
                      <option value="1">优惠券</option>
                      <option value="2">满减</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">是否允许叠加</label>
                  <div class="col-xs-12 col-md-6">
                    <select class="form-control" id="isOverLayAdd">
                      <option></option>
                      <option value="0">否</option>
                      <option value="1">是</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">活动名字</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="couponNameAdd">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">优惠门槛</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="overAdd">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">优惠金额</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="reduceAdd">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">开始时间</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="startTimeAdd" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">结束时间</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="endTimeAdd" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group" id="picShow" style="display: none">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">优惠券图片</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="hidden" id="picAdd">
                    <img id="picSelect" style="height: 50px; weight: 50px;">
                    <input type="button" value="浏览" id="ckfinder-popup-1" style="border-style: solid;">
                  </div>
                </div>
                <div class="form-group" style="display: none" id="amountShow">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">剩余数量</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" id="couponAmountAdd">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                  </button>
                  <button type="button" class="btn btn-primary" id="addData">
                    提交
                  </button>
                </div>
              </form>
            </div>
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
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <!-- <script src="/Public/plug/excelCkfinder/ckfinder.js"></script> -->
  <!-- <script type="text/javascript" src="http://api.mall.thatsmags.com/Public/ckfinder/ckfinder.js"></script> -->
  <script src="http://oss.sheetjs.com/js-xlsx/xlsx.full.min.js"></script>
  <script src="/thmartAdmin/js/api/couponList.js"></script>
  <script src="/thmartAdmin/js/xlsx.full.min.js"></script>
  <script src="/Public/plug/ckfinder/ckfinder.js"></script>
</body>
</html>
