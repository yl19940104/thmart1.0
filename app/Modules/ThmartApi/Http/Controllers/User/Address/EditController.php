<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Address;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
		$param['userId'] = $this->userId;
        if (!isset($param['id'])) {
        	return $this->addData($param);
        } else {
        	return $this->saveData($param);
        }
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'fullName'     => 'required',
		    'phone'        => 'required|regex:/^1[34578][0-9]{9}$/',
            'isDefault'    => 'required|integer|min:0|max:1',
            'email'        => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'regex'    => ':attribute 必须为手机号格式',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
		], [
		    'fullname'     => '姓名',
		    'phone'        => '电话',
            'isDefault'    => '是否是默认地址',
            'province'     => '省份',
            'city'         => '城市',
            'email'        => 'email',
            'regionDetail' => '地址详情',
		]);
		return $validator;
	}

    //添加
	private function addData($data)
	{
		if (!isset($data['city']) || !$data['city']) $data['city'] = '';
		//英文地址选填
		if (!isset($data['regionDetail']) || !$data['regionDetail']) $data['regionDetail'] = '';
		if ($data['isDefault'] == 1) $this->cancelDefault($this->userId);
		//如果用户没有地址，则添加的第一条数据默认是默认地址
		if (!((new Address)->getOne($this->userId))) $data['isDefault'] = 1;
        if ((new Address)->addOne($data)) return $this->returnJson(1, 'success'); 
	}

    //保存
	private function saveData($data)
	{
        $address = new Address;
        if ($data['isDefault'] == 1) $this->cancelDefault($this->userId);
        //如果是取消默认地址，需要把剩下地址中最新更新的地址变成默认地址
        if ($data['isDefault'] == 0) {
        	$res = (new Address)->findDefault($data['userId']);
        	if ($res['id'] == $data['id']) Address::setDefault($data['userId']);
        }
        if ($address->saveOne($data)) return $this->returnJson(1, 'success');
	}

    //取消默认地址
	private function cancelDefault($userId)
	{
		$res = (new Address)->findDefault($userId);
		(new Address)->saveOne(['id'=>$res['id'], 'isDefault'=>0]);
	}
}

