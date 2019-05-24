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
  <link rel="stylesheet" href="/thmartAdmin/css/datepicker3.css">
  <link rel="stylesheet" href="/thmartAdmin/css/daterangepicker.css">
  <link rel="stylesheet" href="/thmartAdmin/css/bootstrap-timepicker.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/style.css">
  <style type="text/css">
    #order-page th,#order-page td {
      white-space:nowrap;
      text-align: center;
      cursor: pointer;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini" id="order-page">
  <div class="wrapper">
    @include('thmartAdmin::Common.top')
    @include('thmartAdmin::Common.left')
    <div class="content-wrapper">
      <section class="content-header">
        <h1>订单查询</h1>
      </section>
      <section class="content">
        <form id="formData">
          <div class="form-group col-xs-12">
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label" style="">订单编号</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control" name="orderNumber" @if (isset($compactData['get']['orderNumber'])) value="{{$compactData['get']['orderNumber']}}" @endif>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">起始时间</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control pull-right" id="datepicker" name="startTime" @if (isset($compactData['get']['startTime'])) value="{{$compactData['get']['startTime']}}" @endif>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">结束时间</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control" id="datepicker2" name="endTime" @if (isset($compactData['get']['endTime'])) value="{{$compactData['get']['endTime']}}" @endif>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">供应商</label>
              <div class="col-xs-12 col-sm-7">
                <select class="form-control select2 select2-hidden-accessible" name="supplierId" style="width: 100%;" tabindex="-1" aria-hidden="true">
                  <option selected="selected"></option>
                  @foreach ($compactData['supplier'] as $v)
                    @if (isset($compactData['get']['supplierId']) && $compactData['get']['supplierId'] == $v->id)
                      <option value="{{$v->id}}" selected="selected">{{$v->supplier_name}}</option>
                    @else
                      <option value="{{$v->id}}">{{$v->supplier_name}}</option>
                    @endif
                  @endforeach 
                </select>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">商品分类</label>
              <div class="col-xs-12 col-sm-7">
                <select class="form-control select2 select2-hidden-accessible" name="categoryOne" style="width: 100%;" tabindex="-1" aria-hidden="true">
                  <option selected="selected"></option>
                  @foreach ($compactData['categoryOne'] as $v)
                    @if (isset($compactData['get']['categoryOne']) && $compactData['get']['categoryOne'] == $v->name)
                      <option value="{{$v->name}}" selected="selected">{{$v->title}}</option>
                    @else
                      <option value="{{$v->name}}">{{$v->title}}</option>
                    @endif
                  @endforeach 
                </select>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">汇款来源</label>
              <div class="col-xs-12 col-sm-7">
                <select class="form-control" name="paySource">
                  <option selected="selected"></option>
                  @if (isset($compactData['get']['paySource']) && $compactData['get']['paySource'] == 1)
                    <option value="1" selected="selected">微信</option>
                  @else
                    <option value="1">微信</option>
                  @endif
                  @if (isset($compactData['get']['paySource']) && $compactData['get']['paySource'] == 2)
                    <option value="2" selected="selected">支付宝</option>
                  @else
                    <option value="2">支付宝</option>
                  @endif
                </select>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">邮箱</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control" name="email" @if (isset($compactData['get']['email'])) value="{{$compactData['get']['email']}}" @endif>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">收件人手机</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control" name="phone" @if (isset($compactData['get']['phone'])) value="{{$compactData['get']['phone']}}" @endif>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">商品英文名</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control" name="title" @if (isset($compactData['get']['title'])) value="{{$compactData['get']['title']}}" @endif>
              </div>
            </div>
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label">商品中文名</label>
              <div class="col-xs-12 col-sm-7">
                <input type="text" class="form-control" name="subTitle" @if (isset($compactData['get']['subTitle'])) value="{{$compactData['get']['subTitle']}}" @endif>
              </div>
            </div>
            <!-- <div class="form-group col-sm-6 form-hori col-lg-4zontal">
              <label class="control-label col-xs-12 col-sm-4">一级分类</label>
              <div class="col-sm-8">
                <select class="form-control select2 select2-hidden-accessible" name="categoryName" style="width: 100%;" tabindex="-1" aria-hidden="true">
                  <option selected="selected"></option>
                </select>
              </div>
            </div> -->
            <div class="form-group col-sm-6 col-xs-12 col-lg-4 form-horizontal">
              <label for="inputEmail3" class="col-xs-12 col-sm-5 control-label"></label>
              <div class="col-xs-12 col-sm-4">
                <input type="button" class="form-control" id="searchOrder" value="搜索">
              </div>
            </div>
          </div>
        </form>
        <div class="editButtom clearfix">
        </div>
        <div>
        </div>
        <div class="form-group col-xs-12">
          <label class="col-xs-12 col-sm-2">
            <select name="example1_length" aria-controls="example1" class="form-control input-sm selectPage">
              @if ((isset($compactData['pageSize']) && $compactData['pageSize'] == 20))
                <option value="20" selected="">每页20条数据</a></option>
              @else 
                <option value="20">每页20条数据</a></option>
              @endif
              @if ((isset($compactData['pageSize']) && $compactData['pageSize'] == 50))
                <option value="50" selected="">每页50条数据</a></option>
              @else 
                <option value="50">每页50条数据</a></option>
              @endif
              @if (isset($compactData['pageSize']) && $compactData['pageSize'] == 100)
                <option value="100" selected="">每页100条数据</a></option>
              @else 
                <option value="100">每页100条数据</a></option>
              @endif
              @if (isset($compactData['pageSize']) && $compactData['pageSize'] == 200)
                <option value="200" selected="">每页200条数据</a></option>
              @else 
                <option value="200">每页200条数据</a></option>
              @endif
              @if (isset($compactData['pageSize']) && $compactData['pageSize'] == 500)
                <option value="500" selected="">每页500条数据</a></option>
              @else 
                <option value="500">每页500条数据</a></option>
              @endif
              @if (isset($compactData['pageSize']) && $compactData['pageSize'] > 500)
                <option value="all" selected="">一页显示所有数据</a></option>
              @else 
                <option value="all">一页显示所有数据</a></option>
              @endif
            </select>
          </label>
          <button type="button" class="btn btn-default col-xs-12 col-sm-3" disabled="disabled">数据量：{{$compactData['total']}}</button>
          <a href="" download="订单表格.xlsx" id="hf"></a>
          <div class="col-xs-12 col-sm-1"></div>
          <button type="button" class="btn btn-default col-xs-12 col-sm-1 selectStatus" value="5" @if($compactData['status']==5 || !isset($compactData['status'])) disabled="true" @endif>全部</button>
          <button type="button" class="btn btn-default col-xs-12 col-sm-1 selectStatus" value="6" @if($compactData['status']==6) disabled="true" @endif>拼单待成功</button>
          <button type="button" class="btn btn-default col-xs-12 col-sm-1 selectStatus" value="1" @if($compactData['status']==1) disabled="true" @endif>未发货</button>
          <button type="button" class="btn btn-default col-xs-12 col-sm-1 selectStatus" value="2" @if($compactData['status']==2) disabled="true" @endif>运输中</button>
          <button type="button" class="btn btn-default col-xs-12 col-sm-1 selectStatus" value="3" @if($compactData['status']==3) disabled="true" @endif>已到货</button>
        </div>
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-hover table-striped">
            <!-- <button onclick="downloadExl(jsono)">导出</button> -->
            <thead>
              <button type="button" class="btn btn-success col-xs-12 col-sm-1" onclick="downloadExl(jsono)">导出</button>
          <button type="button" class="btn btn-warning col-xs-12 col-sm-1 addLogistics" data-toggle="modal" data-target="#myModal" @if($compactData['status']==6) disabled="true" @endif>添加物流</button>
              <tr>
                <th><input type="checkbox" id="allChecked"></th>
                <th>订单号</th>
                <th>一级分类</th>
                <th>商品id</th>
                <th>成交价</th>
                <th>成本价</th>
                <th>数量</th>
                <th>英文名称</th>
                <th>中文名称</th>
                <th>下单时间</th>
                <th>品牌</th>
                <th>货款来源</th>
                <th>用户id</th>
                <th>收件人</th>
                <th>收件人手机</th>
                <th>收货地址</th>
                <th>邮箱</th>
                <th class="buyerRemark">留言</th>
                <th>物流状态</th>
                <th>快递单号</th>
                <th>上传者</th>
                <th>供应商</th>
                <th>code</th>
              </tr>
            </thead>
            <tbody id="selectedrow">
              @foreach ($compactData['orderList'] as $v)
                <tr>
                  <td><input type="checkbox" class="check" value="{{$v->id}}"></td>
                  <td>{{$v->orderNumber}}</td>
                  <td>{{$v->categoryOne}}</td>
                  <td>{{$v->goodsNumber}}</td>
                  <td>{{$v->price}}</td>
                  <td>{{$v->costPrice}}</td>
                  <td>{{$v->number}}</td>
                  <td style="text-align:left">{{$v->title}}</td>
                  <td style="text-align:left">{{$v->subTitle}}</td>
                  <td>{{$v->payTime}}</td>
                  <td>{{$v->brandName}}</td>
                  <td>
                    @if ($v->paySource == 1)
                      微信
                    @else
                      支付宝
                    @endif
                  </td>
                  <td>{{$v->userId}}</td>
                  <td>{{$v->fullName}}</td>
                  <td>{{$v->phone}}</td>
                  <td>{{$v->province}}</td>
                  <td>{{$v->email}}</td>
                  <td class="buyerRemark">{{$v->buyerRemark}}</td>
                  <td>
                    @if ($v->status == 3)
                      已到货
                    @elseif ($v->status == 2)
                      运输中
                    @else
                      未发货
                    @endif
                  </td>
                  <td>{{$v->logistics}}</td>
                  <td>{{$v->username}}</td>
                  <td>{{$v->supplier_name}}</td>
                  <td>
                    @if ($v->type == 2)
                      {{$v->code}}
                    @else
                      无
                    @endif
                  </td>
                </tr>
              @endforeach
            </tfoot>
          </table>
        </div>
        <div class="pageStyle">
          {{$compactData['orderList']->appends(request()->all())->links()}}
        </div>
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
                添加物流
              </h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="formData" style="margin-top:2%">
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">物流号</label>
                  <div class="col-xs-12 col-md-6">
                    <input class="form-control" id="logistics">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">物流公司</label>
                  <div class="col-xs-12 col-md-6">
                    <select class="form-control" style="width: 100%;" id="companyId">
                      <option selected="selected"></option>
                      @foreach ($compactData['logistics'] as $v) 
                        <option value="{{$v->id}}">{{$v->company}}</option>
                      @endforeach
                    </select> 
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
  </div>
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/jquery.form.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/moment.js"></script>
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/daterangepicker.js"></script>
  <script src="/thmartAdmin/js/bootstrap-datepicker.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/orderList.js"></script>
  <script src="/thmartAdmin/js/xlsx.full.min.js"></script>
</body>
</html>
