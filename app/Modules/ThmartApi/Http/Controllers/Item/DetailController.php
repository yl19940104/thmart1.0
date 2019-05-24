<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\OrdersSpell;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\ItemCaroPic;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\UserInfo;
use App\Modules\ThmartApi\Models\UserCollect;
use App\Modules\ThmartApi\Models\Ads;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 

    protected $userId = null;

    public function __construct(){
        $this->checkToken();
    }

    protected function checkToken()
    {
        if (isset($_SERVER['HTTP_TOKEN'])) {
            $sign = substr($_SERVER['HTTP_TOKEN'], 0, 32);
            $token = substr($_SERVER['HTTP_TOKEN'], 32);
            //如果计算出的签名和前端传回的签名不一致的话
            if (md5(md5($token).config('config.tokenSignSalt')) == $sign) {
                $base64Token = base64_decode($token); 
                $expire_time = substr($base64Token, 0, 10);
                //token是否过期
                if ($expire_time >= time()) {
                    $this->userId = substr($base64Token, 10);
                    if (!$this->userData = UserInfo::select('id', 'password', 'salt', 'headimg_url')->find($this->userId)) {
                        $this->userId = null;
                    }
                }
            }
        }
    }
    
	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnJson(0, $validator->getMessageBag());
		}
        $item = new Item();
        $itemCaroPic = new ItemCaroPic();
        $sku = new Sku();
        $res = $item->getDetail($request->input('id'));
        $res['singleBuyPrice'] = '';//如果这个商品有拼单价，那么这个值就是拼单价之外的商品最低价
        if ($res['type'] == 2) {
            //此字段表示该商品是电子票
            $res['isTicketing'] = true;
        } else {
            $res['isTicketing'] = false;
        }
        if (!$res || $res['isDelete'] == 1) returnJson(0, '商品Id不存在');
        $res['price'] = $sku->getMinPrice($res['id']);
        $figure = $itemCaroPic->getList($res['id']);
        $res['figure'] = [];
        foreach ($figure as &$v) {
            $v['pic'] = adminDomain().$v['pic'];
        	array_push($res['figure'], $v['pic']);
        }
        //正则替换src为绝对路径
        $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.JPG]))[\'|\"].*?[\/]?>/"; 
        preg_match_all($pattern, $res['detail'], $match); 
        foreach ($match['1'] as &$v) {
            $url = adminDomain().$v;
            $res['detail'] = str_replace($v, $url, $res['detail']);
        }
        //如果未设置轮播图，则调用主图
        if (!$res['figure']) array_push($res['figure'], adminDomain().$res['pic']);
        unset($v);
        $skuList = $sku->getList($res['id']);
        foreach ($skuList as &$v) {
        	$v['propName'] = json_decode($v['propName'], true);
        }
        unset($v);
        //如果某个sku库存为0则不显示在前台
        $res['skuList'] = [];
        foreach ($skuList as $key => $val) {
            $data = (new Sku)->getOne($val['id']);
            if ($data['stock'] <= 0) {
                unset($skuList[$key]);
            } else {
                array_push($res['skuList'], $val);                
            }
        }
        $res['minPrice'] = (new Sku)->getMinPrice($res['id']);
        $res['maxPrice'] = (new Sku)->getMaxPrice($res['id']);
        $res['sumStock'] = (new Sku)->getSumStock($res['id']);
        $res['pic'] = adminDomain().$res['pic'];
        if ($this->userId) {
            if ($data = (new UserCollect)->getOne(1, $request->input('id'), $this->userId)) {
                if ($data['0']['isCollect'] == 1) $res['isCollect'] = 1;
            }
        }
        $groupPrice = (new ItemSalePrice)->hasSalePrice($request->input('id'), 2);
        $spellPrice = (new ItemSalePrice)->hasSalePrice($request->input('id'), 3);
        if (isset($groupPrice)) {
            $res['type'] = 'group';
            //团购最低价信息
            $res['group'] = [
                'groupPrice' => $groupPrice['salePrice'],
                'currentTime' => time(),
                'endTime' => $groupPrice['endTime']
            ];
        } elseif (isset($spellPrice)) {
            $res['type'] = 'spell';
            //拼单最低价信息
            $res['group'] = [
                'groupPrice' => $spellPrice['salePrice'],
                'currentTime' => time(),
                'endTime' => $spellPrice['endTime']
            ];
            $res['singleBuyPrice'] = (new Sku)->getMinPrice($res['id'], 1);
            $res['singleBuyPriceMax'] = (new Sku)->getMaxPrice($res['id']);
            $res['maxPrice'] = (new Sku)->getMaxPrice($res['id'], 1);
        } else {
            $res['group'] = [];
            $salePrice = (new ItemSalePrice)->hasSalePrice($request->input('id'), 1);
            if (isset($salePrice)) {
                $res['type'] = 'sale';
            } else {
                $res['type'] = 'none';
            }
        } 
        //满减信息
        $overReduce = (new Sku)->getOverReduceList($request->input('id'));
        //商品涉及到的所有优惠券
        $res['couponList'] = (new Sku)->getCouponList($request->input('id'), $this->userId);
        if ($overReduce) {
            $res['overReduce'] = ['over'=>$overReduce['0']['over'], 'reduce'=>$overReduce['0']['reduce'], 'name'=>$overReduce['0']['name']];
        } else {
            $res['overReduce'] = [];
        }
        //推荐商品
        $res['recommend'] = (new Item)->recommendList($request->input('id'), $res['categoryName']);
        /*returnJson(1, $res['recommend']);*/
        /*$count = count($res['recommend']);
        if ($count < 5) {
            $amount = 5 - $count;
            $additionComment = (new Item)->recommendList($request->input('id'), $res['categoryName'], 0, $amount);
            $res['recommend'] = array_merge($res['recommend'], $additionComment);
        }*/
        //如果用户登录状态，返回是否收藏商品和商户
        if ($this->userId) {   
            $res['brand']['isCollect'] = (new UserCollect)->getStatus(2, $res['brand']['id'], $this->userId);
            $res['isCollect'] = (new UserCollect)->getStatus(1, $request->input('id'), $this->userId);
        } else {
            $res['brand']['isCollect'] = 0;
            $res['isCollect'] = 0;
        }
        $banner = (new Ads)->getList(40, 3, $res['brand']['id']);
        if (isset($banner) && $banner) $res['brand']['banner'] = $banner['0']['pic'];
        /*$res['spellList'] = (new OrdersSpell())->getList($res['id']);*/
        $res['spellInfo'] = [
            'total' => (new OrdersSpell())->getTotalNumber($res['id']),
            'spellList' => (new OrdersSpell())->getList($res['id']),
        ];
        returnJson(1, 'success', $res);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'          => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'           => '商品编号',
		]);
		return $validator;
	}
}