<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class MobileCode extends Model
{
	protected $table = "mobileCode";
    protected $primaryKey = "mobile";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['mobile', 'code', 'time', 'createTime'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($mobile)
    {
        return $this->find($mobile);
    }
    
    public function addOne($data)
    {
        return $this->create($data);
    }

    public function saveOne($data)
    {
        return $this->where(['mobile'=>$data['mobile']])->update($data);
    }
}