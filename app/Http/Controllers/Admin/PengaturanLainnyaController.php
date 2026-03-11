<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginSettings;
use Illuminate\Support\Facades\Auth;

class PengaturanLainnyaController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $loginSettings = LoginSettings::first();

        return view('admin.pengaturan-lainnya', compact('admin', 'loginSettings'));
    }
}
