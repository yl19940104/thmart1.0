<?php
namespace App\Modules\ThmartApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\MongoComment;

class MongodbController extends Controller
{

    public function __construct(){}

    public function index(Request $request)
    {
        $res = MongoComment::all();
        returnJson(1, $res);
    }
}

