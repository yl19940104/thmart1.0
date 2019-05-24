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
  <link rel="stylesheet" href="/thmartAdmin/css/select2.min.css">
  <link rel="stylesheet" href="/thmartAdmin/css/style2.css">
  <style type="text/css">
    .borer-style {
      width: 100px;
      height: 40px;
      border-top: 1px solid #ccc;
      border-left: 1px solid #ccc;
      line-height: 40px;
      text-align: center;
    }
    .tableBox .clearfix:last-child .borer-style {
      border-bottom: 1px solid #ccc;
    }
    .tableBox .clearfix .borer-style:last-child {
      border-right: 1px solid #ccc;
    }
    .add {
      cursor: pointer;
    }
    .input-style {
      float: left;
      width: 100%;
      height: 100%;
      text-align: center;
    }
    .tableBox .clearfix:last-child span {
      border-bottom: 1px solid transparent;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    @include('thmartAdmin::Common.top')
    @include('thmartAdmin::Common.left')
    <div class="content-wrapper">
      <section class="content-header">
        <h1>
          新建商品
        </h1>
        <ol class="breadcrumb">
          <li><a href="#">首页</a></li>
          <li class="active">新建商品</li>
          <li class="active">商品管理</li>
        </ol>
      </section>
      <section class="content" id="app">
        <div class="form-group form-ticket">
          <label>一级分类</label>
          <select v-model="categoryFirst" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" @change="selectCategorySecond()">
            @foreach ($compactData['categoryFirst'] as $v)
              <option value="{{$v->name}}">{{$v->title_cn}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group form-ticket">
          <label>二级分类</label>
          <select v-model="categorySecond" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
            <!-- @foreach ($compactData['supplier'] as $v)
              <option value="{{$v->id}}">{{$v->supplier_name}}</option>
            @endforeach -->
            <option v-for="(categoryList) in categorySecondList" :value="categoryList.id">@{{categoryList.title_cn}}</option>
          </select>
        </div>
        <div class="form-group form-ticket" v-show="showCategoryThree">
          <label>三级分类</label>
          <select v-model="categoryThird" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
          <!-- @foreach ($compactData['supplier'] as $v)
            <option value="{{$v->id}}">{{$v->supplier_name}}</option>
            @endforeach -->
            <option v-for="(categoryList) in categoryThirdList" :value="categoryList.id">@{{categoryList.title_cn}}</option>
          </select>
        </div>
        <div class="form-group title-radio" id="isDisplay" v-if="categoryFirst == 1">
          <div class="radio">
            <label>
              <input type="radio" name="optionsRadios" id="optionsRadios1" value="2" v-model="typed">
              电子票
            </label>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="optionsRadios" id="optionsRadios2" value="3" v-model="typed">
              纸质票
            </label>
          </div>
        </div>
        <div class="form-group form-ticket">
          <label>供应商</label>
          <select v-model="shopId" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
            @foreach ($compactData['supplier'] as $v)
              <option value="{{$v->id}}">{{$v->supplier_name}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group form-ticket">
          <label>品牌</label>
          <select v-model="selectBrand" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
            <option selected="selected"></option>
            @foreach ($compactData['brand'] as $v)
              <option value="{{$v->id}}">{{$v->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="proInfo">
          <span>英文名称</span>
          <input type="text" v-model="titled" placeholder="请输入">
        </div>
        <div class="proInfo">
          <span>中文名称</span>
          <input type="text" v-model="subTitle" placeholder="请输入">
        </div>
        <div class="proInfo">
          <span>英文副标题</span>
          <input type="text" v-model="enTitle" placeholder="请输入">
        </div>
        <div class="proInfo">
          <span>副标题链接<br>(非必填)</span>
          <input type="text" v-model="titleLink" placeholder="请输入">
        </div>
        <div class="form-group form-file">
          <span>主图</span>
          <div>
            <p class="boxImg"><img id="pic" alt="" src=""></p>
            <input type="button" value="浏览" id="ckfinder-popup-1" style="border-style: solid;" @click="selectPic">
            <p class="help-block">800*800，白底，不超过200k</p>
          </div>
        </div>
        <div class="form-group form-file">
          <span>轮播图<br>(非必填)</span>
          <div>
            <input type="hidden" id="picOne">
            <p class="boxImg" v-for="(item,index) in picList"><img id="pic" alt="" :src="getPicUrl(item)" @click="deletePicList(index)"></p>
            <input type="button" value="浏览" id="ckfinder-popup-2" style="border-style: solid;" @click="selectPicList">
            <p class="help-block">800*800，白底，不超过200k</p>
          </div>
        </div>
        <div class="form-group clearfix">
          <span>类目属性</span>
          <div class="plus">
            <ul class="clearfix">
              <li v-for="(item,index) in propValue" :key="index">
                <span>@{{item.name}}</span>
                <span @click="checkProp(item)">+</span>
              </li>
              <li>
                <span><input v-model="propName" name=""></span>
                <span @click="addPropValue">+</span>
              </li> 
            </ul>
          </div>
        </div>
        <div class="tableBox">
          <!-- <table class="plusTable" v-if="propList.length>0">
            <tr>
              <td></td>
              <td v-for="(item,index) in productSkuNameList" :key="index">@{{item}}<i class="fa fa-fw fa-minus-square" @click="deleteProductSkuName(index)"></i></td>
              <td @click="addProductSkuName"><i class="fa fa-fw fa-plus-square"></i></td>
            </tr>
            <tr v-for="(item,rowindex) in propList" :key="rowindex">
              <td><span @click="deletePropValue(rowindex)">-</span>@{{item.name}}</td>
              <td v-for="(item,index) in productSkuNameList" :key="index"><input @blur="addPropItem(rowindex,index,$event)" type="text" v-model="propList[rowindex].arr[index]"></td>
            </tr>
          </table> -->
          <!-- <table class="plusTable" v-if="propList.length>0">
            <tr>
              <td></td>
              <td v-for="(item,index) in productSkuNameList" :key="index">@{{item}}<i class="fa fa-fw fa-minus-square" @click="deleteProductSkuName(index)"></i></td>
              <td @click="addProductSkuName"><i class="fa fa-fw fa-plus-square"></i></td>
            </tr>
            <tr v-for="(item,rowindex) in propList" :key="rowindex">
              <td><span @click="deletePropValue(rowindex)">-</span>@{{item.name}}</td>
              <td v-for="(item,index) in productSkuNameList" :key="index"><input @blur="addPropItem(rowindex,index,$event)" type="text" v-model="propList[rowindex].arr[index]"></td>
            </tr>
          </table> -->

          <!-- productSkuNameList  縱向 --> 
          <!-- propList 橫向 -->
          <!-- <div v-for="(item,rowindex) in propList" :key="rowindex">
            
          </div> -->
          <!-- <div class="pull-left" style="width: 100px;height: 100px;">@{{item.name}}</div>
            <div class="pull-left" style="width: 1000px;height: 100px;" v-if="rowindex == 0">
              <span  v-for="(item,index) in productSkuNameList" :key="index" style="width: 100px;height: 100px;display: inline-block;">@{{item}}</span>
              <span style="width: 100px;height: 100px;background: red;color: #fff;" @click="addProductSkuName"> + </span>
            </div>
            <div class="pull-left" v-if="rowindex != 0" style="width: 1000px;height: 100px; display: inline-block;">
              <span v-for="(item,index) in productSkuNameList" :key="index"><input style="width: 100px;height: 100px; display: inline-block;border: 1px solid #000;" type="text" name=""></span>
            </div> -->

          <div>
            <div class="clearfix" v-if="rowindex == 0" class="clearfix" v-for="(item,rowindex) in productSkuNameList">
              <span class="pull-left borer-style add" @click="addProductSkuName">+</span>
              <span class="pull-left borer-style" v-for="(item,rowindex) in propList">@{{item.name}}<i class="fa fa-fw fa-minus-square" @click="deletePropValue(rowindex)"></i></span>
            </div>
            <div class="clearfix" v-for="(item,rowindex) in productSkuNameList">
              <span class="pull-left borer-style">@{{item}}<i class="fa fa-fw fa-minus-square" @click="deleteProductSkuName(rowindex)"></i></span>
              <span class="pull-left borer-style" v-for="(item,index) in propList"><input class="input-style" type="text" name="" @blur="addPropItem(index,rowindex,$event)" v-model="propList[index].arr[rowindex]"></span>
            </div>
          </div>
        </div>
        <p class="star">注：红色带*为必填项</p>
        <div class="tableBox">
          <table class="starTable">
            <tr>
              <td>图片管理</td>
              <td v-for="(item,index) in propList" :key="index">@{{item.name}}</td>
              <td>售价<i>*</i></td>
              <td v-if="point>0">成本价(扣点@{{point}}%)<i>*</i></td>
              <td v-else>成本价<i>*</i></td>
              <td>库存设置<i>*</i></td>
              <td>长宽高（cm）</td>
              <td>重量（g）</td>
              <td>产地（英语）</td>
              <td>型号（中文备注）</td>
            </tr>
            <tr v-for="(item,index) in finalSkuListArr" :key="index">
              <td>
                <p class="boxImg"><img :id="getImageId(index)" alt="" :src="item.pic"></p>
                <input type="button" value="浏览" id="ckfinder-popup-1" style="border-style: solid;" @click="selectImage(index)">
              </td>
              <td v-for="(item2, index2) in item.arr">@{{item2}}</td>
              <td><input @blur="addFinalSkuListArr(index,'price',$event)" type="text" v-model="finalSkuListArrSubmit[index].price"></td>
              <td><input @blur="addFinalSkuListArr(index,'costPrice',$event)" v-model="finalSkuListArrSubmit[index].costPrice" type="text"></td>
              <td><input @blur="addFinalSkuListArr(index,'stock',$event)" type="text" v-model="finalSkuListArrSubmit[index].stock"></td>
              <td><input @blur="addFinalSkuListArr(index,'size',$event)" type="text" v-model="finalSkuListArrSubmit[index].size"></td>
              <td><input @blur="addFinalSkuListArr(index,'weight',$event)" type="text" v-model="finalSkuListArrSubmit[index].weight"></td>
              <td><input @blur="addFinalSkuListArr(index,'place',$event)" type="text" v-model="finalSkuListArrSubmit[index].place"></td>
              <td><input @blur="addFinalSkuListArr(index,'type',$event)" type="text" v-model="finalSkuListArrSubmit[index].type"></td>
            </tr>
          </table>
        </div>
        <p class="detailEdit">详情页编辑</p>
        <!-- 编辑器 -->
        <div class="box-body pad">
          <form>
            <textarea id="detail" name="detail" cols="80"></textarea>
          </form>
        </div>
        <div class="bottomBtn">
          <!-- <button type="submit" class="btn btn-default">预览</button> -->
          <button @click="submitData" class="btn btn-default">提交</button>
          @if (in_array(1, $compactData['roleArray']))
            <button @click="changeAudited(1)" class="btn btn-default" v-if="getParam[3]">上架</button>
            <button @click="changeAudited(2)" class="btn btn-default" v-if="getParam[3]">下架</button>
          @endif
        </div>
      </section>
      <!-- /.content -->
    </div>
    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 2.3.12
      </div>
      <strong>© 2017 SH Aoyang Advertising Co., Ltd. 沪ICP备12038926号-5</strong>
    </footer>
  </div>
  <script src="/thmartAdmin/js/jquery-2.2.3.min.js"></script>
  <script src="/thmartAdmin/js/bootstrap.min.js"></script>
  <script src="/thmartAdmin/js/fastclick.js"></script>
  <script src="/thmartAdmin/js/select2.full.min.js"></script>
  <script src="/thmartAdmin/js/app.min.js"></script>
  <script src="/thmartAdmin/js/vue.js"></script>
  <script src="/thmartAdmin/js/layer.js"></script>
  <script src="/thmartAdmin/js/api/common.js"></script>
  <script src="/thmartAdmin/js/api/second.js"></script>
  <script src="/thmartAdmin/plug/ckeditor/ckeditor.js"></script>
  <!-- <script src="/thmartAdmin/plug/ckfinder/ckfinder.js"></script> -->
  <script src="/Public/plug/ckfinder/ckfinder.js"></script>
  <script>
    $(function () {
      CKEDITOR.replace('detail');
      $(".select2").select2();
    });
  </script>
</body>
</html>
