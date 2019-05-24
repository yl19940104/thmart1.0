<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PropTemplate extends Model
{
	protected $table = "proptemplate";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['name', 'type', 'categoryName', 'required', 'dataType', 'defauleValue', 'valueList', 'needImage', 'orderby'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

	public function getOne($name, $type, $categoryName, $id=null)
	{
		if (!$id) return $this->select('id')->where(['name'=>$name, 'type'=>$type, 'categoryName'=>$categoryName])->get()->toarray();
		return $this->select('id', 'categoryName', 'type', 'name')->find($id);
	}

    //获取某一分类下指定type的所有模板属性
	static function getList($type)
	{
        return PropTemplate::select('id', 'name', 'defaultValue', 'dataType', 'orderby')->where(['type'=>$type])->get()->toarray();
	}

    //获取某一分类下子类的所有指定模板属性,$nameArray为分类下的所有子类集合数组
	public function getArray($nameArray, $name, $type)
	{
		return $this->select('id', 'isParent', 'categoryName')->whereIn('categoryName', $nameArray)->where(['name'=>$name, 'type'=>$type])->get()->toArray();
	}

	public function addOne($array)
	{
		return $this->create($array);
	}

    //批量添加属性模板
	public function addArray($array)
	{
		return $this->insert($array);
	}

	public function saveOne($array)
	{
		$this->where(['name'=>$array['name'], 'type'=>$array['type'], 'categoryName'=>$array['categoryName']])->update($array);
	}

	//批量更新属性模板
	public function updateArray($categoryNameArray, $data)
	{
		return $this->where(['name'=>$data['name'], 'type'=>$data['type']])->whereIn('categoryName', $categoryNameArray)->update($data);
	}

	//删除属性模板
	public function deleteOne($id)
	{
		return $this->destroy([$id]);
	}
}