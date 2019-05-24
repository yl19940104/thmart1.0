<?php
namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\Cart;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\CouponSku;
use App\Modules\ThmartApi\Models\CouponUser;
use App\Modules\ThmartApi\Models\OrdersSpell;
use Illuminate\Http\Request;
use Illuminate\Payment\Wxpaylib\JsApiPay;
use Illuminate\Payment\Wxpaylib\WxPayUnifiedOrder;
use Illuminate\Payment\Wxpaylib\NativePay;
use Illuminate\Payment\Wxpaylib\Phpqrcode;
use Illuminate\Payment\Wxpaylib\WxPayApi;
use Illuminate\Payment\Wxpaylib\WxPayConfig;
use Illuminate\Payment\Wxpaylib\WxPayException;
use Illuminate\Payment\Wxpaylib\WxPayNotify;

class Orders extends Model
{
	protected $table = "orders";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['orderNumber', 'feeTotal', 'priceTotal', 'couponTotal', 'address', 'email', 'userId', 'province', 'city', 'regionDetail', 'orderTime', 'fullName', 'phone', 'code', 'buyerRemark'];
	//为true表示记录添加时间和更新时间
	public $timestamps = true;

	protected function getDateFormat()
    {
        return time();
    }
    
    public function getOne($orderNumber)
    {
        $res = $this->select('priceTotal', 'feeTotal' ,'orderNumber', 'orderTime', 'sendTime', 'payTime', 'userId', 'province', 'city', 'regionDetail', 'phone', 'fullName', 'email', 'status', 'buyerRemark')->where(['orderNumber'=>$orderNumber])->get();
        $res = $res->toArray();
        if ($res) {
            $res['0']['priceTotal'] *= 0.01;
            $res['0']['feeTotal'] *= 0.01;
            return $res;
        }
    }

    //订单列表页
    public function getList($condition, $pageSize)
    {
        if (isset($condition['status']) && is_array($condition['status'])) {
            $condition_two = $condition;
            unset($condition['status']);
            $res = $this->select('orderNumber', 'priceTotal', 'status', 'feeTotal')->where($condition)->whereIn('status', $condition_two['status'])->orderBy('orderTime', 'desc')->paginate($pageSize);
        } else {
            $res = $this->select('orderNumber', 'priceTotal', 'status', 'feeTotal')->where($condition)->orderBy('orderTime', 'desc')->paginate($pageSize);
        }
        if ($res) {
            foreach ($res as &$v) {
                $v['priceTotal'] *= 0.01;
                $v['feeTotal'] *= 0.01;
            }
            unset($v);
            return $res->toArray();
        }
    }

    //获取订单状态
    public function getStatus($orderNumber)
    {
        return $this->select('status', 'userId', 'email', 'code', 'orderNumber')->where('orderNumber', $orderNumber)->get()->toArray();
    }

    //添加物流时更新订单状态
    public function saveStatus($param)
    {
        return $this->where('orderNumber', $param['orderNumber'])->update($param);
    }

    //保存一条数据
    public function saveOne($param)
    {
        $param['feeTotal'] *= 100;
        $param['couponTotal'] *= 100;
        $param['priceTotal'] *= 100;
    	return $this->create($param);
    }

    //逻辑删除一条订单
    public function deleteOne($orderNumber)
    {
        return $this->where('orderNumber', $orderNumber)->update(['isDelete'=>1]);
    }

    //订单变为已支付状态
    public function savePayStatus($param)
    {
        return $this->where('orderNumber', $param['orderNumber'])->update($param);
    }

    //返回预处理订单信息,如果$couponId不为空，则表示用户在订单预处理页选择优惠券并修改总价或用户下单时传送的couponId
    public function getInfo($userId, $param)
    {
    	//$selectCouponId是用户在订单预处理页选择的coupon的Id或者下单传输的couponId
    	if (isset($param['couponId'])) $selectCouponId = $param['couponId'];
    	$res = $this->validateParamReturnRes($userId, $param);
    	$array = [];
    	//每个供应商对应的所有商品经过优惠后的商品总价，格式为["1": 149,"2": 40]
    	$shopFee = [];
        //该订单下的运费总价 
        $feeTotal = 0;
        //团购数组，用来判定是否先进行满减分组
    	$overReduce = [];
        //sku的满减活动id,skuid,商品价格(原价或促销)，满减最低消费，减去金额, 满减池中的商品总价， 该满减达标时该商品减去一定比例的优惠后剩余的价格。该购物车里所有在此满减池中的价格总和，格式为[{"couponId": 52,"skuId": 202,"skuPrice": 35,"over": 10,"reduce": 6,"total": 135,"afterReduce": 33},{"couponId": 52,"skuId": 203,"skuPrice": 100,"over": 10,"reduce": 6,"total": 135,"afterReduce": 96},{"couponId": 51,"skuId": 2,"skuPrice": 40,"over": 200,"reduce": 5,"total": 40,"afterReduce": 35}]
        $skuIdPriceArray = [];
        //skuid对应扣除一定比例满减优惠的数组，格式为[{"skuId": 202,"afterReduce": 33},{"skuId": 203,"afterReduce": 96},{"skuId": 2,"afterReduce": 40}]
        $skuReducePriceArray = [];
        //skuId数组，用来查询预下单时选中的商品所涉及的所有优惠券
        $skuIdArray = [];
        //skuid对应扣除一定比例满减优惠价格的数组，格式为["2": 40,"201": 20,"202": 33,"203": 96]
        $skuReduce = [];
        //订单涉及到的优惠券对应的满，减，订单里在此优惠池内的所有商品的总价，格式为["47": {"over": 100,"reduce": 50,"total": 40},"49": {"over": 20,"reduce": 5,"total": 20}},"data": null]
        $couponTotal = [];
        //默认选择的符合满减的最大优惠券金额
        $couponTypeOneReduce = 0;
        //订单内商品经过优惠活动后的最终价格
        $total = 0;
        //订单内商品经过优惠活动后的最终价格(不包含电子票)，用来计算运费
        $totalWithOutEticket = 0;
        //键值对，用户领取的符合条件的couponId对应减去的价格
        $userCouponReduceArray = [];
        //用户领取的优惠券id数组，格式[1,2,3],用来判断商品所涉及的优惠券时候被用户领取过
        $couponIdArray = [];
        //这层循环的目的是把购物车里所有包含的满减全部以数组形式存入$overReduce,形式为[["over"=>1000,"reduce"=>50,"total"=>1045.6],["over"=>1000,"reduce"=>50,"total"=>1045.6]],作用是第二次循环$res时区分满减是否达到要求,如果达到，那么商品按满减分组，如果没有，商品按品牌分组
        //$onlyEticket表示订单里面是否只有电子票，1表示是，0表示否，用来计算运费
        $onlyEticket = 1;
        /*returnJson(1, $res);*/
    	foreach ($res as $v) {
        	//获取该sku的最低促销(团购)价类型(促销或团购)
            $skuMinPriceType = (new ItemSalePrice)->getSkuMinPriceType($v['skuId']);
            /*returnJson(1, $skuMinPriceType);*/
            $data = (new Sku)->getDetail($v['skuId']);
            $item = (new Item)->getDetail($data['itemId']);
            //如果这个sku没有团购价,那么判断他是否在满减池里,如果在满减池,则记录起来
            if (!$skuMinPriceType || $skuMinPriceType['0']['type'] != 2) {
                $couponList = (new couponSku)->getCouponTypeOverReduce($v['skuId'], 2);
                /*returnJson(1, $couponList);*/
                //如果这个sku有拼单价而且请求的是拼单价的话
                if ($skuMinPriceType && $skuMinPriceType['0']['type'] == 3 && isset($param['isSpell']) && $param['isSpell']) {
                    array_push($skuReducePriceArray, ['skuId'=>$v['skuId'], 'afterReduce'=>$skuMinPriceType['0']['salePrice'] * $v['number'], 'shopId'=>$v['shopId'], 'reduce'=>0, 'shopId'=>$v['shopId'], 'title'=>$item['title'], 'goodsId'=>$item['id'], 'skuPropName'=>$data['propName'], 'costPrice'=>$data['costPrice'] * $v['number'], 'number'=>$v['number'], 'skuPrice'=>$skuMinPriceType['0']['salePrice'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$data['pic'], 'point'=>$data['point'], 'isGroup'=>1/*isGroup字段表示该sku价格是否是团购价，如果是团购价则无法纳入优惠券范畴*/]);
                    //如果有满减
                } elseif ($couponList) {
                    $overReduce[$couponList['0']['id']]['over'] = $couponList['0']['over'];
                    $overReduce[$couponList['0']['id']]['reduce'] = $couponList['0']['reduce'];
                    if (!isset($overReduce[$couponList['0']['id']]['total'])) $overReduce[$couponList['0']['id']]['total'] = 0;
                    if (!isset($overReduce[$couponList['0']['id']]['sku'])) $overReduce[$couponList['0']['id']]['sku'] = [];
                    //如果该商品只有原价
                    if (!$skuMinPriceType) {
                        $overReduce[$couponList['0']['id']]['total'] += $data['price'] * $v['number'];
                        array_push($overReduce[$couponList['0']['id']]['sku'], ['couponId'=>$couponList['0']['id'], 'skuId'=>$v['skuId'], 'skuPrice'=>$data['price'], 'over'=>$couponList['0']['over'], 'reduce'=>$couponList['0']['reduce'], 'shopId'=>$v['shopId'], 'title'=>$item['title'], 'goodsId'=>$item['id'], 'skuPropName'=>$data['propName'], 'costPrice'=>$data['costPrice'] * $v['number'], 'number'=>$v['number'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$data['pic'], 'point'=>$data['point']]);
                    //如果该商品有促销价
                    } else {
                        $overReduce[$couponList['0']['id']]['total'] += $skuMinPriceType['0']['salePrice'] * $v['number'];
                        array_push($overReduce[$couponList['0']['id']]['sku'], ['couponId'=>$couponList['0']['id'], 'skuId'=>$v['skuId'], 'skuPrice'=>$skuMinPriceType['0']['salePrice'], 'over'=>$couponList['0']['over'], 'reduce'=>$couponList['0']['reduce'], 'shopId'=>$v['shopId'], 'title'=>$item['title'], 'goodsId'=>$item['id'], 'skuPropName'=>$data['propName'], 'costPrice'=>$data['costPrice'] * $v['number'], 'number'=>$v['number'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$data['pic'], 'point'=>$data['point']]);
                    }
                //如果没有满减，只有原价,或有原价和拼单价
                } elseif(!$skuMinPriceType || $skuMinPriceType['0']['type'] == 3) {
                    /*returnJson(1, $data);*/
                array_push($skuReducePriceArray, ['skuId'=>$v['skuId'], 'afterReduce'=>$data['price'] * $v['number'], 'shopId'=>$v['shopId'], 'reduce'=>0, 'shopId'=>$v['shopId'], 'title'=>$item['title'], 'goodsId'=>$item['id'], 'skuPropName'=>$data['propName'], 'costPrice'=>$data['costPrice'] * $v['number'], 'number'=>$v['number'], 'skuPrice'=>$data['price'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$data['pic'], 'point'=>$data['point']]);
                //如果没有满减，有促销价
            } else {
                    array_push($skuReducePriceArray, ['skuId'=>$v['skuId'], 'afterReduce'=>$skuMinPriceType['0']['salePrice'] * $v['number'], 'shopId'=>$v['shopId'], 'reduce'=>0, 'shopId'=>$v['shopId'], 'title'=>$item['title'], 'goodsId'=>$item['id'], 'skuPropName'=>$data['propName'], 'costPrice'=>$data['costPrice'] * $v['number'], 'number'=>$v['number'], 'skuPrice'=>$skuMinPriceType['0']['salePrice'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$data['pic'], 'point'=>$data['point']]);
                }
            //如果有团购价，那么那数据记录在$skuReducePriceArray，用来之后计算运费
            } else {
                array_push($skuReducePriceArray, ['skuId'=>$v['skuId'], 'afterReduce'=>$skuMinPriceType['0']['salePrice'] * $v['number'], 'shopId'=>$v['shopId'], 'reduce'=>0, 'shopId'=>$v['shopId'], 'title'=>$item['title'], 'goodsId'=>$item['id'], 'skuPropName'=>$data['propName'], 'costPrice'=>$data['costPrice'] * $v['number'], 'number'=>$v['number'], 'skuPrice'=>$skuMinPriceType['0']['salePrice'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$data['pic'], 'point'=>$data['point'], 'isGroup'=>1/*isGroup字段表示该sku价格是否是团购价，如果是团购价则无法纳入优惠券范畴*/]);
            }
        }
        /*returnJson(1, $skuReducePriceArray);*/
        /*returnJson(1, $overReduce);*/
        foreach ($overReduce as $k => $v) {
            foreach ($v['sku'] as $key => $value) {
                array_push($skuIdPriceArray, $value);
            }
        }
        foreach ($skuIdPriceArray as &$v) {
            $v['total'] = $overReduce[$v['couponId']]['total'];
            if ($v['total'] > $v['over']) {
                $v['afterReduce'] = ($v['skuPrice'] - floor($v['skuPrice']/$v['total']*$v['reduce']*100)/100) * $v['number'];
                $v['reduce'] = floor($v['skuPrice']/$v['total']*$v['reduce']*100)/100;
            } else {
                $v['afterReduce'] = $v['skuPrice'] * $v['number'];
                $v['reduce'] = 0;
            }
        }
        /*returnJson(1, $skuIdPriceArray);*/

        unset($v);
        /*returnJson(1, $skuIdPriceArray);*/
        foreach ($skuIdPriceArray as $v) {
            array_push($skuReducePriceArray, ['skuId'=>$v['skuId'], 'afterReduce'=>$v['afterReduce'], 'reduce'=>$v['reduce'], 'shopId'=>$v['shopId'], 'title'=>$v['title'], 'goodsId'=>$v['goodsId'], 'skuPropName'=>$v['skuPropName'],'costPrice'=>$v['costPrice'], 'number'=>$v['number'], 'skuPrice'=>$v['skuPrice'], 'type'=>$v['type'], 'brandId'=>$v['brandId'], 'brandName'=>$v['brandName'], 'pic'=>$v['pic'], 'point'=>$v['point']]);
        }

        /*
         *$skuReducePriceArray后期会存进orderSku表中
         */

        /*returnJson(1, $skuReducePriceArray);*/
        foreach ($skuReducePriceArray as $v) {
            $skuReduce[$v['skuId']] = $v['afterReduce'];
            if (!isset($v['isGroup'])) array_push($skuIdArray, $v['skuId']);
        }
        /*returnJson(1, $skuReduce);*/
        /*returnJson(1, $shopFee);*/
        /*returnJson(1, $skuIdArray);*/
        //获取该订单的商品所设计的所有优惠券
        $data = (new CouponSku)->getCouponTypeOverReduceByArray($skuIdArray, 1); 
        /*returnJson(1, $data);*/
        //用户领取的未使用过的优惠券
        $couponUser =  (new CouponUser)->getList($userId, 0);
        /*returnJson(1, $couponUser);*/
        if ($couponUser) {
            foreach ($couponUser as $v) {
                array_push($couponIdArray, $v['couponId']);
            }          
        }
        //如果用户没有领取过此优惠券，则在优惠券数组中删除该优惠券信息
        foreach ($data as $k => $v) {
            if (!in_array($v['id'], $couponIdArray)) unset($data[$k]);
        }
        /*returnJson(1, $data);*/
        /*returnJson(1, $couponIdArray);*/
        /*return $couponListUser;*/
        foreach ($data as $v) {
            if (!isset($couponTotal[$v['id']])) $couponTotal[$v['id']] = [];
            $couponTotal[$v['id']]['over'] = $v['over'];
            $couponTotal[$v['id']]['reduce'] = $v['reduce'];
            $couponTotal[$v['id']]['couponId'] = $v['id'];
            if (!isset($couponTotal[$v['id']]['total'])) $couponTotal[$v['id']]['total'] = 0;
            $couponTotal[$v['id']]['total'] += $skuReduce[$v['skuId']];
            $couponTotal[$v['id']]['name'] = $v['name'];
        }
        unset($v);
        /*returnJson(1, $data);*/
        /*returnJson(1, $couponTotal);*/
        //去除不符合优惠满减的优惠券信息并获取订单内符合条件的优惠券的最高优惠
        foreach ($couponTotal as $k => $v) {
            //如果该优惠券下的商品总价不符合最低满减价格，删除此优惠券信息
            if ($v['total'] < $v['over']) unset($couponTotal[$k]);
            if (isset($couponTotal[$k])) {
               if ($v['reduce'] > $couponTypeOneReduce) $couponTypeOneReduce = $v['reduce'];
            }
        }
        /*returnJson(1, $couponTotal);*/
        /*returnJson(1, $couponTypeOneReduce);*/
        //用户在订单预处理页获取的已领取的所有符合要求的优惠券信息
        $couponListUser = array_values($couponTotal);
        /*returnJson(1, $couponListUser);*/
        //skuCoupon为获取每个sku对应的最终价格以及平摊下来的优惠券扣除金额
        foreach ($data as $k => &$v) {
            if (isset($couponTotal[$v['id']])) {
                $v['total'] = $couponTotal[$v['id']]['total'];
                //每个sku平摊的优惠券价格
                $v['couponReduce'] = floor($skuReduce[$v['skuId']]/$v['total']*$v['reduce']*100)/100;
                $skuCoupon[$v['skuId']]['couponReduce'] = $v['couponReduce'];
                $skuCoupon[$v['skuId']]['couponId'] = $v['id'];
            } else {
                unset($data[$k]);
            }
        }
        unset($v);
        /*returnJson(1, $data);*/
        /*returnJson(1, $skuCoupon);*/
        //商品信息分组拼接并显示
    	foreach ($res as $k => $v) {
    		$sku = (new Sku)->getDetailNotDelete($v['skuId']);
            $item = (new Item)->getOneNotDeleteOrAudited($v['goodsId']);
            if (!isset($shopFee[$item['shopId']])) $shopFee[$item['shopId']] = 0;
            //如果sku未被删除且商品未被删除或下架
            if ($sku && $item) {
                $data = [
            		'goodsName'     => $v['goodsName'],
            		'number'        => $v['number'],
            		'pic'           => adminDomain().$sku['pic'],
            		'price'         => $sku['price'],
            		'prop'          => json_decode($sku['propName']),
	    		];
	    		    //获取这个sku的最低促销(团购)价;
                $minSkuSalePrice = (new ItemSalePrice)->getMinSkuSale($v['skuId']);
                $data1 =  (new ItemSalePrice)->select('salePrice', 'type')
                    ->where(['skuId'=>$v['skuId'], ['startTime', '<=', time()], ['endTime', '>=', time()]])
                    ->get()
                    ->toArray();
                //如果这个商品有团购或促销价、或者有拼单价且参数传过来要求显示拼单价，那么价格会进行修改
                if ($minSkuSalePrice && ($data1['0']['type'] != 3 || (isset($param['isSpell']) && $param['isSpell']))) {
                    $param = $data['price'];
                    $data['price'] = $minSkuSalePrice;
                    $data['originalPrice'] = $param;
                }
    		}
            //获取该sku可能存在的最低促销(团购)价类型(促销或团购)
            $skuMinPriceType = (new ItemSalePrice)->getSkuMinPriceType($v['skuId']);
            //获取该sku可能存在的团购价
            $couponList = (new couponSku)->getCouponTypeOverReduce($v['skuId'], 2);
            //如果这个sku当前时间段存在满减池中并且(其没有促销价或者促销价不是团购价)并且该用户的购物车中选中商品已达到此满减条件,则商品按满减划分
            if ($couponList && (!$skuMinPriceType || $skuMinPriceType['0']['type'] != 2) && ($overReduce[$couponList['0']['id']]['over'] <=  $overReduce[$couponList['0']['id']]['total'])) {
                if (!isset($array['overReduceArray'][$couponList['0']['id']]['data'][$v['brandId']])) $array['overReduceArray'][$couponList['0']['id']]['data'][$v['brandId']]['data'] = [];
                array_push($array['overReduceArray'][$couponList['0']['id']]['data'][$v['brandId']]['data'], $data);
                $array['overReduceArray'][$couponList['0']['id']]['data'][$v['brandId']]['brandName'] = $v['brandName'];
                $array['overReduceArray'][$couponList['0']['id']]['data'][$v['brandId']]['brandId'] = $v['brandId'];
                $array['overReduceArray'][$couponList['0']['id']]['reduce'] = $couponList['0']['reduce'];
                if (!isset($array['overReduceArray'][$couponList['0']['id']]['total'])) {
                    $array['overReduceArray'][$couponList['0']['id']]['total'] = 0;
                    $array['overReduceArray'][$couponList['0']['id']]['total'] -= $couponList['0']['reduce'];
                }
                $array['overReduceArray'][$couponList['0']['id']]['total'] += $data['price'] * $data['number'];
            //商品按品牌划分
            } else {
                if (!isset($array['brandArray'][$v['brandId']])) $array['brandArray'][$v['brandId']]['data'] = [];
                array_push($array['brandArray'][$v['brandId']]['data'], $data);
                $array['brandArray'][$v['brandId']]['brandName'] = $v['brandName'];
                $array['brandArray'][$v['brandId']]['brandId'] = $v['brandId'];
                if (!isset($array['brandArray'][$v['brandId']]['total'])) $array['brandArray'][$v['brandId']]['total'] = 0;
                $array['brandArray'][$v['brandId']]['total'] += $data['price'] * $data['number'];
            }
    	}
        $array['couponReduce'] = $couponTypeOneReduce;
        if (isset($array['brandArray'])) {
            foreach ($array['brandArray'] as $v) {
                $total += $v['total'];
            }
            $array['brandArray'] = array_values($array['brandArray']);
            /*returnJson(1, $array['brandArray']);*/
        }
        if (isset($array['overReduceArray'])) {
            foreach ($array['overReduceArray'] as $v) {
                $total += $v['total'];
            }
            $array['overReduceArray'] = array_values($array['overReduceArray']);
            foreach ($array['overReduceArray'] as &$v) {
                $v['data'] = array_values($v['data']);
            }
            /*returnJson(1, $array['overReduceArray']);*/
        }
        unset($v);
        //如果订单总价不满129，运费10元，如果满129，则免去运费
        /*if ($total < 129) $feeTotal = 10;*/
        foreach ($skuReducePriceArray as $v) {
            if ($v['type'] != 2) {
                $totalWithOutEticket += $v['afterReduce'];
                $onlyEticket = 0;
            }
        }
        //如果订单总价不满129，运费10元，如果满129，则免去运费
        if ($totalWithOutEticket < 99 && $onlyEticket == 0) $feeTotal = 10;
        $array['total'] = $total - $couponTypeOneReduce + $feeTotal;
        $array['feeTotal'] = $feeTotal;
        $array['userCouponList'] = $couponListUser;
        unset($v);
        /*returnJson(1, $couponListUser);*/
        foreach ($couponListUser as $v) {
        	$userCouponReduceArray[$v['couponId']] = $v['reduce'];
        }
        //如果$selectCouponId存在，说明是用户在订单预处理页选择优惠券，所以返回的是优惠券减去的金额以及该订单优惠完之后的总价
        if (isset($selectCouponId)) {
            /*returnJson(1, $selectCouponId);*/
        	//如果$selectCouponId = 0 说明用户不选择优惠券
        	if ($selectCouponId == 0) return ['total' => $total + $feeTotal, 'couponReduce' => 0, 'feeTotal'=>$feeTotal, 'skuReducePriceArray'=>$skuReducePriceArray];
        	if (!$res = (new Coupon)->getOne($selectCouponId)) return ['message'=>'优惠券不存在'];
           /* returnJson(1, $userCouponReduceArray);*/
        	if (!isset($userCouponReduceArray[$selectCouponId])) return ['message'=>'用户未领过此优惠券或未达到优惠券额度'];
        	$totalPrice = $total + $feeTotal - $userCouponReduceArray[$selectCouponId];
            //如果该sku对应的优惠券id是用户选择的id，则记录平摊的优惠券价格以及减去平摊优惠券价格否的最终价格
            foreach ($skuReducePriceArray as &$v) {
                $data = (new Sku)->getOne($v['skuId']);
                if ($data['stock'] < $v['number']) returnJson(114, '商品'.$v['title'].'库存不足');
                /*(new Sku)->editSkuStock($v['skuId'], $data['stock']-$v['number']);*/
                if (isset($skuCoupon[$v['skuId']]) && ($skuCoupon[$v['skuId']]['couponId'] == $selectCouponId)) {
                    //平摊优惠券后的最终价格
                    $v['afterReduce'] -= $skuCoupon[$v['skuId']]['couponReduce'];
                    //每个sku平摊的优惠券价格
                    $v['couponReduce'] = $skuCoupon[$v['skuId']]['couponReduce'];
                }
            }
            unset($v);
            /*returnJson(1, $skuReducePriceArray);*/
            //变量skuReducePriceArray是用户在下单的时候用来记录订单里面每个sku的具体信息
        	return ['total'=>$totalPrice, 'couponReduce'=>$userCouponReduceArray[$selectCouponId], 'feeTotal'=>$feeTotal, 'skuReducePriceArray'=>$skuReducePriceArray];
        }
        return $array;
    }

    //如果传了skuId和商品数量，则验证参数，返回数据结果
    private function validateParamReturnRes($userId, $param)
    {
    	if (isset($param['skuId']) && isset($param['number'])) {
            /*returnJson(1, (new Sku)->getOneToOrder($param['array']['skuId']));*/
            if (!$param['number'] || !is_numeric($param['number'])) returnJson(0, 'number错误');
            $res = (new Sku)->getOneToOrder($param['skuId']);
            if (!$res) returnJson(115, 'skuId不存在');
            if ($param['number'] > $res['0']['stock']) returnJson(114, '库存不足');
            $res['0']['number'] = $param['number'];
        } else {
            $res = (new Cart)->getListSelected($userId);
            if (!$res) returnJson(0, '购物车为空');
        }
        return $res;
    }

    //支付异步通知,$source支付来源,1支付宝，2微信
    public function payNotify($orderNumber, $source)
    {
        $result = (new Orders)->getStatus($orderNumber);
        if ($result['0']['status'] == 0 || $result['0']['status'] == 5)
        {
            //如果该订单使用过优惠券,更新优惠券用户状态
            if ($res = (new OrdersCoupon)->getOne($orderNumber)) (new CouponUser)->saveOne($result['0']['userId'] , $res['0']['couponId']);
            $data = [
                'status'      => '1',
                'paySource'   => $source,
                'payTime'     => time(),
                'orderNumber' => $orderNumber,
            ];
            //如果订单是拼单未支付状态，那么订单进去拼单已支付状态,拼单表状态也改变
            if ($result['0']['status'] == 5) {
                $data['status'] = 6;
                OrdersSpell::where(['orderNumber'=>$data['orderNumber']])->update(['status'=>1]);
                //如果拼单已经成功，则订单状态改为7
                $re = (new OrdersSpell)->changeOrderStatus($data['orderNumber']);
                if ($re) {
                    $data['status'] = 7;
                }
            }
            //如果订单中的所有商品都是电子票，那么该订单直接进入已到货状态
            if ($this->judgeTypeTwo($data['orderNumber'])) $data['status'] = 3;
            //更新订单状态为已支付状态
            $res = $this->savePayStatus($data);
            $res = (new OrdersSku)->getSkuIdList($orderNumber);
            $replayGoodsIdContent = [];
            foreach ($res as &$value) {
                if ($value['type'] == 2) {
                    if (!isset($replayGoodsIdContent[$value['goodsId']])) $replayGoodsIdContent[$value['goodsId']] = '';
                    $replayGoodsIdContent[$value['goodsId']] = $replayGoodsIdContent[$value['goodsId']].'<br/>goodsName: '.$value['title'];
                    foreach ($value['skuPropName'] as $k => $val) {
                        $replayGoodsIdContent[$value['goodsId']] = $replayGoodsIdContent[$value['goodsId']].'<br/>'.$k.':'.$val['0'];
                    }
                }
            }
            unset($value);
            //如果有电子票，则发送邮件，每个有多少个电子票商品就发多少份邮件
            if ($replayGoodsIdContent) {
                foreach ($replayGoodsIdContent as $v) {
                    $body = "<p>Dear Attendee,</p>
                                <p>Thank you for your ticket purchase for</p>
                                ".$v."
                                <p>Here is your booking code:{$result['0']['code']}</p>
                                <p>Please bring the code along on the day of the event, and your ticket will be available for collection according to your booking code.</a>.</p>
                                <p>For any further enquiries please email <a href='mailto:marketing@urbanatomy.com'>marketing@urbanatomy.com</a>.</p>
                                <p>We look forward to seeing you there!</p>
                                <p>Best regards,</p>";
                    sendMail('Autoresponse Email', $body, $result['0']['email']);
                }
            }


            ////invite活动更新日志表
            DB::table('invite_log')->where(['orderid'=>$orderNumber])->update(['status'=>1]);
            $res = DB::table('invite_log')->where(['orderid'=>$orderNumber])->get();
            $body = "<p>Dear Attendee,</p>
                                <p>Thank you for your ticket purchase for</p>
                                L'AVENUE Easter Basket DIY
                                <p>Here is your booking code:{$res['0']->code}</p>
                                <p>Please bring the code along on the day of the event, and your ticket will be available for collection according to your booking code.</a>.</p>
                                <p>For any further enquiries please email <a href='mailto:marketing@urbanatomy.com'>marketing@urbanatomy.com</a>.</p>
                                <p>We look forward to seeing you there!</p>
                                <p>Best regards,</p>";
            sendMail('Autoresponse Email', $body, $result['0']['email']);
            echo $wxdata->ToXml();
            return true;
        }
    }

    //获取四种订单状态数字
    public function getStatusNumber($userId)
    {
        /*$res = $this->where('userId', $userId)->;*/
    }

    //关闭过期订单并恢复sku库存,订单超过俩小时未支付则过期
    public function closeOrder()
    {
        $array = [];
        $data = $this->select('id')->where([['orderTime', '<=', time()-7200]])->whereIn('status', [0, 5])->get()->toArray();
        if (isset($data) && $data) {
            foreach ($data as $v) {
                array_push($array, $v['id']);
            }
        }
        $res = (new OrdersSku)->getSkuIdAndNumberByArray($array);
        //恢复库存
        (new Sku)->recoverStockArray($res);
        //关闭订单
        $this->where([['orderTime', '<=', time()-7200], 'status'=>0])->update(['status'=>4]);
        $this->where([['orderTime', '<=', time()-7200], 'status'=>5])->update(['status'=>4]);
    }

    //判断订单中是否所有商品都是电子票
    public function judgeTypeTwo($orderNumber)
    {
        $result = 1;
        $res = DB::table('orderssku')
            ->select('type')
            ->where(['orderNumber'=>$orderNumber])
            ->get()
            ->toArray();
        foreach ($res as $v) {
            if ($v->type != 2) $result = 0;
        }
        return $result;
    }

    //获取微信小程序支付或微信公众号支付参数
    public function wxPay($orderNumber)
    {
        $res = $this->getOne($orderNumber);
        $tools = new JsApiPay();
        $openId2 = $tools->GetOpenid();
        $input2 = new WxPayUnifiedOrder();
        $input2->SetBody('ThMart');
        $input2->SetAttach("test");
        $input2->SetOut_trade_no($res['0']['orderNumber']);
        $input2->SetTotal_fee($res['0']['priceTotal']*100);
        $input2->SetTime_start(date("YmdHis"));
        $input2->SetTime_expire(date("YmdHis", time() + 600));
        $input2->SetGoods_tag("tag");
        $url = adminDomain().'/thmartApi/Wx/notify';
        $input2->SetNotify_url($url);
        $input2->SetTrade_type("JSAPI");
        $input2->SetOpenid($openId2);
        $api = new WxPayApi();
        $order = $api->unifiedOrder($input2);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $jsApiParameters = json_decode($jsApiParameters, true);
        $jsApiParameters = http_build_query($jsApiParameters);
        $jsApiParameters = str_ireplace('prepay_id%3D', 'prepay_id', $jsApiParameters);
        return $jsApiParameters;
    }
}