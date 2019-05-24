<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AdminListCat extends Model
{
	protected $table = "adminListCat";
	public $timestamps = false;

    public function getOne($authArray)
    {
        $res = $this->select('id', 'pid as fname', 'name', 'sort', 'url')->get()->toArray();
        //如果用户没有此权限，则不在前台显示
        foreach ($res as $key => $value) {
        	if (!in_array($value['url'], $authArray) && $value['fname'] != 0) unset($res[$key]);
        }
        return $res;
    }
}   