<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tamu;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    /**
     * Display list of guests
     */
    public function index(Request $request)
    {
        $query = Tamu::query();
        
        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('datang_sebagai', $request->kategori);
        }
        
        // Search by nama or bertemu_dengan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('bertemu_dengan', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }
        
        $tamuList = $query->orderBy('created_at', 'desc')->paginate(20);
        $kategoriOptions = Tamu::getKategoriOptions();
        
        // Stats
        $totalHariIni = Tamu::whereDate('created_at', today())->count();
        $totalBulanIni = Tamu::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalSemua = Tamu::count();
        
        return view('admin.tamu.index', compact(
            'tamuList',
            'kategoriOptions',
            'totalHariIni',
            'totalBulanIni',
            'totalSemua'
        ));
    }
    
    /**
     * Delete guest record
     */
    public function destroy($id)
    {
        $tamu = Tamu::findOrFail($id);
        $tamu->delete();
        
        return redirect()->route('admin.tamu.index')
            ->with('success', 'Data tamu berhasil dihapus!');
    }
    
    /**
     * Show detail of a guest
     */
    public function show($id)
    {
        $tamu = Tamu::findOrFail($id);
        return view('admin.tamu.show', compact('tamu'));
    }
}
