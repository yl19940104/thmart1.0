<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\User;
use App\Modules\ThmartApi\Models\UserLogin;

class UserLogin extends Model
{
	protected $table = "userLogin";
	//指定此表主键,find方法直接传入主键值即可调用
	/*protected $primaryKey = "id";*/
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['userId', 'type', 'nickname', 'loginId'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getUserId($type, $loginId)
    {
    	return $this->select('userId', 'nickname')->where(['type' => $type, 'loginId' => $loginId])->get()->toArray();
    }

    //判断改用户的账号是否绑定过微信
    public function getWxDetail($userId)
    {
        return $this->select('userId', 'nickname')->where(['type' => config('config.login_type.wx'), 'userId' => $userId])->get()->toArray();
    }

    //判断改用户的账号是否绑定过PC端微信
    public function getWxPcDetail($userId)
    {
        return $this->select('userId', 'nickname')->where(['type' => config('config.login_type.wxPC'), 'userId' => $userId])->get()->toArray();
    }

    public function addOne($array)
    {
    	return $this->create($array);
    }

    public function getNickName($type, $userId)
    {
        $res = $this->select('nickname', 'type')->where(['type' => $type, 'userId' => $userId])->get();
        if ($res) return $res->toArray();
    }

    public function saveOne($array)
    {
        return $this->where(['userId'=>$array['userId'], 'nickname'=>$array['oldNickName']])->update(['nickname'=>$array['newNickName']]);
    }

    //用户登录小程序时判断是否登录过以及是否绑定过手机号
    public function isLogin($type, $loginId)
    {
        $res = $this->select('userId')
            ->where(['type'=>$type, 'loginId'=>$loginId])
            ->get()
            ->toArray();
        //如果用户从未使用过thmart小程序
        if (!$res) {
            return ['unionid'=>$loginId, 'isRegister'=>0];
        } else {
            return ['token'=>createToken($res[0]['userId']), 'isRegister'=>1];
        }
    }
}