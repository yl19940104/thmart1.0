<?php
namespace App\Modules\ThmartApi\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Supplier;
use App\Modules\ThmartApi\Models\SupplierPrecentage;
use App\Modules\ThmartApi\Models\CrontabChangePrice;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnFalse($validator->getMessageBag());
        //修改
        $param = $request->input();
        if (isset($param['id'])) {
            $array = $param;
            unset($array['pointList']);
            $res = (new Supplier)->saveOne($array);
            SupplierPrecentage::where('supplierId', $param['id'])->delete();
            if (isset($param['pointList']) && $param['pointList']) {
                foreach ($param['pointList'] as $key => $value) {
                    if (!$value['catOneId']) returnJson(0, '一级分类必填');
                    if (!$value['catTwoId']) returnJson(0, '二级分类必填');
                    if (!$value['point']) returnJson(0, '扣点必填');
                    if ($value['point'] < 0) returnJson(0, '扣点必须大于等于0');
                    if (floor($value['point']*100) != $value['point']*100) returnJson(0, '扣点最多精确到小数点后两位');
                    if ($value['point']*100 >= 10000) returnJson(0, '扣点百分比不能超过100');
                    (new SupplierPrecentage)->create(['catOneId'=>$value['catOneId'], 'catTwoId'=>$value['catTwoId'], 'point'=>$value['point']*100, 'supplierId'=>$param['id']]);
                    $data = CrontabChangePrice::where('supplierId', $param['id'])->get()->toArray();
                    if (!isset($data) || !$data) CrontabChangePrice::create(['supplierId'=>$param['id']]);
                }
            }
        //添加
        } else {
            $array = $param;
            unset($array['pointList']);
            $array['staff_id'] = session()->get('userInfo')['id'];
            $res = (new Supplier)->addOne($array);
            if (isset($param['pointList']) && $param['pointList']) {
                foreach ($param['pointList'] as $key => $value) {
                    if (!$value['catOneId']) returnJson(0, '一级分类必填');
                    if (!$value['catTwoId']) returnJson(0, '二级分类必填');
                    if (!$value['point']) returnJson(0, '扣点必填');
                    if ($value['point'] < 0) returnJson(0, '扣点必须大于等于0');
                    if (floor($value['point']*100) != $value['point']*100) returnJson(0, '扣点最多精确到小数点后两位');
                    if ($value['point']*100 >= 10000) returnJson(0, '扣点百分比不能超过100');
                    (new SupplierPrecentage)->create(['catOneId'=>$value['catOneId'], 'catTwoId'=>$value['catTwoId'], 'point'=>$value['point']*100, 'supplierId'=>$res['id']]);
                }
            }
        }
        returnJson(1, '操作成功');
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'supplier_name'     => 'required',
            'contacts_name'     => 'required',
            'contacts_phone'    => 'required',
            'contacts_email'    => 'required',
            'contacts_address'  => 'required',
            'param'             => 'required',
            'number'            => 'required',
            'sale'              => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'supplier_name'     => '供应商名称',
            'contacts_name'     => '供应商联系人',
            'contacts_phone'    => '供应商电话',
            'contacts_email'    => '供应商邮箱',
            'contacts_address'  => '供应商地址',
            'param'             => '统一社会信用代码',
            'number'            => '合同编号',
            'sale'              => '采销',
        ]);
        return $validator;
    }
}