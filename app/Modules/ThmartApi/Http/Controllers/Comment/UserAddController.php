<?php
namespace App\Modules\ThmartApi\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\MongoComment;
use Illuminate\Http\Request;

class UserAddController extends Controller
{

    public function index(Request $request)
    {
        $param = $request->input();
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            returnFalse($validator->getMessageBag());
        }
        /*if (strlen($param['comment']) < 9) returnJson(0, "评论长度不能小于3个字符");
        if (strlen($param['comment']) >200) returnJson(0, "评论长度不能大于200个字符");*/
        $mongoComment = new MongoComment;
        $param['userId'] = $this->userId;
        $mongoComment->userAddData($param);
        returnJson(1, 'success');
    }

    private function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'comment'   => 'required',
            'id'        => 'required|integer',
        ], [
            'required'  => ':attribute 为必填项',
            'integer'   => ':attribute 必须为数字',
        ], [
            'comment'   => '评论内容',
            'id'        => 'orderskuId',
        ]);
        return $validator;
    }
}

