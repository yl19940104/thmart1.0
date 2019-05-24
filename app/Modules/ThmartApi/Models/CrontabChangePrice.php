<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CrontabChangePrice extends Model
{
	protected $table = "crontab_changePrice";

	protected $primaryKey = "id";

	protected $fillable = ['supplierId'];

    public $timestamps = true;

    public function getSupplierIdArray()
    {
    	$array = [];
    	$res = $this->get()->toArray();
    	if (isset($res) && $res) {
    		foreach ($res as $v) {
	    		array_push($array, $v['supplierId']);
	    	}
	    	return $array;
    	}
    }
}