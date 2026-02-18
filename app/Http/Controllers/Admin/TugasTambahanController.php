<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TugasTambahanController extends Controller
{
    /**
     * Display the tugas tambahan lainnya page
     */
    public function index()
    {
        return view('admin.tugas-tambahan.index');
    }
}
