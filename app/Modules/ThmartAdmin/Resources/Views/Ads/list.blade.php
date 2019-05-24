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
        <h1>配置位置列表</h1>
      </section>
      <input type="hidden" value="{{$compactData['position']['0']->type}}" id="positionType">
      @if (isset($compactData['get']['id']))
        <input type="hidden" value="{{$compactData['get']['id']}}" id="positionId">
      @else
        <input type="hidden" value="3" id="positionId"> 
      @endif
      <section class="content">
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <div class="box-header">
              <ul class="nav nav-tabs">
                @foreach ($compactData['cat'] as $v)
                  <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                      {{$v['name']}}<span class="caret"></span>
                    </a>
                      <ul class="dropdown-menu">
                        @foreach ($v['son'] as $v2)
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="/thmartAdmin/Ads/list?id={{$v2['id']}}">{{$v2['name']}}</a></li>
                        @endforeach
                      </ul>
                  </li>
                @endforeach
                <button class="btn btn-primary pull-right skipUrl" data-toggle="modal" data-target="#myModal">添加配置内容</button>
              </ul>
            </div>
            <thead>
              <tr>
                <th>配置位置</th>
                @if (isset($compactData['get']['id']) && ($compactData['get']['id']  == 9 || $compactData['get']['id'] == 39 || $compactData['get']['id'] == 40 || $compactData['get']['id'] == 10))
                  <th>所属商户</th>
                @endif
                <th>内容</th>
                <th>排序</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($compactData['data'] as $v) 
                <tr>
                  <td class="col-xs-1">{{$compactData['position']['0']->name}}</td>
                  @if (isset($compactData['get']['id']) && ($compactData['get']['id']  == 9 || $compactData['get']['id'] == 39 || $compactData['get']['id'] == 40 || $compactData['get']['id'] == 10))
                    <td class="col-xs-1">{{$v->brandName}}</td>
                  @endif
                  <td class="col-xs-1"><image src="{{$v->content}}" style="width:50px; height:50px"></td>
                  <td class="col-xs-1">{{$v->order}}</td>
                  <td class="col-xs-1">
                    <button type="button" class="btn btn-primary col-xs-3" data-toggle="modal" data-target="#editAds" itemId="{{$v->contentId}}" order="{{$v->order}}" adsId="{{$v->id}}" pic="{{$v->pic}}" url="{{$v->url}}" merchantId="{{$v->merchantId}}">修改</button>
                    <div class="col-xs-1"></div>
                    <button type="button" class="btn btn-danger col-xs-3 deleteOne" adsId="{{$v->id}}">删除</button>
                  </td>
                </tr>
              @endforeach
            </tfoot>
          </table>
        </div>
        <div class="pageStyle">
          {{$compactData['data']->appends(request()->all())->links()}}
        </div>
        <!-- 模态框（Modal） -->
        <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                  添加配置
                </h4>
              </div>
              <div class="modal-body">
                <form class="form-horizontal" id="formData" style="margin-top:2%">
                  @if ($compactData['position']['0']->type  == 1)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商品</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" id="addItemId" style="width: 100%;">
                          <option selected="selected"></option>
                          @foreach ($compactData['itemList'] as $v) 
                            <option value="{{$v->id}}">{{$v->title}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  @if ($compactData['position']['0']->type  == 2)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商戶</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" id="addItemId" style="width: 100%;">
                          <option selected="selected"></option>
                          @foreach ($compactData['brandList'] as $v) 
                            <option value="{{$v->id}}">{{$v->name}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  @if ($compactData['position']['0']->type  == 4)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">文章</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" id="addItemId" style="width: 100%;">
                          <option selected="selected"></option>
                          @foreach ($compactData['articleList'] as $v) 
                            <option value="{{$v->id}}">{{$v->title}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  @if ($compactData['position']['0']->type  == 3)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">图片</label>
                      <div class="col-xs-12 col-md-6">
                        <input type="hidden" id="addPic">
                        <image id="pic" style="width: 50px; height: 50px;">
                        <input type="button" value="浏览" id="ckfinder-popup-1" style="border-style: solid;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">超链接</label>
                      <div class="col-xs-12 col-md-6">
                        <input class="form-control" type="text" id="addUrl">
                      </div>
                    </div>
                  @endif
                  @if (isset($compactData['get']['id']) && ($compactData['get']['id']  == 9 || $compactData['get']['id'] == 39 || $compactData['get']['id'] == 40 || $compactData['get']['id'] == 10))
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商戶</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" id="addMerchantId" style="width: 100%;">
                          <option selected="selected"></option>
                          @foreach ($compactData['brandList'] as $v) 
                            <option value="{{$v->id}}">{{$v->name}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  <div class="form-group">
                    <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">排序</label>
                    <div class="col-xs-12 col-md-6">
                      <input type="text" class="form-control" id="addOrder">
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
        <!-- 模态框（Modal） -->
        <div class="modal fade" id="editAds" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                  修改配置
                </h4>
              </div>
              <div class="modal-body">
                <form class="form-horizontal" id="formData" style="margin-top:2%">
                  <input type="hidden" id="adsId">
                  @if ($compactData['position']['0']->type  == 1)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商品</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" name="itemId" id="editItemId">
                          <option selected="selected"></option>
                          @foreach ($compactData['itemList'] as $v) 
                            <option value="{{$v->id}}">{{$v->title}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  @if ($compactData['position']['0']->type  == 2)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商戶</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" id="editItemId" style="width: 100%;">
                          <option selected="selected"></option>
                          @foreach ($compactData['brandList'] as $v) 
                            <option value="{{$v->id}}">{{$v->name}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  @if ($compactData['position']['0']->type  == 3)
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">图片</label>
                      <div class="col-xs-12 col-md-6">
                        <input type="hidden" id="editPic">
                        <image id="picTwo" style="width: 50px; height: 50px;">
                        <input type="button" value="浏览" id="ckfinder-popup-2" style="border-style: solid;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">超链接</label>
                      <div class="col-xs-12 col-md-6">
                        <input class="form-control" type="text" id="editUrl">
                      </div>
                    </div>
                  @endif
                  @if (isset($compactData['get']['id']) && ($compactData['get']['id']  == 9 || $compactData['get']['id'] == 39 || $compactData['get']['id'] == 40 || $compactData['get']['id'] == 10))
                    <div class="form-group">
                      <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">商戶</label>
                      <div class="col-xs-12 col-md-6">
                        <select class="form-control select2 select2-hidden-accessible" id="editMerchantId" style="width: 100%;">
                          <option selected="selected"></option>
                          @foreach ($compactData['brandList'] as $v) 
                            <option value="{{$v->id}}">{{$v->name}}</option>
                          @endforeach
                        </select> 
                      </div>
                    </div>
                  @endif
                  <div class="form-group">
                    <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">排序</label>
                    <div class="col-xs-12 col-md-6">
                      <input type="text" class="form-control" name="order" id="editOrder">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="button" class="btn btn-primary" id="updateData">
                      提交
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
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
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/Public/plug/ckfinder/ckfinder.js"></script>
  <script src="/thmartAdmin/js/api/adsList.js"></script>
</body>
</html>
