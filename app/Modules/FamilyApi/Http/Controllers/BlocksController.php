<?php
namespace App\Modules\FamilyApi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FamilyApi\Models\Blocks;
use Illuminate\Http\Request;

/**
 *
 */
class BlocksController extends Controller
{
    public function index(Request $request)
    {
        $list = Blocks::where('prefix', '=', 'home-slider')->where('publish', '=', 1)->limit(intval($request->input('limit')))->first();
        $content = explode("\n", $list['content']);
        array_walk($content, function(&$value){
        	$value = explode("\t", $value);
        });
        return $content;
        return $this->returnJson('0000', 'success', Blocks::where('prefix', '=', 'home-slider')->where('publish', '=', 1)->limit(intval($request->input('limit')))->get());
    }
}
