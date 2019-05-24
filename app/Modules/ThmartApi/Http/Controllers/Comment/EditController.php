<?php
namespace App\Modules\ThmartApi\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\MongoComment;
use Illuminate\Http\Request;

class EditController extends Controller
{

    public function __construct(){}

    public function index(Request $request)
    {
        $param = $request->input();
        if (isset($param['id']) && $param['id']) {
            $validator = $this->validateParamUpdate($request);
        } else {
            $validator = $this->validateParamAdd($request);
        }
        if ($validator->fails()) {
            returnFalse($validator->getMessageBag());
        }
        if (strlen($param['comment']) < 9) returnJson(0, "评论长度不能小于3个字符");
        if (strlen($param['comment']) >200) returnJson(0, "评论长度不能大于200个字符");
        if (isset($param['reply']) && strlen($param['reply']) < 9) returnJson(0, "评论长度不能小于3个字符");
        if (isset($param['reply']) && strlen($param['reply']) >200) returnJson(0, "评论长度不能大于200个字符");
        $mongoComment = new MongoComment;
        if (isset($param['id']) && $param['id']) {
            $mongoComment->updateData($param);
        } else {
            $mongoComment->addData($param);
        }
        returnJson(1, 'success');
    }

    private function validateParamAdd(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'comment'   => 'required',
            'goodsId'   => 'required|integer',
            'number'    => 'required|integer',
            'skuId'     => 'required|integer',
            'username'  => 'required',
        ], [
            'required'  => ':attribute 为必填项',
            'integer'   => ':attribute 必须为数字',
        ], [
            'comment'   => '评论内容',
            'goodsId'   => '商品',
            'skuId'     => '规格',
            'username'  => '昵称',
        ]);
        return $validator;
    }

    private function validateParamUpdate(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'comment'   => 'required',
            'goodsId'   => 'required|integer',
            'number'    => 'required|integer',
            'skuId'     => 'required|integer',
        ], [
            'required'       => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
        ], [
            'comment'   => '评论内容',
            'goodsId'   => '商品',
            'skuId'     => '规格',
        ]);
        return $validator;
    }
}

