<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ItemCaroPic extends Model
{
	protected $table = "itemCaroPic";
	protected $primaryKey = "id";
	protected $fillable = ['itemId', 'pic'];
	public $timestamps = false;

    public function getList($itemId)
    {
        return $this->select('pic')->where(['itemId'=>$itemId])->get()->toArray();
    }

    public function addOne($array)
    {
        return $this->create($array);
    }

    public function saveOne($array)
    {
        return $this->where('id', $array['id'])->update($array);
    }

    public function deleteItemAllPic($itemId)
    {
        return $this->where('itemId', $itemId)->delete();
    }
}