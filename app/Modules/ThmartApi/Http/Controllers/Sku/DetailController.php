<?php
namespace App\Modules\ThmartApi\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 

    public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$sku = new sku();
		$res = $sku->getOne($request->input('id'));
		if (!$res || $res['isDelete'] == 1) returnJson(0, 'skuId不存在');
        $skuDetail = $sku->getDetail($res['id'], $request->input('isSpell'));
        /*$skuDetail['coupon_price'] = null;*/
        $skuDetail['propName'] = json_decode($skuDetail['propName']);
        $skuDetail['pic'] = adminDomain().$skuDetail['pic'];
        returnJson(1, 'success', $skuDetail);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id' =>  'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id' => 'id',
		]);
		return $validator;
	}
}

