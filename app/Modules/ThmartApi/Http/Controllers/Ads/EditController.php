<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Ads;
use App\Modules\ThmartApi\Models\AdsPosition;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Supplier;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
	public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$adsPosition = new AdsPosition();
		$ads = new Ads();
		$item = new item();
		$supplier = new Supplier();
	    if (!$res = $adsPosition->getOneById($request->input('adsPositionId'))) returnJson(0, '广告位置id不存在');
	    $res = json_decode(json_encode($res), true);
	    /*if (!$request->input('contentId') && !$request->input('url')) returnJson(0, '内容id或url其中之一必填');*/
	    if ($res['type'] == 1 && !$item->getOne($request->input('contentId'))) returnJson(0, '该商品id不存在');
	    if ($res['type'] == 2 && !$supplier->getOne($request->input('contentId'))) returnJson(0, '该品牌id不存在');
	    if ($res['type'] == 3 && !$request->input('pic')) returnJson(0, '请添加图片');
	    /*if ($res['type'] == 4 && !$request->input('pic')) returnJson(0, '请添加超链接或图片');*/
	    if (!$request->input('id')) {
	    	if ($ads->addOne($request->input())) returnJson(1, '添加成功');
	    } else {
	    	if (!$ads->getOne($request->input('id'))) returnJson(0, '该广告id不存在');
	    	$param = $request->input();
	    	unset($param['type']);
	    	if ($ads->saveOne($param)) returnJson(1, '修改成功');
	    }
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'            => 'integer',
		    'adsPositionId' => 'required|integer',
		    'contentId'     => 'integer',
            'order'         => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
		], [
            'id'            => '广告id',
		    'adsPositionId' => '广告位置id',
		    'contentId'     => '内容id',
		    'order'         => '顺序',
		]);
		return $validator;
	}
}

