<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Category;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\Brand;
use App\Modules\ThmartApi\Models\UserInfo;
use App\Modules\ThmartApi\Models\UserCollect;
use App\Modules\ThmartApi\Models\Ads;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListController extends Controller
{ 
    private $page;
    private $pageSize;
    protected $userId=null;

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
        $category = new Category();
        $item = new Item;
        $brand = new Brand;
        //如果id为-1代表查询除了coupon和ticketing之外的所有分类数据
        /*if ($request->input('id') && $request->input('id') != '-1') {
        	if (!$category->getOne($request->input('id'))) returnJson(0, '该分类不存在');
        	$param['where'] = 'and item.categoryName = '.$request->input('id');
        } elseif ($request->input('id') == '-1') {
            $param['where'] = 'and item.categoryName not in (1, 2)';
        } else {
        	$param['where'] = '';
        }
        if ($search = $request->input('search')) {
            //过滤用户输入
            $search = '%'.addslashes($search).'%';
            $param['where'] = $param['where']." and item.title like ?";
        }*/
        $param['where'] = '';
        //指定商品分类(一级分类)
        if ($request->input('catOneId')) {
            if (!$categoryName = $category->getOne($request->input('catOneId'))) returnJson(0, '该分类不存在');
            $param['where'] = $param['where'].'and item.categoryName = '.$request->input('catOneId');
        }
        //指定商品分类(二级分类)
        if ($request->input('catTwoId')) {
            if (!$categoryName = $category->getOne($request->input('catTwoId'))) returnJson(0, '该分类不存在');
            $param['where'] = $param['where'].' and item.categoryTwoName = '.$request->input('catTwoId');
        }
        //指定商品分类(三级分类)
        if ($request->input('catThreeId')) {
            if (!$categoryName = $category->getOne($request->input('catThreeId'))) returnJson(0, '该分类不存在');
            $param['where'] = $param['where'].' and item.categoryThreeName = '.$request->input('catThreeId');
        }
        //指定查询字段
        if ($search = $request->input('search')) {
            //过滤用户输入
            $search = '%'.addslashes($search).'%';
            $param['where'] = $param['where']." and item.title like ?";
        }
        //指定品牌id
        if ($request->input('brandId')) {
            if (!$brand->getOne($request->input('brandId'))) returnJson(0, '该品牌不存在');
            $param['where'] = $param['where']." and item.brandName =".$request->input('brandId');
        }
        $sort = $request->input('sort');
        if ($sort == 'createTime_desc') $sort = 'id_desc';
        $param['order'] = str_replace('_', ' ', $sort);
        //获取该分类下所有商品最小价格的sku
        $res = $item->getMinSkulist($param['where'], $param['order'], $search);
        $count = count($res);
        if ($request->input('sort') == 'price_desc' || $request->input('sort') == 'price_asc') {
            //冒泡排序排列商品价格
            for ($i = 0; $i < $count - 1; $i++) {
                for ($j = 0; $j < $count - 1 - $i; $j++) {
                    $left = $res[$j]['price'];
                    $right = $res[$j+1]['price'];
                    //价格从大到小
                    if ($request->input('sort') == 'price_desc') {
                        if ($left < $right) {
                            $middle = $res[$j];
                            $res[$j] = $res[$j+1];
                            $res[$j+1] = $middle;                     
                        }
                    }
                    //价格从小到大
                    if ($request->input('sort') == 'price_asc') {
                        if ($left > $right) {
                            $middle = $res[$j];
                            $res[$j] = $res[$j+1];
                            $res[$j+1] = $middle;                     
                        }
                    }
                }
            }
        }
        $res = pageData($res, $this->page, $this->pageSize);
        $res['recommend'] = (new Item)->recommendList();
        if ($request->input('catOneId') || $request->input('catTwoId')) $res['categoryName'] = $categoryName['title'];
        if ($request->input('brandId')) {
            $brandData = (new Brand)->getDetail($request->input('brandId'));
            $banner = (new Ads)->getList(40, 3, $request->input('brandId'));
            $res['brand'] = [
                'id'   => $brandData['id'],
                'name' => $brandData['name'],
                'pic'  => adminDomain().$brandData['pic'],
            ];
            if (isset($banner) && $banner) $res['brand']['banner'] = $banner['0']['pic'];
            if ($this->userId) {
                $res['brand']['isCollect'] = (new UserCollect)->getStatus(2, $request->input('brandId'), $this->userId);
            } else {
                $res['brand']['isCollect'] = 0;
            }
        }
        returnJson(1, 'success', $res);
	}

    public function itemList()
    {
        $res = Item::select('id', 'title')->where(['isDelete'=>0, 'audited'=>1])->get()->toArray();
        returnJson(1, 'success', $res);
    }

	private function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'pageSize' => 'required|integer',
		    'page'     => 'required|integer',
            'brandId'  => 'integer',
		    'sort'     => ['required', Rule::in(['price_asc', 'price_desc', 'sellNumber_desc', 'createTime_desc'])],
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'       => '分类id',
            'pageSize' => '每页显示数据量',
            'page'     => '页数',
            'sort'     => '排序',
		]);
		return $validator;
	}
}

