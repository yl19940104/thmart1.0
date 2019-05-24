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
        <h1>活动白名单</h1>
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
          <button type="button" class="btn btn-default" data-toggle="modal" data-target="#excelModal">批量上传</button>
          <table class="orderTable">
            <tr>
              <td>选择</td>
              <td>sku</td>
              <td>商品名字</td>
              <td>价格</td>
              <td>促销价</td>
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
                  <td>{{$v->skuNumber}}</td>
                  <td>{{$v->title}}</td>
                  <td>{{$v->price}}</td>
                  <td>{{$v->salePrice}}</td>
                </tr>
              @endforeach
            @endif
          </table>
        </div>
      </section>
      <!-- 模态框（Modal） -->
      <div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
              </button>
              <h4 class="modal-title" id="myModalLabel">
                批量上传白名单
              </h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="excelData" style="margin-top:2%" method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">excel表格</label>
                  <div class="col-xs-12 col-md-6">
                    <!-- <input type="text" class="form-control" id="excelName">
                    <input type="button" value="浏览" id="selectExcel" style="border-style: solid;"> -->
                    <input type="file" onchange="importf(this)">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                  </button>
                  <button type="button" class="btn btn-primary" id="excelSubmitData">
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
  <script src="/thmartAdmin/js/api/couponSkuList.js"></script>
  <script src="/thmartAdmin/js/xlsx.full.min.js"></script>
  <script src="/Public/plug/ckfinder/ckfinder.js"></script>
</body>
</html>
