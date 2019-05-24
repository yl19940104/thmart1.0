<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Category;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\Brand;
use App\Modules\ThmartApi\Models\Ads;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupBuyingListController extends Controller
{ 
    private $page;
    private $pageSize;

    public function __construct(Request $request)
    {
    	$this->page = $request->input('page');
    	$this->pageSize = $request->input('pageSize');
        $this->terminal = $request->input('terminal');
    }

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
        $item = new Item;
        $param['order'] = str_replace('_', ' ', $request->input('sort'));
        //获取该分类下所有商品最小价格的sku
        $res = $item->getGroupBuyingList($param['order']);
        $count = count($res);
        $res = pageData($res, $this->page, $this->pageSize);
        $res['recommend'] = (new Item)->recommendList();
        $res['banner'] = (new Ads)->getList(37, 3);
        returnJson(1, 'success', $res);
	}

	private function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'pageSize' => 'required|integer',
		    'page'     => 'required|integer',
		    'sort'     => ['required', Rule::in(['price_asc', 'price_desc', 'createTime_desc', 'rand() limit 5', 'sellNumber_desc'])],
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'pageSize' => '每页显示数据量',
            'page'     => '页数',
            'sort'     => '排序',
		]);
		return $validator;
	}
}

