<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/*use App\Modules\ThmartApi\Models\UserLogin;
use App\Modules\ThmartApi\Models\User;*/
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class ChangeNickNameController extends Controller
{ 
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        /*if (!$data = (new UserLogin)->getNickName(config('config.login_type.wx'), $this->userId)) {
            $data = (new UserLogin)->getNickName(config('config.login_type.mobile'), $this->userId);
        }*/
        /*(new UserInfo)->saveOne(['userId'=>$this->userId, 'oldNickName'=>$data['0']['nickname'], 'newNickName'=>$param['nickname']]);*/
        UserInfo::where('id', $this->userId)->update(['nickname' => $param['nickname']]);
        returnJson(1, 'success');
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'nickname' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'nickname' => '用户名',
        ]);
        return $validator;
    }
}

