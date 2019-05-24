<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;

class ItemEditDetailController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		//前端需要的sku格式
        $propList = [];
        //sku模板属性个数
        $propNumber = 0;
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
        $res = DB::table('item')
        	->select('type', 'brandName', 'title', 'subTitle', 'enTitle', 'titleLink', 'pic', 'detail')
        	->where(['id'=>$param['itemId']])
        	->get()
        	->toArray();
        $res['0']->picList = DB::table('itemCaroPic')
        	->select('pic')
        	->where('itemId', $param['itemId'])
        	->get()
        	->toArray();
        $sku = DB::table('sku')
        	->where(['itemId'=>$param['itemId'], 'isDelete'=>0])
        	->get()
        	->toArray();
        foreach ($sku as &$v) {
        	$v->propName = json_decode($v->propName);
        	$v->price /= 100;
        	$v->costPrice *= 0.01;
        }
        unset($v);
        foreach ($sku[0]->propName as $key => $val) {
        	$array = [
        		'name' => $key,
        		'id' => $val[1],
        		'arr' => [],
        	];
        	array_push($propList, $array);
        }
        foreach ($propList as &$va) {
        	foreach ($sku as $v) {
	        	foreach ($v->propName as $key => $value) {
	        		if ($va['name'] == $key) {
	        			array_push($va['arr'], $value[0]);
	        		}
	        	}
	        }
        }
        returnJson(1, 'success', ['item'=>$res['0'], 'propList'=>$propList, 'sku'=>$sku, 'test']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'itemId'          => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'itemId'           => '商品编号',
		]);
		return $validator;
	}
}

