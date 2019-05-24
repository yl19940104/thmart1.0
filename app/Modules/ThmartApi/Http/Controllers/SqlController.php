<?php
namespace App\Modules\ThmartApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\CrontabChangePrice;
use App\Modules\ThmartApi\Models\MongoComment;
use App\Modules\ThmartApi\Models\OrdersSpell;
use Illuminate\Http\Request;

class SqlController extends Controller
{ 

    public function __construct(){}

	public function index(Request $request)
	{
        $data = (new Orders())->select('id')->where([['orderTime', '<=', time()-7200]])->whereIn('status', [0, 5])->get()->toArray();
        returnJson(1, $data);

        /*Log::emergency("系统挂掉了");
        returnJson(1, 33);*/


        /*$res = (new Item)->getMinSkulist('and item.isDelete = 0 and item.audited = 1 and sku.isDelete = 0 and item.categoryName = 160 and item.categoryTwoName = 161 limit 5', 'createTime desc', null);*/
        /*returnJson(1, $res);*/
        /*returnJson(1, $res);*/
        /*$userinfo = "Name: <a href='http://www.baidu.com'>百度</a> <br> Title: <a href='http://www.zhuhu.com'>知乎</a>";
        preg_match_all ("/<a .*?>.*?<\/a>/", $userinfo, $pat_array);
        returnJson(1, $pat_array);*/




	    /*$pattern = '/^(0|[1-9][0-9]*)$/';
        $res = preg_match($pattern, "1235", $matches);*/
        /*returnJson(1, 123);*/
        /*$res = MongoComment::get()->toArray();
        returnJson(1, $res);*/
        /*
	     *   记录每个分类下有多少未被删除且已上架的商品
	     * */
        /*$res = DB::table('category')->select('name', 'fname')->get();
        foreach ($res as $v) {
            if ($v->fname == 0) {
                $data = DB::table('item')->where(['categoryName'=>$v->name, 'isDelete'=>0, 'audited'=>1])->count();
                DB::table('category')->where('name', $v->name)->update(['itemNumber'=>$data]);
            } else {
                $data = DB::table('item')->where(['categoryTwoName'=>$v->name, 'isDelete'=>0, 'audited'=>1])->count();
                DB::table('category')->where('name', $v->name)->update(['itemNumber'=>$data]);
            }
        }
        returnJson(1, $res);*/
	    /*
	     *   迁移用户注册数据
	     * */
	    /*$res = DB::table('user')->select('id', 'createTime')->where('status', 0)->limit(500)->get();
        foreach ($res as $v) {
            $time = date('Y-m-d H:i:s', $v->createTime);
            $data = DB::table('user_info')->where('id', $v->id)->update(['created_at'=>$time]);
            DB::table('user')->where('id', $v->id)->update(['status'=>'1']);
        }
        returnJson(1, $data);*/

		/*
		 * 调整订单里面所有成本价大于售价的数据记录
		 */
		/*$res = DB::table('orderssku')
			->select('orderssku.id', 'goodsId', 'price', 'costPrice', 'point')
			->leftjoin('item', 'item.id', '=', 'orderssku.goodsId')
			->leftjoin('supplierPrecentage', function ($join) {
		        $join->on('supplierPrecentage.supplierId', '=', 'item.shopId')
		        	->on('supplierPrecentage.catTwoId', '=', 'item.categoryTwoName')
		        	->on('supplierPrecentage.catOneId', '=', 'item.categoryName');
		    })
			->whereColumn('price', '<' ,'costPrice')
			->get();
		foreach ($res as $k => $v) {
			if (isset($v->point) && $v->point) {
				$costPrice = floor($v->price * (1 - $v->point * 0.0001));
			    DB::table('orderssku')
			    	->where('orderssku.id', $v->id)
			    	->update(['costPrice'=>$costPrice]);
			}
		}
		returnJson(1, $res);*/

		/*
		 * test
		 */
		/*$res = DB::table('item')
			->select('supplierPrecentage.id')
			->leftjoin('supplierPrecentage', function ($join) {
		        $join->on('supplierPrecentage.supplierId', '=', 'item.shopId')
		        	->on('supplierPrecentage.catTwoId', '=', 'item.categoryTwoName')
		        	->on('supplierPrecentage.catOneId', '=', 'item.categoryName');
		    })
			->where('item.id', 33)
			->get()
			->toArray();
		returnJson(1, $res);*/

		/*
		 * category表记录排序状态应对pc端首页二级分类显示效果
		 */
		/*$res = DB::table('category')->select('name', 'fname')->get();
		foreach ($res as $k => $v) {
			if ($v->fname == 0) {
				$data = $v->name * 10;
				DB::table('category')->where('name', $v->name)->update(['orderby'=>$data]);
			}
			if ($v->fname != 0) {
				$data = $v->fname * 10 + 1;
				DB::table('category')->where('name', $v->name)->update(['orderby'=>$data]);
			}
		}
		returnJson(1, 'success');*/

		/*
		 * userLogin表的数据整合到user_info里面
		 */
		/*$res = DB::table('userLogin')->where('param', 0)->limit(600)->get()->toArray();
        foreach ($res as $v) {
        	if ($v->type == 'mobile') {
        		DB::table('user_info')->where('id', $v->userId)->update(['mobile'=>$v->loginId]);
        		DB::table('user_info')->where('id', $v->userId)->update(['nickname'=>$v->nickname]);
        	}
        	if ($v->type == 'wx') {
        		DB::table('user_info')->where('id', $v->userId)->update(['wx_openid'=>$v->loginId]);
        		DB::table('user_info')->where('id', $v->userId)->update(['nickname'=>$v->nickname]);
        	}
        	if ($v->type == 'wxPC') {
        		DB::table('user_info')->where('id', $v->userId)->update(['wx_unionid'=>$v->loginId]);
        		DB::table('user_info')->where('id', $v->userId)->update(['nickname'=>$v->nickname]);
        	}
        	DB::table('userLogin')->where('loginId', $v->loginId)->update(['param'=>1]);
        }
		returnJson(1, 'success');*/


		/*$array = [];
    	$res = CrontabChangePrice::get()->toArray();
    	$data = file_put_contents("yl.txt", var_export($res, TRUE));
    	if (isset($res) && $res) {
    		foreach ($res as $v) {
	    		array_push($array, $v['supplierId']);
	    	}
    	}
    	returnJson(1, $array);*/
		/*
		 * 订单商品表根据最新扣点调整成本价
		 */
		/*$res = DB::table('orderssku')->select('orderssku.id', 'orderssku.price', 'number', 'shopId', 'categoryName', 'categoryTwoName', 'orderssku.skuId', 'sku.costPrice')
			->leftjoin('item', 'item.id', '=', 'orderssku.goodsId')
			->leftjoin('sku', 'orderssku.skuId', '=', 'sku.id')
			->leftjoin('orders', 'orders.id', '=', 'orderssku.orderId')
			->orderby('orderssku.id', 'desc')
			->get()
			->toArray();
		foreach ($res as $v) {
			$param = [
				'catOneId'   => $v->categoryName,
				'catTwoId'   => $v->categoryTwoName,
				'supplierId' => $v->shopId,
			];
			$data = DB::table('supplierPrecentage')->select('point')
				->where($param)
				->get()
				->toArray();

			if (isset($data) && $data) {
				$save = [
					'costPrice' => $v->price * (1 - $data[0]->point * 0.0001), 
				];
			} else {
				$save = [
					'costPrice' => $v->number * $v->costPrice,
				];
			}
			$result = DB::table('orderssku')->where(['id'=>$v->id])->update($save);
		}
		returnJson(1, $result);*/
		/*
		 * 老用户邮箱迁移
		 */
		/*$res = DB::table('userAddress')->select('id', 'userId')->where(['email'=>'0', 'isDelete'=>'0'])->where('userAddress.id', '>', 1054)->get()->toArray();
		foreach ($res as $k => $v) {
			$data = DB::table('mt_user_third')->select('email', 'id')->where('id', $v->userId)->get()->toArray();
			DB::table('userAddress')->where('userId', $data['0']->id)->update(['email'=>$data['0']->email]);
		}
		returnJson(1, 'success');*/
		/*
		 * 新订单sku迁移
		 */
		/*$data = DB::table('orderssku')
			->whereIn('orderNumber', ['181655011999', '188702422468', '185347495894', '189775652175', '188816611897'])
			->get()
			->toArray();
		$data = objectToArray($data);
		foreach ($data as $v) {
			unset($v['id']);
			$res = DB::table('neworderssku')->insert($v);
		}
		returnJson(1, $data);*/

		/*
		 * 新订单迁移
		 */
		/*$data = DB::table('orders')
			->select('orderNumber', 'status', 'feeTotal', 'priceTotal', 'couponTotal', 'fullName', 'phone', 'email', 'userId', 'province', 'city', 'regionDetail', 'created_at', 'updated_at', 'orderTime', 'payTime', 'sendTime', 'buyerRemark', 'paySource', 'code', 'isDelete')
			->whereIn('orderNumber', ['181655011999', '188702422468', '185347495894', '189775652175', '188816611897'])
			->get()
			->toArray();
		$data = objectToArray($data);
		foreach ($data as $v) {
			$res = DB::table('neworders')->insert($v);
		}
		returnJson(1, $data);*/

		/*
		 * 订单sku迁移
		 */
		/*$data = DB::table('mt_order_goods')
			->select('mt_order_goods.id', 'order_id as orderId', 'mt_order.trade_no as orderNumber', 'item.title', 'item.pic', 'sku.itemId as goodsId', 'brand.id as brandId', 'brand.name as brandName', 'sku.id as skuId', 'sku.propName as skuPropName', 'sku.price', 'sku.costPrice', 'mt_order_goods.goods_cnt as number', 'logistics', 'company')
			->leftjoin('mt_order', 'mt_order.id', '=', 'mt_order_goods.order_id')
			->leftjoin('sku', 'sku.id', '=', 'mt_order_goods.price_id')
			->leftjoin('item', 'item.id', '=', 'sku.itemId')
			->leftjoin('brand', 'item.brandName', '=', 'brand.id')
			->where(['mt_order.order_success'=>'1'])
			->get()
			->toArray();
		$data = objectToArray($data);
		foreach ($data as $k => &$v) {
			$v['couponFee'] = 0;
			$v['discountFee'] = 0;
			$v['skuPrice'] = $v['price'] * $v['number'];
			$v['price'] = $v['price'] * $v['number'];
			$v['type'] = 1;
			$v['logisticsTime'] = 0;
		}
		DB::table('neworderssku')->insert($data);
		returnJson(1, $data);*/

		/*
		 * 订单迁移
		 */
		/*$data = DB::table('mt_order')
			->select('mt_order.id', 'trade_no as orderNumber', 'order_success', 'total as priceTotal', 'mt_order.user_id as userId', 'mt_address.fullname as fullName', 'mt_address.phone', 'mt_order.email', 'mt_address.region as province', 'mt_address.region_detail as regionDetail', 'payment_time', 'words as buyerRemark', 'source as paySource', 'code', 'address_id')
			->leftjoin('mt_address', 'mt_address.id', '=', 'mt_order.address_id')
			->where('order_success', 1)
			->get()
			->toArray();
		$data = objectToArray($data);
		foreach ($data as $key => &$value) {
			$value['status'] = $value['order_success'];
			unset($value['order_success']);
			$res = DB::table('mt_order_goods')
				->select('id', 'logistics', 'company', 'status')
				->where('order_id', $value['id'])
				->get()
				->toArray();
			$res = objectToArray($res);
			foreach ($res as $k => $v) {
				if (isset($v['logistics']) && $v['logistics'] && isset($v['company']) && $v['company'] && ($v['status'] == '0')) $value['status'] = 2;
				if ($v['status'] == '1') $value['status'] = 3;
			}

			if ($value['address_id'] == 0) {
				$value['fullName'] = 0;
				$value['phone'] = 0;
				$value['province'] = 0;
				$value['regionDetail'] = 0;
			}
			$value['feeTotal'] = 0;
			$value['couponTotal'] = 0;
			$value['city'] = 0;
			$value['created_at'] = $value['payment_time'];
			$value['updated_at'] = $value['payment_time'];
			$value['orderTime'] = $value['payment_time'];
			$value['payTime'] = $value['payment_time'];
			$value['sendTime'] = 0;
			unset($data[$key]['payment_time']);
			unset($data[$key]['address_id']);
			DB::table('neworders')->insert($value);
		}
		returnJson(1, 'success1122', $data);*/

		/*
		 * sku的propName属性迁移
		 */
		/*$typeNameArray = [];
		$goods = DB::table('mt_goods')->select('id', 'type_name_one', 'type_name_two')->get()->toArray();
		$price = DB::table('mt_goods_price')->select('price_id', 'goods_id', 'goods_type_one', 'goods_type_two')->get()->toArray();
		$price = objectToArray($price);
		foreach ($goods as $v) {
			$typeNameArray[$v->id] = [];
			array_push($typeNameArray[$v->id], $v->type_name_one);
			array_push($typeNameArray[$v->id], $v->type_name_two);
		}
		foreach ($price as &$v) {
			$v['array'] = [];
			$v['array'] = [
				$typeNameArray[$v['goods_id']][0] => [
					$v['goods_type_one'],
					0,
					0
				],
			];
			if (isset($v['goods_type_two']) && $v['goods_type_two']) {
				$v['array'][$typeNameArray[$v['goods_id']][1]] = [
					$v['goods_type_two'],
					0,
					0
				];
			}
			$v['array'] = json_encode($v['array']);
		}
		unset($v);
		foreach ($price as $value) {
			(new Sku)->where(['id'=>$value['price_id']])->update(['propName'=>$value['array']]);
		}*/


		/*
		 * sku的skuNumber
		 */
		/*$sku = DB::table('sku')->select('id')->get()->toArray();
		$sku = objectToArray($sku);
		foreach ($sku as $v) {
			$skuNumber = $v['id'] + 818000000;
			DB::table('sku')->where('id', $v['id'])->update(['skuNumber'=>$skuNumber]);
		}*/


		/*
		 * sku的price, costPrice
		 */
		/*$sku = DB::table('sku')->select('id', 'price', 'costPrice')->get()->toArray();
		$sku = objectToArray($sku);
		foreach ($sku as $v) {
			DB::table('sku')->where('id', $v['id'])->update(['price'=>$v['price']*100, 'costPrice'=>$v['costPrice']*100]);
		}*/


		/*
		 * item的detail
		 */
		/*$goodsContent = DB::table('mt_goods_content')->select('goods_id', 'goods_content')->get()->toArray();
		$goodsContent = objectToArray($goodsContent);
		foreach ($goodsContent as $v) {
			DB::table('item')->where('id', $v['goods_id'])->update(['detail'=>$v['goods_content']]);
		}*/

		/*
		 * item的goodsName
		 */
		/*$goodsId = DB::table('item')->select('id')->get()->toArray();
		$goodsId = objectToArray($goodsId);
		foreach ($goodsId as $v) {
			$goodsNumber = 600000 + $v['id'];
			DB::table('item')->where('id', $v['id'])->update(['goodsNumber'=>$goodsNumber]);
		}*/

		/*
		 * item的type
		 */
		/*$goodsType = DB::table('item')->select('id', 'type')->get()->toArray();
		$goodsType = objectToArray($goodsType);
		foreach ($goodsType as $v) {
			$type = $v['type'] + 1;
			DB::table('item')->where('id', $v['id'])->update(['type'=>$type]);
		}*/


		/*
		 * userLogin的mobile和微信方式
		 */
		/*$data = DB::table('mt_user_third')->select('id', 'third_id', 'mobile', 'third_name') ->whereBetween('id', [3601, 4000])->get()->toArray();
		$data = objectToArray($data);
		foreach ($data as $v) {
			$array = ['userId'=>$v['id'], 'type'=>'mobile', 'loginId'=>$v['mobile']];
			if (isset($v['third_name']) && $v['third_name']) {
				$array['nickname'] = $v['third_name'];
			} else {
				$array['nickname'] = $v['mobile'];
			}
			DB::table('userLogin')->insert($array);
			if ($v['third_id']) {
				$array2 = ['userId'=>$v['id'], 'type'=>'wx', 'loginId'=>$v['third_id']];
				if (isset($v['third_name']) && $v['third_name']) {
					$array2['nickname'] = $v['third_name'];
				} else {
					$array2['nickname'] = $v['mobile'];
				}
				DB::table('userLogin')->insert($array2);
			}
		}*/
	}
}

