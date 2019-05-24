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
      <section class="content" id="app">
        <div>
          <div class="box-header with-border">
            <h3 class="box-title">供应商</h3>
          </div>
          <form class="form-horizontal" style="margin-top:2%" id="formData" method="post">
            <div class="form-group">
              @if (isset($compactData['id']))
              <input type="hidden" id="supplierId" value="{{$compactData['id']}}">
              @endif
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">供应商名称</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="supplier_name">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">供应商联系人</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="contacts_name">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">供应商电话</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="contacts_phone">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">供应商邮箱</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="contacts_email">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">供应商地址</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="contacts_address">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">统一社会信用代码</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="param">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">合同编号</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="number">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">采销(sales)</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="sale">
              </div>
            </div>
            <div class="form-group">
            </div>
            <div class="form-group">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">备注(非必填)</label>
              <div class="col-xs-12 col-md-6">
                <input type="text" class="form-control" v-model="remark">
              </div>
            </div>
            <div class="form-group" v-if="buttonShow">
              <label for="inputEmail3" class="col-xs-12 col-md-3 control-label">分类扣点</label>
              <div class="col-xs-12 col-md-6">
                <button type="button" class="btn btn-primary" @click="addPointList">+添加扣点</button>
              </div>
            </div>
            <div v-for="(item,index) in pointList">
              <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-12 col-md-6">
                  <label class="col-xs-12 col-md-2 control-label">一级分类</label>
                  <div class="col-md-4">
                    <select class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" @change="addPointListValue(index, 'catOneId', $event)" v-model="pointList[index].catOneId">
                      <option></option>
                      <option v-for="(item3,index3) in catOneList" :value="getCatOneId(index3)">@{{item3.title_cn}}</option>
                    </select>
                  </div>
                  <label class="col-xs-12 col-md-2 control-label">二级分类</label>
                  <div class="col-md-4">
                    <select class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" @change="addPointListValue(index, 'catTwoId', $event)" v-model="pointList[index].catTwoId">
                      <option></option>
                      <option v-for="(item2,index2) in catTwoList[index]" :value="getCatTwoId(index, index2)">@{{item2.title_cn}}</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                <div class="col-xs-12 col-md-6">
                  <label class="col-xs-12 col-md-2 control-label">扣点百分比(%)</label>
                  <div class="col-md-4">
                    <input class="form-control" @blur="addPointListValue(index, 'point', $event)" v-model="item.point">
                  </div>
                  <label class="col-xs-12 col-md-2 control-label"></label>
                  <div class="col-md-4" v-if="buttonShow">
                    <button type="button" class="btn btn-danger" @click="deletePointList(index)">删除</button>
                  </div>
                </div>
              </div>
              <div class="form-group">
              </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-xs-12 col-md-3 control-label"></label>
                
                <!-- @if (in_array(1, $compactData['roleArray']))
                <div class="col-xs-12 col-md-6">
                  <div v-if="is_effective == 0">
                    <button type="button" class="btn pull-right btn-primary" @click=submitEffective>确认通过审核</button>
                  </div>
                  <div v-else>
                    <button type="button" class="btn btn-success pull-right">审核通过</button>
                  </div>
                </div>
                @else
                <div class="col-xs-12 col-md-6" v-if="pointList.length > 0">
                  <div v-if="is_effective == 0">
                    <button type="button" class="btn btn-default pull-right">审核中</button>
                  </div>
                  <div v-else>
                    <button type="button" class="btn btn-success  pull-right">审核通过</button>
                  </div>
                </div>
                @endif -->
                @if (!in_array('/thmartAdmin/Supplier/list', $compactData['authArray']))
                  @if (isset($compactData['id']))
                  <div class="col-xs-12 col-md-6">
                    <div v-if="is_effective == 0">
                      <button type="button" class="btn btn-default pull-right">审核中</button>
                    </div>
                    <div v-else>
                      <button type="button" class="btn btn-success  pull-right">审核通过</button>
                    </div>
                  </div>
                  @else 
                  <div class="col-xs-12 col-md-6">
                    <button type="button" class="btn btn-primary pull-right" id="submit" @click="submitData">提交</button>
                  </div>
                  @endif
                @elseif (isset($compactData['get']['id'])) 
                  <div class="col-xs-12 col-md-6">
                    <button type="button" class="btn btn-primary pull-right" id="submit" @click="submitData">修改信息</button>
                    <div v-if="is_effective == 0">
                      <button type="button" class="btn pull-right btn-primary" @click=submitEffective>确认通过审核</button>
                    </div>
                    <div v-else>
                      <button type="button" class="btn btn-success pull-right">审核通过</button>
                    </div>
                  </div>
                @else
                  <div class="col-xs-12 col-md-6">
                    <button type="button" class="btn btn-primary pull-right" id="submit" @click="submitData">提交</button>
                  </div>
                @endif
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
  <script src="/thmartAdmin/js/vue.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/htmlTemplate.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/supplierDetail.js"></script>
  <script type="text/javascript">
  </script>
</body>
</html>
