<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Ads;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\CouponSku;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
        //如果有缓存,输出缓存数据
        $param = $request->input();
        if (isset($param['terminal']) && $request->input('terminal') == 'PC') {
            if ($array = Cache::get('homepageDataPc')) returnJson(1, 'success', $array);
        } elseif ($array = Cache::get('homepageDataMobile')) {
            returnJson(1, 'success', $array);
        }
		$ads = new Ads();
        $item = new Item();
        $order = 'createTime desc';
        if (isset($param['terminal']) && $request->input('terminal') == 'PC') {
            $pic = $ads->getList(20, 3);
            $figure = $ads->getList(19, 3);
            $topBanner = $ads->getList(36, 3);
            $array = [
                'figure'       => $figure,
                'deal'         => ['pic' => $pic[0]['pic'], 'data' => $ads->getList(17, 1)],
                'shop'         => ['data' => $ads->getList(5, 2, null, 'order asc')],
                'category'     => 
                    [
                        'ticketing'    => ['pic' => $pic[1]['pic'], 'data' => $item->getMinSkulist('and item.categoryName = 1', $order), 'id'=>'1'],
                        'food'         => ['pic' => $pic[2]['pic'], 'data' => $ads->getList(41, 1), 'id'=>'6'],
                        'flowers'      => ['pic' => $pic[3]['pic'], 'data' => $ads->getList(4, 1), 'id'=>'30'],
                        'liquor'       => ['pic' => $pic[4]['pic'], 'data' => $ads->getList(42, 1), 'id'=>'8'],
                        'homeWare'     => ['pic' => $pic[5]['pic'], 'data' => $ads->getList(29, 1), 'id'=>'19'],
                        'electronic'   => ['pic' => $pic[6]['pic'], 'data' => $ads->getList(33, 1), 'id'=>'11'],
                        'babyProduct'  => ['pic' => $pic[7]['pic'], 'data' => $ads->getList(30, 1), 'id'=>'16'],
                        'toys'         => ['pic' => $pic[8]['pic'], 'data' => $ads->getList(31, 1), 'id'=>'31'],
                        'coupon'       => ['pic' => $pic[9]['pic'], 'data' => $ads->getList(32, 1), 'id'=>'2'],
                        'books'        => ['pic' => $pic[10]['pic'], 'data' => $ads->getList(43, 1), 'id'=>'25'],
                        'adult'        => ['pic' => $pic[12]['pic'], 'data' => $ads->getList(44, 1), 'id'=>'164'],
                    ],
                /*'article'      => ['data' => convertTime($ads->getList(6, 4))],*/
                'exploreChina' => ['pic' => $pic[11]['pic'], 'data' => $item->getMinSkulist('and item.isDelete = 0 and item.audited = 1 and sku.isDelete = 0 and item.categoryName = 160 and item.categoryTwoName = 161', $order, null)],
                'hotProducts'   => ['pic' => $pic[9]['pic'], 'data' => $item->getMinSkulist('and item.isDelete = 0 and item.audited = 1 and sku.isDelete = 0 limit 72', $order)],
                'groupBuying'   => ['data' => (new Item)->getGroupBuyingList('createTime desc', 'limit 8')],
                'topBanner'     => $topBanner,
            ];
        } else {
            $pic = $ads->getList(7, 3);
            $figure = $ads->getList(3, 3);
            $topBanner = $ads->getList(36, 3);
            $array = [
                'figure'       => $figure,
                'deal'         => ['pic' => $pic[0]['pic'], 'data' => $ads->getList(17, 1)],
                'shop'         => ['pic' => $pic[1]['pic'], 'data' => $ads->getList(5, 2, null, 'order asc')],
                'category'     => 
                    [
                        'ticketing'    => ['pic' => $pic[3]['pic'], 'data' => $item->getMinSkulist('and item.categoryName = 1', $order), 'id'=>'1'],
                        'food'         => ['pic' => $pic[4]['pic'], 'data' => $ads->getList(41, 1), 'id'=>'6'],
                        'flowers'      => ['pic' => $pic[5]['pic'], 'data' => $ads->getList(4, 1), 'id'=>'30'],
                        'liquor'       => ['pic' => $pic[6]['pic'], 'data' => $ads->getList(42, 1), 'id'=>'8'],
                        'homeWare'     => ['pic' => $pic[7]['pic'], 'data' => $ads->getList(29, 1), 'id'=>'19'],
                        'electronic'   => ['pic' => $pic[8]['pic'], 'data' => $ads->getList(33, 1), 'id'=>'11'],
                        'babyProduct'  => ['pic' => $pic[9]['pic'], 'data' => $ads->getList(30, 1), 'id'=>'16'],
                        'toys'         => ['pic' => $pic[10]['pic'], 'data' => $ads->getList(31, 1), 'id'=>'31'],
                        'coupon'       => ['pic' => $pic[11]['pic'], 'data' => $ads->getList(32, 1), 'id'=>'2'],
                        'books'        => ['pic' => $pic[14]['pic'], 'data' => $ads->getList(43, 1), 'id'=>'25'],
                        'adult'        => ['pic' => $pic[15]['pic'], 'data' => $ads->getList(44, 1), 'id'=>'164'],
                    ],
                /*'article'      => ['pic' => $pic[12]['pic'], 'data' => convertTime($ads->getList(6, 4))],*/
                'exploreChina' => ['pic' => $pic[12]['pic'], 'data' => $item->getMinSkulist('and item.isDelete = 0 and item.audited = 1 and sku.isDelete = 0 and item.categoryName = 160 and item.categoryTwoName = 161', $order, null)],
                'groupBuying'  => ['pic' => $pic[2]['pic'], 'data' => (new Item)->getGroupBuyingList('createTime desc', 'limit 8')],
                'hotProducts'  => ['pic' => $pic[13]['pic']],
                'topBanner'    => $topBanner,
            ];
        }
        if (isset($param['terminal']) && $request->input('terminal') == 'PC') {
            Cache::put('homepageDataPc', $array, 60);
        } else {
            Cache::put('homepageDataMobile', $array, 60);
        }
        returnJson(1, 'success', $array);
	}
}

