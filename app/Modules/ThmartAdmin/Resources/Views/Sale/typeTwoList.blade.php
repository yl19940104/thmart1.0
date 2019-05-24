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
        <h1>团购价设置</h1>
      </section>
      <section class="content deals">
        <div class="order-form">
          <form id="formData">
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                  <label class="control-label">产品Id</label>
                  <input type="text" class="form-control" name="id" @if (isset($compactData['get']['id'])) value="{{$compactData['get']['id']}}" @endif>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                  <label class="control-label">SKU</label>
                  <input type="text" class="form-control" name="skuId" @if (isset($compactData['get']['skuId'])) value="{{$compactData['get']['skuId']}}" @endif>
                </div> 
              </div> 
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                  <label>品牌</label>
                  <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" name="brandId">
                    <option></option>
                    @foreach ($compactData['brand'] as $v)
                      @if (isset($compactData['get']['brandId']) && $compactData['get']['brandId'] == $v->id)
                        <option value="{{$v->id}}" selected="">{{$v->name}}</option>
                      @else
                        <option value="{{$v->id}}">{{$v->name}}</option>
                      @endif
                    @endforeach 
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                  <label>分类一</label>
                  <select class="form-controll select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" name="categoryOne"">
                    <option></option>
                    @foreach ($compactData['categoryOne'] as $v)
                      @if (isset($compactData['get']['categoryOne']) && $compactData['get']['categoryOne'] == $v->name)
                        <option value="{{$v->name}}" selected="">{{$v->title}}</option>
                      @else
                        <option value="{{$v->name}}">{{$v->title}}</option>
                      @endif
                    @endforeach 
                  </select>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                  <div class="form-group">
                  <label class="control-label">产品名字</label>
                  <input type="text" class="form-control" name="title" @if (isset($compactData['get']['title'])) value="{{$compactData['get']['title']}}" @endif>
                </div>
                </div>
              </div>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="onlySalePrice" @if (isset($compactData['get']['onlySalePrice']) && $compactData['get']['onlySalePrice'] == 'on') checked="" @endif>仅显示有团购价的商品
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="audited"  @if (isset($compactData['get']['audited']) && $compactData['get']['audited'] == 'on') checked="" @endif>仅显示上架的商品
              </label>
            </div>
            <div class="bottomBtn">
              <button type="button" class="btn btn-default searchButton">搜索</button>
            </div>
          </form>
        </div>
        <div class="tableBox">
          <button type="button" class="btn btn-default" id="allChecked">全选</button>
          <button type="button" class="btn btn-default" id="deleteArray">删除</button>
          <button type="button" class="btn btn-default" data-toggle="modal" data-target="#excelModal">批量上传</button>
          <table class="orderTable">
            <tr>
              <td>选择</td>
              <td>产品ID</td>
              <td>SKU</td>
              <td>一级类目</td>
              <td>英文</td>
              <td>中文</td>
              <td>品牌</td>
              <td>售价</td>
              <td>团购价</td>
              <td>时间</td>
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
                  <td class="col-xs-1">{{$v->itemId}}</td>
                  <td class="col-xs-1">{{$v->skuNumber}}</td>
                  <td class="col-xs-1">{{$v->categoryTitle}}</td>
                  <td class="col-xs-1">{{$v->title}}</td>
                  <td class="col-xs-1">{{$v->subTitle}}</td>
                  <td class="col-xs-1">{{$v->brandName}}</td>
                  <td class="col-xs-1">{{$v->price}}</td>
                  <td class="col-xs-1">{{$v->salePrice}}</td>
                  <td class="col-xs-1"><p>{{$v->startTime}}</p><p>--</p><p>{{$v->endTime}}</p></td>
                  <td class="col-xs-1">
                    @if (isset($v->id))
                      <button type="submit" class="btn btn-default add" data-toggle="modal" data-target="#myModal" skuNumber="{{$v->skuNumber}}" goodsName="{{$v->title}}" price="{{$v->price}}" salePrice="{{$v->salePrice}}" startTime="{{$v->startTime}}" endTime="{{$v->endTime}}" typeValue="{{$v->type}}" saleEditId="{{$v->id}}">修改</button>
                      <button type="submit" class="btn btn-default delete deleteButton" saleId="{{$v->id}}">删除</button>
                    @else
                      <button type="submit" class="btn btn-default add" data-toggle="modal" data-target="#myModal" skuNumber="{{$v->skuNumber}}" goodsName="{{$v->title}}" price="{{$v->price}}">添加</button>
                    @endif
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
                团购价设置
              </h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="formData" style="margin-top:2%">
                <input type="hidden" disabled="" id="saleEditId">
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">sku</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" name="password" placeholder="new password" disabled="" id="skuNumber">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商品名称</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" name="password" placeholder="new password" disabled="" id="goodsName">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">原价</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" name="password" placeholder="new password" disabled="" id="price">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">团购价</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" name="salePrice" id="salePrice">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">开始时间</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" name="startTime" id="startTime" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">结束时间</label>
                  <div class="col-xs-12 col-md-6">
                    <input type="text" class="form-control" name="endTime" id="endTime" placeholder="YYYY-MM-DD HH:II">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">设置规则</label>
                  <div class="col-xs-12 col-md-6">
                    <select class="form-control" id="typeValue">
                      <option></option>
                      <option value="1">清除老促销价的重复时间</option>
                      <option value="2">清除新促销价的重复时间</option>
                      <option value="3">清除有重叠时间的老促销价</option>
                    </select> 
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
      <div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
              </button>
              <h4 class="modal-title" id="myModalLabel">
                批量上传团购价
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
  <script src="/thmartAdmin/js/api/typeTwoList.js"></script>
  <script src="/thmartAdmin/js/xlsx.full.min.js"></script>
</body>
</html>
