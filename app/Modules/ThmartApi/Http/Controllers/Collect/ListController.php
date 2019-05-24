<?php
namespace App\Modules\ThmartApi\Http\Controllers\Collect;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserCollect;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Brand;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use Illuminate\Http\Request;

class ListController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
		if ($param['type'] == 1) {
			$res = (new UserCollect)->getItemList($this->userId, $param['type']);
			foreach ($res as $k => &$v) {
				$v['id'] = $v['contentId'];
				unset($v['contentId']);
				if ($param['type'] == 1) {
					//查询每个商品最低原价或最低促销价
					$data =  DB::select("SELECT * FROM
	                        (select price, item.id, item.pic, item.title from item 
	                        left join sku 
	                        on item.id = sku.itemId
	                        where sku.isDelete = 0 and item.isDelete = 0 and item.audited = 1 and item.id = {$v['id']} 
	                        order by price asc) 
	                        as a group by a.id");
					$data = objectToArray($data);
					if (isset($data) && $data) {
						$v['price'] = $data['0']['price'] * 0.01;
						$v['pic'] = adminDomain().$data['0']['pic'];
						$v['title'] = $data['0']['title'];
					} else {
						unset($res[$k]);
					}
				}
			}
			unset($v);
			$res = (new ItemSalePrice)->addArrayMinSalePrice($res);
			$res = objectToArray($res);
			$data = pageData($res, $param['page'], $param['pageSize']);
			returnJson(1, 'success', $data);
		} else {
			$res = (new UserCollect)->getList($this->userId, $param['type'], $param['pageSize']);
			foreach ($res['data'] as &$v) {
				$data = (new Brand)->getDetail($v['contentId']);
				$v['id']= $v['contentId'];
				unset($v['id']);
				$v['pic'] = adminDomain().$data['pic'];
				$v['name'] = $data['name'];
			}
			returnJson(1, 'success', ['data' => $res['data'], 'totalPage'=>$res['last_page']]);
		}
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'type'      => 'required|integer|min:1|max:2',
		    'pageSize'  => 'required|integer',
		    'page'      => 'required|integer',
		], [
            'min'      => ':attribute 最小为1',
            'min'      => ':attribute 最大为2',
		], [
            'type'      => '类型id',
            'pageSize'  => '每页显示数据量',
            'page'      => '当前页数',
		]);
		return $validator;
	}
}