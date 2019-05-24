<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $table = "category";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "name";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['fname', 'title', 'isFinal', 'allowItem'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    //获取所有数据
    public function getAll()
	{
		return $this->all()->toArray();
	}

	//获取所有数据
    public function getList($fname)
	{
		return $this->select('name as id', 'title', 'title_cn')->where(['fname'=>$fname])->get()->toArray();
	}

    //获取一条数据
	public function getOne($name)
	{
		return $this->select('isFinal', 'isDelete', 'title')->find($name);
	}

    //添加一条数据
	public function addOne($array)
	{
		return $this->create($array);
	}

    //更新一条数据
	public function saveOne($array)
	{
		$this->where('name', $array['name'])->update($array);
	}
}