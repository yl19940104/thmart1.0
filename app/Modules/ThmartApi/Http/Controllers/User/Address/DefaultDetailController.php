<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Address;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class DefaultDetailController extends Controller
{ 

	public function index(Request $request)
	{
		$res = (new Address)->findDefault($this->userId);
		returnJson(1, 'success', $res);
	}
}

