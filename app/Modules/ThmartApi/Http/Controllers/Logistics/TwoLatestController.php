<?php
namespace App\Modules\ThmartApi\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\OrdersSku;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/*
 * 个人中心最新物流
 */
class TwoLatestController extends Controller
{ 

	public function index(Request $request)
	{
		$res = (new OrdersSku)->TwoLatestLogistics($this->userId);
		$data = [];
		if ($res) {
			foreach ($res as $k => $v) {
				$result = $this->queryLogistics($v['company'], $v['logistics']);
				if ($result['error_code'] == 0) {
					$data[$k]['company'] = $v['company'];
					$data[$k]['logistics'] = $v['logistics'];
					$data[$k]['info'] = end($result['result']['list']);
				}
			}
		}
		returnJson(1, 'success', $data);
	}
}

  