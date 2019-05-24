<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Juhe\Juhe;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
/*use App\Modules\ThmartApi\Models\User;*/
use App\Modules\ThmartApi\Models\UserInfo;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $userId;
    protected $userData;

    public function __construct(){
        $this->checkToken();
    }

    protected function returnJson($code, $message, $data = null)
    {
        return response(compact('code', 'message', 'data'));
    }

    //检查Token
    protected function checkToken()
    {
        if (!isset($_SERVER['HTTP_TOKEN'])) returnJson('100', '未传token');
        $sign = substr($_SERVER['HTTP_TOKEN'], 0, 32);
        $token = substr($_SERVER['HTTP_TOKEN'], 32);
        //如果计算出的签名和前端传回的签名不一致的话
        if (md5(md5($token).config('config.tokenSignSalt')) != $sign) {
            returnJson('101', 'token签名错误');
        }
        $base64Token = base64_decode($token); 
        $expire_time = substr($base64Token, 0, 10);
        //token是否过期
        if ($expire_time < time()) {
            returnJson('102', 'token过期');
        }
        $this->userId = substr($base64Token, 10);
        /*if (!$this->userData = (new User)->getOne($this->userId)) {
            returnJson('103', 'token用户不存在');
        }*/
        if (!$this->userData = UserInfo::select('id', 'password', 'salt', 'headimg_url')->find($this->userId)) {
            returnJson('103', 'token用户不存在');
        }

    }

    //查询物流
    protected function queryLogistics($company, $logistics)
    {
        $params = array(
          'key' => '198f3399d4bc3dafa970d416d8f89bfb', //您申请的快递appkey
          'com' => $company, //快递公司编码，可以通过$exp->getComs()获取支持的公司列表
          'no'  => $logistics, //快递编号
        );
        $res = new Juhe($params['key']);
        /*$this->ajaxReturn($res->getComs());*/
        $result = $res->query($params['com'],$params['no']); //执行查询
        return $result;
    }
}
