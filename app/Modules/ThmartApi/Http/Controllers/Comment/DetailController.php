<?php
namespace App\Modules\ThmartApi\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\MongoComment;
use Illuminate\Http\Request;

class DetailController extends Controller
{

    public function __construct(){}

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        $res = MongoComment::where(['_id'=>$param['id']])->get()->toArray();
        foreach ($res as &$v) {
            if (isset($v['info']['pic'])) $v['picList'] = explode('|', $v['info']['pic']);
        }
        unset($v);
        returnJson(1, 'success', $res);
    }

    private function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'id' => '评论id',
        ]);
        return $validator;
    }
}

