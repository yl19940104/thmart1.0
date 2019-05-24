<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/*use App\Modules\ThmartApi\Models\UserLogin;
use App\Modules\ThmartApi\Models\User;*/
use App\Modules\ThmartApi\Models\UserInfo;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
	protected $userId = null;

    public function __construct(){
    	$this->tokenCheck();
    }

    public function index(Request $request)
    {
    	if ($this->userId) {
    		$res = UserInfo::select('headimg_url', 'nickname')->where('id', $this->userId)->get()->toArray();
	        $address = Address::select('fullName', 'phone', 'email', 'regionDetail', 'province')
	        	->where(['isDefault'=>1, 'userId'=>$this->userId])
	        	->get()
	        	->toArray();
        	$array = [
	        	'pic'       => adminDomain().$res['0']['headimg_url'],
	        	'nickname'  => $res['0']['nickname'],
	        	'data'      => false,
	        	'id'        => $this->userId,
	        ];
	        if (!$array['pic']) $array['pic'] = config('config.headimg');
	        if (isset($address) && $address) {
	        	$array['fullname'] = $address['0']['fullName'];
	        	$array['mobile'] = $address['0']['phone'];
	        	$array['email'] = $address['0']['email'];
	        	$array['address_en'] = $address['0']['regionDetail'];
	        	$array['address_cn'] = $address['0']['province'];
	        }
	        returnJson(1, 'success', $array);
    	} else {
    		returnJson(1, 'success', ['pic' => config('config.headimg'), 'data' => ['login'=>'LOG IN', 'signup' => 'SIGN UP']]);
    	}
    }

    //检查Token
    private function tokenCheck()
    {
        if (isset($_SERVER['HTTP_TOKEN'])) {
        	$sign = substr($_SERVER['HTTP_TOKEN'], 0, 32);
	        $token = substr($_SERVER['HTTP_TOKEN'], 32);
	        //如果计算出的签名和前端传回的签名不一致的话
	        if (md5(md5($token).config('config.tokenSignSalt')) == $sign) {
	            $base64Token = base64_decode($token); 
		        $expire_time = substr($base64Token, 0, 10);
		        //token是否过期
		        if ($expire_time >= time()) {
		            $userId = substr($base64Token, 10);
			        if ($this->userData = 
			        	UserInfo::select('id', 'password', 'salt', 'headimg_url')->get()->toArray()) {
			            $this->userId = $userId;
			        } else {
		        		returnJson(0, 'wrong token');
		        	}
		        } else {
		        	returnJson(0, 'wrong token');
		        }
	        } else {
	        	returnJson(0, 'wrong token');
	        }
        } else {
        	returnJson(1, 'success', ['pic' => config('config.headimg'), 'data' => ['login'=>'LOG IN', 'signup' => 'SIGN UP']]);
        }
    }
}

