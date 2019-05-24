<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class StaffInfoRole extends Model
{
	protected $table = "staffInfoRole";
	protected $primaryKey = "id";
	/*protected $fillable = ['username', 'password', 'salt', 'lastIp'];*/
	public $timestamps = false;

    public function getList($staff_id)
    {
    	$res = $this->select('roleName')
            ->leftjoin('staffRole', 'staffRole.id', '=', 'staffInfoRole.role_id')
            ->where(['staffInfoRole.staff_id'=>$staff_id])
            ->get();
    	if ($res) return $res->toArray();
    }

    public function getRoleIdList($staff_id)
    {
        $res = $this->select('role_id')
            ->where(['staff_id'=>$staff_id])
            ->get()
            ->toArray();
        return $res;
    }

    public function deleteList($staff_id)
    {
        return $this->where('staff_id', $staff_id)->delete();
    }

    public function saveOne($array)
    {
        return $this->insert($array);
    }
}