<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LoginController extends Controller
{ 

    public function __construct(){}

    public function index(Request $request)
    {
        if (session()->get('userInfo')) return redirect('/thmartAdmin/homepage');
    	return view('thmartAdmin::User/login');
    }

    public function logout(Request $request)
    {
        session()->forget('userInfo');
        return redirect('/thmartAdmin/login');
    }
}

