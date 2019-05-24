<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category\Prop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\PropTemplate;
use App\Modules\ThmartApi\Models\Category;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$category = new Category();
		$propTemplate = new propTemplate();
		if (!$res = $category->getOne($request->input('categoryName'))) return response()->json(['code' => "0", 'message' => '该分类不存在']);
		//$res:当前分类是否存在模板属性
		$res = $propTemplate->getOne($request->input('name'), $request->input('type'), $request->input('categoryName'));
		//如果存在则添加，如果不存在更新当前分类的模板属性
		if (!$res) {
            $propTemplate->addOne($request->input());
		} else {
			$propTemplate->saveOne($request->input());
		}
		//获取该分类以及其子分类的name集合
		$nameArray = sonIdArray($category->getAll(), $request->input('categoryName'));
		//获取该分类下所有子类的指定模板属性
        $propTemplateArray = $propTemplate->getArray($nameArray, $request->input('name'), $request->input('type'));
        //$updateArray:所有需要更新的该分类的子类指定模板属性数组集合
        $updateArray = [];
        //所有需要添加的子类模板属性数组集合
        $addArray = [];
        foreach ($propTemplateArray as &$v) {
        	if (in_array($v['categoryName'], $nameArray)) {
        		//如果该子类存在指定模板属性,那么在$nameArray数组中删除此子类name，该数组之后进行批量添加子类模板操作
                foreach ($nameArray as $key => $value) {
                	if ($value == $v['categoryName']) unset($nameArray[$key]);
                }
                if ($v['isParent'] == 1) {
                	//如果该子类为可继承属性，则把子类name加入$updateArray数组中，之后进行批量更新子类模板操作
	                array_push($updateArray, $v['categoryName']);
                }
        	}
        }
        unset($v);
        $data = $request->input();
        foreach ($nameArray as &$v) {
        	$data['categoryName'] = $v;
        	array_push($addArray, $data);
        }
        //添加子类模板
        $propTemplate->addArray($addArray);
        unset($data['categoryName']);
        //更新子类模板
        $propTemplate->updateArray($updateArray, $data);
        return response()->json(['code' => "1", 'message' => '添加/编辑模板成功']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'name'         => 'required|integer',
			'type'         => 'required|min:0|max:1|integer',
			'categoryName' => 'required|integer',
			'required'     => 'required|min:0|max:1|integer',
			'dataType'     => 'required',
			'needImage'    => 'min:0|max:1|integer',
			'orderby'      => 'integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
		], [
            'name'         => '分类',
            'type'         => '属性',
            'categoryName' => '所属类目',
            'required'     => '是否必填属性',
            'dataType'     => '数据类型',
            'needImage'    => '是否需要图片',
            'orderby'      => '排序',
		]);
		return $validator;
	}
}

