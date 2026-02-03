<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CatatanBimbingan;
use App\Models\DataPeriodik;
use App\Models\AdminSekolah;
use App\Services\EffectiveDateService;

class DashboardController extends Controller
{
    public function index()
    {
        $guruBK = Auth::guard('guru_bk')->user();
        $periodik = DataPeriodik::aktif()->first();

        // Check if impersonating
        $isImpersonating = session('impersonating', false);
        
        // Get effective date (supports testing mode)
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $isTesting = $effectiveDate['is_testing'];
        $tanggalFormatted = $effectiveDate['formatted'];

        // Statistics
        $totalCatatan = CatatanBimbingan::where('guru_bk_id', $guruBK->id)->count();
        $catatanBelum = CatatanBimbingan::where('guru_bk_id', $guruBK->id)
            ->where('status', 'Belum')->count();
        $catatanProses = CatatanBimbingan::where('guru_bk_id', $guruBK->id)
            ->where('status', 'Proses')->count();
        $catatanSelesai = CatatanBimbingan::where('guru_bk_id', $guruBK->id)
            ->where('status', 'Selesai')->count();

        return view('guru_bk.dashboard', compact(
            'guruBK',
            'periodik',
            'totalCatatan',
            'catatanBelum',
            'catatanProses',
            'catatanSelesai',
            'isImpersonating',
            'isTesting',
            'tanggalFormatted'
        ));
    }

    /**
     * Stop impersonating and return to admin account
     */
    public function stopImpersonate()
    {
        if (!session('impersonating')) {
            return redirect()->route('guru_bk.dashboard');
        }

        $adminId = session('original_admin_id');
        
        // Logout guru BK
        Auth::guard('guru_bk')->logout();
        
        // Clear impersonation session
        session()->forget(['impersonating', 'impersonate_type', 'original_admin_id', 'original_admin_username']);
        
        // Login back as admin
        $admin = AdminSekolah::find($adminId);
        if ($admin) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.guru-bk.index')->with('success', 'Kembali ke akun admin');
        }

        return redirect()->route('login');
    }
}
