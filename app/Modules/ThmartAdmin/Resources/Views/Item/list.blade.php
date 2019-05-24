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
  <style type="text/css">
    #order-page th,#order-page td {
      white-space:nowrap;
      text-align: center;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini" id="order-page">
  <div class="wrapper">
    @include('thmartAdmin::Common.top')
    @include('thmartAdmin::Common.left')
    <div class="content-wrapper">
      <section class="content-header">
        <h1>商品列表</h1>
      </section>
      <section class="content">
        <div class="editButtom clearfix">
          <form id="formData">
            <div class="form-group col-xs-12">
              <div class="form-group col-sm-4 form-horizontal">
                <label for="inputEmail3" class="col-sm-5 control-label" style="">英文名</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="title" @if (isset($compactData['get']['title'])) value="{{$compactData['get']['title']}}" @endif>
                </div>
              </div>
              <div class="form-group col-sm-4 form-horizontal">
                <label for="inputEmail3" class="col-sm-5 control-label">中文名</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="subTitle" @if (isset($compactData['get']['subTitle'])) value="{{$compactData['get']['subTitle']}}" @endif>
                </div>
              </div>
              <div class="form-group col-sm-4 form-horizontal">
                <label class="control-label col-sm-5">一级分类</label>
                <div class="col-sm-7">
                  <select class="form-control select2 select2-hidden-accessible" name="categoryName" style="width: 100%;" tabindex="-1" aria-hidden="true">
                    <option selected="selected"></option>
                    @foreach ($compactData['catOneList'] as $v)
                      @if (isset($compactData['get']['categoryName']) && $compactData['get']['categoryName'] == $v->name)
                        <option value="{{$v->name}}" selected="selected">{{$v->title}}</option>
                      @else
                        <option value="{{$v->name}}">{{$v->title}}</option>
                      @endif
                    @endforeach 
                  </select>
                </div>
              </div>
              <div class="form-group col-sm-4 form-horizontal">
                <label class="control-label col-sm-5">供应商</label>
                <div class="col-sm-7">
                  <select class="form-control select2 select2-hidden-accessible" name="supplier" style="width: 100%;" tabindex="-1" aria-hidden="true">
                    <option selected="selected"></option>
                    @foreach ($compactData['supplier'] as $v)
                      @if (isset($compactData['get']['supplier']) && $compactData['get']['supplier'] == $v->id)
                        <option value="{{$v->id}}" selected="selected">{{$v->supplier_name}}</option>
                      @else
                        <option value="{{$v->id}}">{{$v->supplier_name}}</option>
                      @endif
                    @endforeach 
                  </select>
                </div>
              </div>
              <div class="form-group col-sm-4 form-horizontal">
                <label for="inputEmail3" class="col-sm-5 control-label">商品id</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="id" @if (isset($compactData['get']['id'])) value="{{$compactData['get']['id']}}" @endif>
                </div>
              </div>
              <div class="form-group col-sm-4 form-horizontal">
                <label class="control-label col-sm-5">上下架状态</label>
                <div class="col-sm-7">
                  <select class="form-control" name="audited">
                    <option></option>
                    @if (isset($compactData['get']['audited']) && $compactData['get']['audited'] == 1)
                      <option value="1" selected="">已上架</option>
                    @else
                      <option value="1">已上架</option>
                    @endif
                    @if (isset($compactData['get']['audited']) && $compactData['get']['audited'] == 2)
                      <option value="2" selected="">未上架</option>
                    @else
                      <option value="2">未上架</option>
                    @endif
                  </select>
                </div>
              </div>
              <div class="form-group col-sm-4 form-horizontal">
                <label for="inputEmail3" class="col-sm-5 control-label"></label>
                <div class="col-sm-4">
                  <input type="button" class="form-control" id="searchItem" value="搜索">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-striped">
            <button type="button" class="btn btn-success col-xs-12 col-sm-1" onclick="downloadExl(jsono)">导出</button>
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
            <thead>
              <tr>
                <th><input type="checkbox" id="allChecked"></th>
                <th>id</th>
                <th>英文名</th>
                <th>中文名</th>
                <th>一级分类</th>
                <th>品牌</th>
                <th>供应商</th>
                <th>状态</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($compactData['itemData'] as $v)
                <tr>
                  <td><input type="checkbox" class="check" value="{{$v->id}}"></td>
                  <td>{{$v->id}}</td>
                  <td style="text-align:left">{{$v->title}}</td>
                  <td style="text-align:left">{{$v->subTitle}}</td>
                  <td>{{$v->categoryTitle}}</td>
                  <td>{{$v->brandName}}</td>
                  <td>{{$v->supplier_name}}</td>
                  <td>
                    @if ($v->audited == 1)
                      已上架
                    @else
                      未上架
                    @endif
                  </td>
                  <td>
                    <a href="/thmartAdmin/Item/second?supplier={{$v->supplier}}&categoryOne={{$v->categoryOne}}&categoryTwo={{$v->categoryTwo}}&itemId={{$v->id}}&categoryThree={{$v->categoryThree}}" style="display: inline-block; padding: 0 !important;"><button type="button" class="btn btn-primary" style="width: 100%;">修改</button></a>
                    @if (in_array(1, $compactData['roleArray']))
                    <button type="button" class="btn btn-primary btn-danger deleteButton" itemId="{{$v->id}}" id='deleteButton'>删除</button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tfoot>
          </table>
        </div>
        <div class="pageStyle">
          {{$compactData['itemData']->appends(request()->all())->links()}}
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
  <script src="/thmartAdmin/js/api/itemList.js"></script>
  <script src="/thmartAdmin/js/xlsx.full.min.js"></script>
</body>
</html>
