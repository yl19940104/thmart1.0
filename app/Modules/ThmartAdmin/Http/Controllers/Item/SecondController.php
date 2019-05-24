<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Item;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Supplier;

class SecondController extends Controller
{ 

    public function index(Request $request)
    {
        $compactData = $this->compactData;
        $compactData['categoryOne'] = $request->input('categoryOne');
        //品牌
        $compactData['brand'] = DB::table('brand')->select('id', 'name')->where('isDelete', 0)->get()->toArray();
        //sku规格
        $compactData['supplier'] = DB::table('supplier')->select('id', 'supplier_name')->where(['is_delete'=>0, 'is_effective'=>1])->get()->toArray();
        //分类一
        $compactData['categoryFirst'] = DB::table('category')->select('name', 'title_cn')->where('fname', 0)->get()->toArray();
        /*$compactData['proptemplate'] = DB::table('proptemplate')->select('id', 'name')->where(['type'=>1])->whereNotIn('id', [170])->get()->toArray();
        dump($compactData['proptemplate']);*/
        return view('thmartAdmin::Item/second', compact('compactData'));
    }
}

