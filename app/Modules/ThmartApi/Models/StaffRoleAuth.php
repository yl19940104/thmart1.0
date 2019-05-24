<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class staffRoleAuth extends Model
{
	protected $table = "staffRoleAuth";
	protected $fillable = ['role_id', 'auth_id'];
	public $timestamps = false;

    public function saveOne($array)
    {
        return $this->create($array);
    }

    public function deleteList($role_id)
    {
        return $this->where('role_id', $role_id)->delete();
    }
}