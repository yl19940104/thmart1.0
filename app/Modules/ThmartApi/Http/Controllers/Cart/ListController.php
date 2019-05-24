<?php
namespace App\Modules\ThmartApi\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Cart;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\CouponSku;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;

class ListController extends Controller
{ 

	public function index(Request $request)
	{
        $res = (new Cart)->getList($this->userId);
        //购物车商品列表数组
        $array = [];
        //$skuIdArray,购物车已选中的所有sku的id，用来计算总价
        $skuIdArray = [];
        $skuSelectedNumber = [];
        //购物车选中商品的总价
        $total = 0;
        //满减总共减去的金额
        $reduceTotal = 0;
        $overReduceArray = [];
        foreach ($res as $v) {
            $sku = (new Sku)->getDetailNotDelete($v['skuId']);
            $item = (new Item)->getOneNotDeleteOrAudited($v['goodsId']);
            //如果sku未被删除且商品未被删除或下架
            if ($sku && $item) {
            	$data = [
                    'cartId'        => $v['id'],
                    'goodsId'       => $v['goodsId'],
            		'goodsName'     => $v['goodsName'],
            		'number'        => $v['number'],
                    'skuId'         => $v['skuId'],
                    'stock'         => intval($sku['stock']),
            		'pic'           => adminDomain().$sku['pic'],
            		'price'         => $sku['price'],
            		'prop'          => json_decode($sku['propName']),
                    'isSelect'      => $v['isSelect'],
            	];
                if ($v['number'] > $sku['stock']) {
                    (new Cart)->saveOneNumber(['id'=>$v['id'], 'number'=>$sku['stock']]);
                    $data['number'] = $sku['stock'];
                }
                //获取这个sku的最低促销(团购)价;
                $minSkuSalePrice = (new ItemSalePrice)->getMinSkuSale($data['skuId']);
                if ($minSkuSalePrice) {
                    $param = $data['price'];
                    $data['price'] = $minSkuSalePrice;
                    $data['originalPrice'] = $param;
                }
                //获取该sku的最低促销(团购)价类型(促销或团购)
                $skuMinPriceType = (new ItemSalePrice)->getSkuMinPriceType($data['skuId']);
                //获取该sku的可能所在的满减活动的最低购买价格和优惠价格
                $couponList = (new couponSku)->getCouponTypeOverReduce($data['skuId'], 2);
                if ($couponList) {
                    $data['over'] = $couponList['0']['over'];
                    $data['reduce'] = $couponList['0']['reduce'];
                }
                //如果这个购物车的这个商品被选中了
                if ($data['isSelect'] == 1) {
                    //如果这个sku没有团购价,那么判断他是否在满减池里,如果在满减池,则记录起来
                    if (!$skuMinPriceType || $skuMinPriceType['0']['type'] != 2) {
                        if ($couponList) {
                            //$overReduceArray[$couponList['0']['id']]形式为[['over'=>$over, 'totalPrice'=>totalPrice], ['over'=>$over, 'totalPrice'=>totalPrice]], 这个数组记录购物车里商品所在的满减池以及对应的在此满减池里的所有购物车商品的总价，满减池最低满减金额，得出结果与总价相运算
                            if (!isset($overReduceArray[$couponList['0']['id']]['over'])) $overReduceArray[$couponList['0']['id']]['over'] = $data['over'];
                            if (!isset($overReduceArray[$couponList['0']['id']]['totalPrice'])) $overReduceArray[$couponList['0']['id']]['totalPrice'] = null;
                            $overReduceArray[$couponList['0']['id']]['totalPrice'] += $data['number'] * $data['price'];
                            if (!isset($overReduceArray[$couponList['0']['id']]['reduce'])) $overReduceArray[$couponList['0']['id']]['reduce'] = $couponList['0']['reduce'];
                        }
                    }
                }
                if ($data['isSelect'] == 1) {
                    array_push($skuIdArray, $v['skuId']);
                    //$skuSelectedNumber形式为[['skuId'=>'number'],['skuId'=>'number']],每个sku的数量
                    $skuSelectedNumber[$v['skuId']] = $v['number'];
                }
                //$array[$v['brandId']]['shopAll']为前端需要的字段，如果购物车品牌下的所有商品都被选择，那么这个品牌被选择,此字段本不应传，但前端实现不了
                if ((new Cart)->getBrandSelect($v['brandId'], $this->userId)) {
                    $array[$v['brandId']]['shopAll'] = true;
                } else {
                    $array[$v['brandId']]['shopAll'] = false;
                };
            	if (!isset($array[$v['brandId']]['brandName'])) $array[$v['brandId']]['brandName'] = $v['brandName'];
                if (!isset($array[$v['brandId']]['brandId'])) $array[$v['brandId']]['brandId'] = $v['brandId'];
            	if (!isset($array[$v['brandId']]['data'])) $array[$v['brandId']]['data'] = [];
            	array_push($array[$v['brandId']]['data'], $data);
            }
        }
        //$res形式为[['skuId'=>'price'],['skuId'=>'price']]，每个sku的价格，之后与$skuSelectedNumber的元素相乘获取总价

        $res = objectToArray((new Cart)->getSelectPriceArray($skuIdArray));

        if (isset($res)) {
            foreach ($res as $key => $v) {
                $total += $res[$key]*$skuSelectedNumber[$key];
            }
        }
        //如果满减符合条件，那么便利数组减去满减金额
        foreach ($overReduceArray as $v) {
            if ($v['over'] <= $v['totalPrice']) {
                $total -= $v['reduce'];
                $reduceTotal += $v['reduce'];
            }
        }
        $array = array_values($array); 
        $data = ['data'=>$array, 'total'=>$total, 'reduceTotal'=>$reduceTotal];
        returnJson(1, 'success', $data);
	}
}

