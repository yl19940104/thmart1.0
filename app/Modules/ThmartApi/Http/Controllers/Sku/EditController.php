<?php
namespace App\Modules\ThmartApi\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\PropTemplate;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\SkuPropValue;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
    private $itemId = 0;
    //该商品分类下的所有sku模板属性
    private $props = null;
    //该商品所属分类
    private $categoryName = 0;

    public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnFalse($validator->getMessageBag());
		}
		$item = new Item();
        $this->itemId = $request->input('itemId');
        if (!$res = $item->getOne($request->input('itemId'))) return response()->json(['code' => "0", 'message' => '商品id不存在']);
        $this->categoryName = $res['categoryName'];
        /*if (empty($this->getProps())) return response()->json(['code' => "0", 'message' => '该商品分类没有模板属性']);*/
        $this->getProps();
        $param = $request->input();
        if (!isset($param['skus'])) {
            $this->deleteItem();
            returnJson(0, '请添加类目属性');
        }
        if (!is_array($request->input('skus'))) {//直接测试接口用json格式传值
            $newSkus = array_map('get_object_vars', json_decode($request->input('skus')));
        } else {//实际用数组形式传值
            $newSkus = $request->input('skus');
        }
        foreach ($newSkus as &$value) {
            if (empty($value['arr']) && empty($value['propName'])) {
                $this->deleteItem();
                returnJson(0, '请完整填写类目属性');
            }
            if (empty($value['price'])) {
                $this->deleteItem();
                returnJson(0, '售价不能为空');
            }
            if (!is_numeric($value['price']) || $value['price'] < 0) {
                $this->deleteItem();
                returnJson(0, '售价必须为数字');
            }
            if (empty($value['costPrice'])) {
                $this->deleteItem();
                returnJson(0, '成本价不能为空');
            }
            if (!is_numeric($value['costPrice']) || $value['costPrice'] < 0) {
                $this->deleteItem();
                returnJson(0, '成本价必须为数字且大于0');
            }
            /*if (empty($value['stock'])) {
                $this->deleteItem();
                returnJson(0, '库存不能为空');
            }*/
            if (!isset($value['stock']) || !is_numeric($value['stock']) || $value['stock'] < 0) {
                $this->deleteItem();
                returnJson(0, '库存必须为整数且大于等于0');
            }
            if (empty($value['pic'])) {
                $this->deleteItem();
                returnJson(0, 'sku图片不能为空');
            }
            /*if (!isset($value['arr'])) returnJson(0, 'sku属性不能为空');*/
            if (isset($value['arr'])) {
                $value['propName'] = $value['arr'];
                unset($value['arr']);
            }
        }
        unset($value);
        $oldSkus = $this->getOldSkus();
        if (!is_array($newSkus = $this->checkNewSkuProp($newSkus))) return response()->json(['code' => "0", 'message' => 'PropName '.$newSkus.' is not exsits']);
        /*foreach($newSkus as $k => $sku)
        {
            $newSkus[$k]['id'] = '';
        }*/
        /*returnJson(1, $newSkus);*/
        if ($this->updateSku($newSkus , $oldSkus)) return response()->json(['code' => "1", 'message' => '操作成功']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'itemId' =>  'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'name' => '商品id',
		]);
		return $validator;
	}

    //获取该商品分类的所有模板属性
	private function getProps()
    {
    	$propTemplate = new PropTemplate();
        if(is_null($this->props))
        {
            $this->props = [];
            if($res = $propTemplate->getList(1))
            {
                foreach($res as $p)
                {
                    $this->props[$p['name']] = [
                            'defaultValue' => $p['defaultValue'],
                            'dataType' => $p['dataType'],
                            'id' => $p['id'],
                            'orderby' => $p['orderby']
                    ];
                }
            }
        }
        return $this->props;
    }

    /**
     * 通过模板编号获取属性名称
     *
     * @param int $propTemplateId
     * @return NULL
     */
    private function getPropName($propTemplateId)
    {
        if(! $propTemplateId)
            return '';
        $this->getProps();
        foreach($this->props as $k => $v)
        {
            if($v['id'] == $propTemplateId)
                return $k;
        }
        return '';
    }
    
    //获取该分类下已有的sku值
    private function getOldSkus()
    {
        $oldskus = [];
        $sku = new sku;
        $res = $sku->join('skupropvalue', 'sku.id', '=', 'skupropvalue.skuId')
            ->select('sku.id as skuId', 'skupropvalue.value', 'skupropvalue.propTemplateId')
            ->where(['itemId'=>$this->itemId])
            ->get()
            ->toArray();
        if ($res) {
            foreach($res as $v)
            {
                $skuId = $v['skuId'];
                if (!isset($oldskus[$skuId])) $oldskus[$skuId] = [];
                $propName = $this->getPropName($v['propTemplateId']);
                if (is_null($propName)) return;
                if($propName)
                    $oldskus[$skuId][$propName] = [
                        $v['value'],
                        $v['propTemplateId']
                    ];
            }
        }
        return $oldskus;
    }

    private function checkNewSkuProp($newSkus)
    {
        foreach($newSkus as $kk => $sku)
        {
            $propName = [];
            if (isset($sku['propName']))
            {
                foreach($sku['propName'] as $pname => $pv)
                {
                    //判断sku模板属性是否存在，目前可以自定义，所以先注释掉
                    /*if(! isset($this->props[$pname]))
                    {
                        return $pname;
                    }
                    if(empty($pv))
                    {
                        $pv = $this->props[$pname]['defaultValue'];
                    }*/
                    //如果是自定义模板，模板id默认是170，顺序默认是1
                    if (!isset($this->props[$pname])) {
                        $propName[$pname] = [
                            $pv,
                            170,
                            1
                        ];
                    //如果不是自定义模板
                    } else {
                        $propName[$pname] = [
                            $pv,
                            $this->props[$pname]['id'],
                            $this->props[$pname]['orderby']
                        ];
                    }
                    /*$propName[$pname] = [
                        $pv,
                        $this->props[$pname]['id'],
                        $this->props[$pname]['orderby']
                    ];*/
                }
            }
            $newSkus[$kk]['propName'] = $propName;
        }
        return $newSkus;
    }

    private function updateSku($newSkus, $oldSkus)
    {
        $insertskus = [];
        $updateskus = [];
        $skupropvalue = [];
        $skupropvalue2 = [];
        $oldSkuId = [];
        $skuIdArray = [];//该商品所有的skuId数组
        $skuIdNotDelete = [];//更新而不删除的skuId
        /*returnJson(1, $newSkus);*/
        foreach($newSkus as $k => $sku)
        {
            if(empty($sku['propName']))
            {
                $propName0 = [];
                $sku['propName'] = '';
            }
            else
            {
                $sku['propName'] = json_encode($sku['propName']);
            }
            $sku['itemId'] = $this->itemId;
            if(isset($sku['id']))
            {
                //把更新而不删除的skuid存进$skuIdNotDelete变量里面
                array_push($skuIdNotDelete, $sku['id']);
                array_push($updateskus , $sku);
            }
            else
            {
            	unset($sku['id']);
                array_push($insertskus , $sku);
            }
        }
        /*returnJson(1, $updateskus);
        returnJson(1, $insertskus);*/
        /*return $insertskus;*/
        $sku = new Sku();
        $skuPropValue = new SkuPropValue();
        //删除该商品没有提交的sku信息

        /*foreach($oldSkus as $skuId => $v)
        {
            array_push($oldSkuId, $skuId);
        }*/
        
        
        $data = DB::table('sku')->select('id')->where(['itemId'=>$this->itemId, 'isDelete'=>0])->get();
        $data = objectToArray($data);
        foreach ($data as $key => $value) {
            array_push($skuIdArray, $value['id']);
        }
        //删除该商品的未提交的所有sku模板属性值
        $skuPropValue->deleteArray($skuIdArray);
        //此时$skuIdArray是未上传，需要删除的skuId数组集合
        foreach ($skuIdArray as $key => $value) {
            if (in_array($value, $skuIdNotDelete)) unset($skuIdArray[$key]);
        }
        //删除该商品未提交的所有sku
        $sku->deleteArray($skuIdArray);
        /*returnJson(1, $insertskus);*/
        foreach ($insertskus as $key => $sku) {
            $sku2 = $sku;
            $sku2['price'] *= 100;
            $sku2['costPrice'] *= 100;
            //添加该商品所提交的所有新的sku
        	$id = sku::insertGetId($sku2);
            sku::where('id', $id)->update(['skuNumber' => '8'.substr(date('Y', time()), 2).str_pad($id, 6, "0", STR_PAD_LEFT)]);
        	/*$newSkus[$key]['id'] = $id;*/
            $insertskus[$key]['propName'] = json_decode($sku['propName']);
            $insertskus[$key]['id'] = $id;
            unset($insertskus[$key]['itemId']);
        }
        if(count($updateskus) > 0)
        {
            //更新该商品所提交的所有旧的sku
            (new Sku)->saveAll($updateskus);
        }
        foreach($insertskus as $k => $sku)
        {
            $p = $sku['propName'];
            foreach($p as $name => $s)
            {
                array_push($skupropvalue , [
                        'skuId' => $sku['id'],
                        'name' => $name,
                        'value' => $s[0],
                        'propTemplateId' => $s[1],
                        'orderby' => $s[2]
                ]);
            }
        }
        if(count($skupropvalue) > 0)
        {
            //添加该商品所提交的所有新的sku模板属性值
            $skuPropValue->saveArray($skupropvalue);
        }
        foreach($updateskus as $k => $sku)
        {      
            $p = json_decode($sku['propName']);
            foreach($p as $name => $s)
            {
                array_push($skupropvalue2 , [
                        'skuId' => $sku['id'],
                        'name' => $name,
                        'value' => $s[0],
                        'propTemplateId' => $s[1],
                        'orderby' => $s[2]
                ]);
            }
        }
        if(count($skupropvalue2) > 0)
        {
            //添加该商品所提交的所有旧的sku模板属性值
            /*returnJson(1, $skupropvalue2);*/
            /*$skuPropValue->saveArray($skupropvalue2);*/
            foreach ($skupropvalue2 as $key => $value) {
                $skuPropValue->saveArray($value);
            }
        }
        return true;
    }

    private function deleteItem()
    {
        $data = (new Sku)->selectItemSku($this->itemId);
        $data = objectToArray($data);
        if (!isset($data) || !$data) (new Item)->deleteOne($this->itemId);
    }
}

