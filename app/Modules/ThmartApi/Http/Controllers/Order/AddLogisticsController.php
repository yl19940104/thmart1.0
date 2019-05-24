<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\Logistics;
use Illuminate\Http\Request;

class AddLogisticsController extends Controller
{ 
	public function __construct(){}
	
	public function index(Request $request)
	{
		$param = $request->input();
		if (!isset($param['idArray']) || !$param['idArray']) returnJson(0, '请选择订单');
		$res = (new Logistics)->getOne($param['companyId']);
		/*returnJson(1, $param['idArray']);*/
		foreach ($param['idArray'] as $v) {
			$data = [
				'id' => $v,
				'logistics' => $param['logistics'],
				'company' => $res['0']['no'],
				'logisticsTime' => time(),
			];
			(new OrdersSku)->updateOne($data);
			$data = (new OrdersSku)->getOne($v);
			(new Orders)->saveStatus(['orderNumber'=>$data['0']['orderNumber'], 'status'=>2]);
		}
		returnJson(1, 'Successfully');
	}
}

  