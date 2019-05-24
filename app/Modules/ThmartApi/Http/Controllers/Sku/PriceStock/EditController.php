<?php
namespace App\Modules\ThmartApi\Http\Controllers\Sku\PriceStock;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
        $sku = new Sku;
        $array = json_decode($request->input('data'), true);
        if (!is_array($array)) return response()->json(['code' => "0", 'message' => '参数格式错误']);
		foreach ($array as $v) {
            $v['price'] = $v['price'] * 100;
            $v['costPrice'] = $v['costPrice'] * 100;
            $validator = $this->validateParam($v);
            if ($validator->fails()) {
                return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
            }
            if (!$sku->getOne($v['id'])) return response()->json(['code' => "0", 'message' => 'skuid '.$v['id'].' 不存在']);
            $sku->saveOne($v);
        }
        return response()->json(['code' => "1", 'message' => '保存成功']);
	}

    public function validateParam($param)
    {
        $validator = \Validator::make($param, [
            'id'        => 'required|integer',
            'stock'     => 'integer',
            'price'     => 'integer',
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 数值错误',
        ], [
            'name' => '商品id',
        ]);
        return $validator;
    }
}

