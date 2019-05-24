<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/*use App\Modules\ThmartApi\Models\UserLogin;*/
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class ChangeHeadController extends Controller
{

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        $res = base64_image_content($param['headimg'], 'storage/headimg/');
        str_replace('public/', '', $res);
        $data = [
            'id'          => $this->userId,
            'headimg_url' => $res,
        ];
        /*(new User)->saveOne($data);*/
        UserInfo::where('id', $this->userId)->update($data);
        returnJson(1, 'success', ['headimg_url' => adminDomain().$res]);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'headimg' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'headimg' => '头像',
        ]);
        return $validator;
    }
}

