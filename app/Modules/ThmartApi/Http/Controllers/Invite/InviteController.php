<?php
namespace App\Modules\ThmartApi\Http\Controllers\Invite;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\UserInfo;

class InviteController extends Controller
{

    public function __construct(){}

    public function add(Request $request)
    {
        $param = $request->input();
        $res = DB::table('invite_code')->where(['invitecode'=>$param['invitecode']])->get()->toArray();
        if (isset($res) && $res && $res['0']->logid != 0) return redirect($param['returnurl'].'?status=2');
        $res = DB::table('invite_code')->where(['invitecode'=>$param['invitecode'], 'logid'=>0])->get()->toArray();
        if (!isset($res) || !$res) {
            $data = $this->saveData($param, 0);
            return redirect('http://mob.thmart.com.cn/GoodsDetails?id=1162&&logid='.$data['id']);
        } else {
            $data = $this->saveData($param, 1);
            DB::table('invite_code')->where(['id'=>$res['0']->id])->update(['mobile'=>$param['mobile'], 'logid'=>$data['id']]);
            $body = "<p>Dear Attendee,</p>
                                <p>Thank you for your ticket purchase for</p>
                                L'AVENUE Easter Basket DIY
                                <p>Here is your booking code:{$data['code']}</p>
                                <p>Please bring the code along on the day of the event, and your ticket will be available for collection according to your booking code.</a>.</p>
                                <p>For any further enquiries please email <a href='mailto:marketing@urbanatomy.com'>marketing@urbanatomy.com</a>.</p>
                                <p>We look forward to seeing you there!</p>
                                <p>Best regards,</p>";
            sendMail('Autoresponse Email', $body, $param['email']);
            return redirect($param['returnurl'].'?status=1');
        }
    }

    public function saveUserId(Request $request)
    {
        $param = $request->input();
        $userId = $this->token($param['token']);
        if ($userId) {
            $res = DB::table('invite_log')->where(['id'=>$param['logid'], 'status'=>0])->update(['userid'=>$userId]);
            if ($res) {
                returnJson(1, 'success');
            } else {
                returnJson(0, 'fail');
            }
        } else {
            returnJson(0, 'fail');
        }
    }

    private function saveData($data, $status)
    {
        $saveData = [
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'email'  => $data['email'],
            'company' => $data['company'],
            'returnurl' => $data['returnurl'],
            'code' => mt_rand(10000, 99999),
            'status' => $status,
        ];
        $id = DB::table('invite_log')->insertGetId($saveData);
        return ['id'=>$id, 'code'=>$saveData['code']];
    }

    private function token($token)
    {
        $id = 0;
        if (isset($token)) {
            $sign = substr($token, 0, 32);
            $token = substr($token, 32);
            //如果计算出的签名和前端传回的签名不一致的话
            if (md5(md5($token).config('config.tokenSignSalt')) == $sign) {
                $base64Token = base64_decode($token);
                $expire_time = substr($base64Token, 0, 10);
                //token是否过期
                if ($expire_time >= time()) {
                    $userId = substr($base64Token, 10);
                    if (UserInfo::select('id', 'password', 'salt', 'headimg_url')->find($userId)) {
                        $id = $userId;
                    }
                }
            }
        }
        return $id;
    }
}

