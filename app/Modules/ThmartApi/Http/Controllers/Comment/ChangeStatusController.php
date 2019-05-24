<?php
namespace App\Modules\ThmartApi\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\MongoComment;
use Illuminate\Http\Request;

class ChangeStatusController extends Controller
{

    public function __construct(){}

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        $condition['audited'] = intval($param['audited']);
        MongoComment::where('_id', $param['id'])->update(['audited'=>$condition['audited']]);
        returnJson(1, 'success');
    }

    private function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id' => 'required',
            'audited' => 'min:0|max:1|integer',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'id'      => '评论id',
            'integer' => ':attribute 只能为整数',
            'min'     => ':attribute 最小为0',
            'max'     => ':attribute 最大为1',
        ]);
        return $validator;
    }
}

