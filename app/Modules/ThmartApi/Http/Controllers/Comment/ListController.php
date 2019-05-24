<?php
namespace App\Modules\ThmartApi\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\MongoComment;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class ListController extends Controller
{

    public function __construct(Request $request)
    {
        $this->page = $request->input('page');
        $this->pageSize = $request->input('pageSize');
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
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        $param['pageSize'] = intval($param['pageSize']);
        $userIdArray = [];
        $userInfo = [];
        $condition = [
            'isDelete' => 0,
            'audited'  => 1,
        ];
        if ($param['id']) $condition['goodsId'] = intval($param['id']);
        (isset($param['hasPic']) && $param['hasPic']) ? $condition['hasPic'] = intval($param['hasPic']) : 0;
        $goods = Item::where(['id'=>$param['id']])->find($param['id']);
        if (!$goods) returnJson(0, 'wrong goodsId');
        $res = MongoComment::where($condition)->orderby('created_at', 'desc')->paginate($param['pageSize'])->toArray();
        $number = MongoComment::where($condition)->count();
        foreach ($res['data'] as &$v) {
            if (isset($v['info']['pic'])) {
                $v['info']['pic'] = explode('|', $v['info']['pic']);
            }
            $v['info']['goodsPic'] = adminDomain().$v['info']['goodsPic'];
            if (isset($v['info']['pic'])) {
                foreach ($v['info']['pic'] as &$value) {
                    $value = adminDomain().$value;
                }
            }
            unset($value);
            if (isset($v['userId']) && !in_array($v['userId'], $userIdArray)) array_push($userIdArray, $v['userId']);
        }
        unset($v);
        $data = UserInfo::select('id', 'nickname', 'headimg_url')->whereIn('id', $userIdArray)->get()->toArray();
        foreach ($data as &$v) {
            $v['headimg_url'] = $v['headimg_url'];
            $userInfo[$v['id']] = $v;
        }
        unset($v);
        foreach ($res['data'] as &$v) {
            if (isset($v['userId'])) {
                $v['info']['username'] = $userInfo[$v['userId']]['nickname'];
                $v['info']['headimg_url'] = adminDomain().$userInfo[$v['userId']]['headimg_url'];
            }
            $v['brandName'] = $v['info']['brandName'];
            $v['total'] = $v['info']['number'];
            $v['data'] = $v['info'];
            $v['data']['goodsName'] = $v['info']['title'];
            $v['data']['prop'] = $v['info']['propName'];
            if (isset($v['info']['pic'])) $v['data']['picList'] = $v['info']['pic'];
            $v['data']['pic'] = $v['info']['goodsPic'];
            $v['info']['price'] *= 0.01;
            unset($v['info']);
            $v['data']['goodId'] = $v['goodsId'];
        }
        unset($v);
        returnJson(1, 'success', ['data'=>$res['data'], 'totalPage'=>$res['last_page'], 'number'=>$number]);
    }

    private function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'page'     => 'required|integer',
            'pageSize' => 'required|integer',
            'id'       => 'required|integer',
            'hasPic'   => 'required|integer',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'page'     => '当前页',
            'pageSize' => '每页显示数据量',
            'id'       => '商品id',
            'hasPic'   => 'hasPic',
        ]);
        return $validator;
    }
}

