<?php
namespace App\Modules\ThmartApi\Http\Controllers\ItemSale;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use Illuminate\Http\Request;

class ExcelEditController extends Controller
{ 
    
    public function __construct(){}

	public function index(Request $request)
	{
        $param = $request->input();
        $data = [];
        $param['excelData'] = json_decode($param['excelData'], true);
        foreach ($param['excelData'] as &$v) {
            $v['startTime'] = str_replace('/', '-', $v['开始时间']);
            $v['endTime'] = str_replace('/', '-', $v['结束时间']);
            $array = [
                'startTime' => strtotime($v['开始时间']),
                'endTime' => strtotime($v['结束时间']),
                'skuNumber' => $v['sku'],
                'salePrice' => $v['促销价'] * 100,
                'rule' => $v['冲突原则'],
            ];
            if (isset($v['拼单人数']) && $v['拼单人数']) $array['amount'] = $v['拼单人数'];
            array_push($data, $array);
        }
        $res = (new ItemSalePrice)->addSalePriceList($data, $param['type']);
        if (isset($res['message']) || $res['message']) returnJson(0,  $res['message']);
        returnJson(1, 'success', $res);
    }
}