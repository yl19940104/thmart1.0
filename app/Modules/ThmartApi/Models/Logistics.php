<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Logistics extends Model
{
	protected $table = "logistics";
    public $timestamps = false;

    public function getOne($id)
    {
    	return $this->select('no')->where(['id'=>$id])->get()->toArray();
    }
}