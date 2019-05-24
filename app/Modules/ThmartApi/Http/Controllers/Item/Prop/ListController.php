<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item\Prop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        $res = Sku::select('propName', 'id as skuId')->where(['isDelete'=>'0', 'itemId'=>$param['id']])->get()->toArray();
        foreach ($res as &$v) {
            $v['propName'] = json_decode($v['propName']);
            $v['value'] = '';
            foreach ($v['propName'] as $key => $value) {
                $v['value'] .= ' '.$key.':'.$value['0'];
            }
        }
        unset($v);
        returnJson(1, 'success', $res);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id'         => 'required|integer',
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
        ], [
            'id'         => '商品编号',
        ]);
        return $validator;
    }
}

