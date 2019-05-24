<?php
namespace App\Modules\ThmartApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Orders;
use Illuminate\ChuanglanSmsHelper\ChuanglanSmsApi;

class TokenController extends Controller
{ 

    public function __construct()
    {
        
    }

	public function index(Request $request)
	{
		$token = createToken($request->input('id'));
        return $this->returnJson(1, 'success', ['token'=>$token]);
	}

	public function mail(Request $request)
	{
		sendMail('Autoresponse Email', 'werwerw', '463745854@qq.com');
		$res = (new Orders)->payNotify('182298226869', 1);
		returnJson(1, $res);
	}

	public function code(Request $request)
	{
		$ChuanglanSmsApi = new ChuanglanSmsApi;
		$res = $ChuanglanSmsApi->sendSMS('13816040284', 'The Mid-Autumn Festival is coming, thMart has prepared a special Festive Coupon for you. Visit http://t.cn/EvQ2rGR to get it.');
		returnJson(1, $res);
	}
}

