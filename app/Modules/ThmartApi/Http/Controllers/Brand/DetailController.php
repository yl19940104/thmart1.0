<?php
namespace App\Modules\ThmartApi\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Brand;
use App\Modules\ThmartApi\Models\Ads;
use App\Modules\ThmartApi\Models\UserInfo;
use App\Modules\ThmartApi\Models\UserCollect;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
    protected $userId = null;

    public function __construct(){
        $this->checkToken();
    }

    protected function checkToken()
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
                    $this->userId = substr($base64Token, 10);
                    if (!$this->userData = UserInfo::select('id', 'password', 'salt', 'headimg_url')->find($this->userId)) {
                        $this->userId = null;
                    }
                }
            }
        }
    }
    
	public function index(Request $request)
	{
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$brand = new Brand;
		$ads = new Ads;
		if (!$res = $brand->getDetail($param['id'])) returnJson(0, '该品牌id不存在');
		$res['pic'] = adminDomain().$res['pic'];
		$res['content'] = $ads->getList(10, 3, $param['id']);
        if (isset($param['terminal']) && $param['terminal'] == 'PC') {
            $res['figure'] = $ads->getList(39, 3, $param['id']);
            $banner = $ads->getList(40, 3, $param['id']);
            if (isset($banner['0']) && $banner['0']) {
                $res['banner'] = $banner['0']['pic'];
            } else {
                $res['banner'] = '';
            }
        } else {
            $res['figure'] = $ads->getList(9, 3, $param['id']);
        }
		$res['isCollect'] = 0;
		if ($this->userId) {
            if ($data = (new UserCollect)->getOne(2, $request->input('id'), $this->userId)) {
                if ($data['0']['isCollect'] == 1) $res['isCollect'] = 1;
            }
        }
		returnJson(1, 'success', $res);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'       => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'       => '品牌id',
		]);
		return $validator;
	}
}

