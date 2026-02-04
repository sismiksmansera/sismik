@extends('layouts.app')

@section('title', 'Anggota {{ $rombel->nama_rombel }} | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        padding: 24px 30px;
        border-radius: 16px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-content { display: flex; align-items: center; gap: 20px; }
    .header-icon {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    .header-text h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
    .header-text p { opacity: 0.9; font-size: 14px; margin: 0; }
    
    .rombel-info-card {
        background: white;
        border-left: 4px solid var(--primary);
        padding: 12px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .rombel-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .rombel-info-text h5 { margin: 0 0 2px 0; font-size: 16px; font-weight: 700; color: var(--gray-800); }
    .rombel-info-text span { font-size: 12px; color: var(--gray-500); }
    
    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .section-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: white;
    }
    .section-header h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #047857;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .action-buttons-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .action-buttons-group .btn {
        font-size: 12px;
        padding: 8px 14px;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .modern-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .modern-table th {
        background: linear-gradient(0deg, #059669 0%, #047857 100%);
        color: white;
        padding: 14px 12px;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
    }
    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    .modern-table tbody tr:hover { background: var(--gray-50); }
    
    .student-info { display: flex; align-items: center; gap: 10px; }
    .student-name { font-weight: 600; color: var(--gray-800); }
    
    .gender-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .gender-l { background: #dbeafe; color: #1e40af; }
    .gender-p { background: #fce7f3; color: #be185d; }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        color: white;
        font-size: 12px;
        transition: all 0.2s;
    }
    .action-btn:hover { transform: translateY(-2px); color: white; }
    .btn-info-action { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .btn-success-action { background: linear-gradient(135deg, #10b981, #059669); }
    
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: var(--gray-500);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--gray-300); }
    
    /* Rekapitulasi Table */
    .rekap-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rekap-table th {
        background: linear-gradient(0deg, #f59e0b, #d97706);
        color: white;
        padding: 12px;
        text-align: center;
        font-weight: 600;
    }
    .rekap-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--gray-200);
        text-align: center;
    }
    .rekap-table .total-row {
        background: #dbeafe;
        font-weight: 700;
        color: #1e40af;
    }
    
    .login-as-btn {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        text-decoration: none;
        background: rgba(6, 182, 212, 0.15);
        color: #06b6d4;
        transition: all 0.2s;
        margin-left: 8px;
    }
    .login-as-btn:hover {
        background: #06b6d4;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="header-text">
                    <h1>Anggota Rombel</h1>
                    <p>{{ $tahunPelajaran }} - Semester {{ $semester }}</p>
                </div>
            </div>
            
            <div class="rombel-info-card">
                <div class="rombel-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="rombel-info-text">
                    <h5>{{ $rombel->nama_rombel }}</h5>
                    <span><i class="fas fa-user-tie"></i> {{ $rombel->wali_kelas ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Daftar Siswa Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list-alt"></i> Daftar Siswa</h2>
                <div class="action-buttons-group">
                    <a href="#" class="btn" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                        <i class="fas fa-sliders-h"></i> Katrol Nilai
                    </a>
                    <a href="{{ route('admin.leger.print-katrol', ['rombel_id' => $rombel->id, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" 
                       class="btn" style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: white;" target="_blank">
                        <i class="fas fa-chart-line"></i> Leger Katrol
                    </a>
                    <a href="{{ route('admin.raport.print-all', ['rombel_id' => $rombel->id, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" 
                       class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;" target="_blank">
                        <i class="fas fa-print"></i> Raport Semua
                    </a>
                    <a href="{{ route('admin.leger.print-nilai', ['rombel_id' => $rombel->id, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" 
                       class="btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;" target="_blank">
                        <i class="fas fa-table"></i> Leger Akademik
                    </a>
                    <a href="{{ route('admin.riwayat-akademik.print-all', ['rombel_id' => $rombel->id, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" 
                       class="btn" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;" target="_blank">
                        <i class="fas fa-history"></i> Riwayat Semua
                    </a>
                    <a href="{{ route('admin.rombel.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div style="overflow-x: auto;">
                @if($siswaList->count() > 0)
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th style="text-align: left;">Nama Siswa</th>
                                <th>Angkatan</th>
                                <th>L/P</th>
                                <th>Agama</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $index => $siswa)
                                <tr>
                                    <td class="text-center" style="font-weight: 600; color: var(--gray-500);">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $siswa->nis }}</td>
                                    <td class="text-center">{{ $siswa->nisn }}</td>
                                    <td>
                                        <div class="student-info">
                                            <span class="student-name">{{ $siswa->nama }}</span>
                                            <a href="javascript:void(0)" 
                                               onclick="confirmLoginAsSiswa('{{ $siswa->nisn }}', '{{ $siswa->nama }}')" 
                                               class="login-as-btn" title="Login sebagai Siswa">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $siswa->angkatan_masuk }}</td>
                                    <td class="text-center">
                                        <span class="gender-badge {{ $siswa->jk == 'Laki-laki' ? 'gender-l' : 'gender-p' }}">
                                            {{ $siswa->jk == 'Laki-laki' ? 'L' : 'P' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $siswa->agama ?? '-' }}</td>
                                    <td class="text-center">
                                        <div style="display: flex; justify-content: center; gap: 6px;">
                                            <a href="{{ route('admin.riwayat-akademik', ['nisn' => $siswa->nisn]) }}" 
                                               class="action-btn btn-info-action" title="Riwayat Akademik" target="_blank">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <a href="{{ route('admin.raport.print', ['nisn' => $siswa->nisn, 'rombel_id' => $rombel->id, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" 
                                               class="action-btn btn-success-action" title="Cetak Raport" target="_blank">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h3>Belum Ada Siswa</h3>
                        <p>Belum ada siswa dalam rombel ini untuk periode {{ $tahunPelajaran }} {{ $semester }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Rekapitulasi Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-chart-pie"></i> Rekapitulasi</h2>
                <span style="background: var(--gray-100); padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    {{ count($rekap) }} Agama
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table class="rekap-table">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding-left: 20px;">Agama</th>
                            <th>Laki-laki</th>
                            <th>Perempuan</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekap as $agama => $data)
                            <tr>
                                <td style="text-align: left; padding-left: 20px; font-weight: 600; color: var(--gray-600);">
                                    {{ $agama }}
                                </td>
                                <td>{{ $data['Laki-laki'] }}</td>
                                <td>{{ $data['Perempuan'] }}</td>
                                <td style="font-weight: 600; background: var(--gray-100);">{{ $data['total'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td style="text-align: left; padding-left: 20px;">Total Keseluruhan</td>
                            <td>{{ $totalLK }}</td>
                            <td>{{ $totalPR }}</td>
                            <td>{{ $totalSemua }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Login As Siswa -->
<div id="modalLoginAsSiswa" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 9999; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; border-radius: 16px; width: 420px; max-width: 90%; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: slideIn 0.3s ease;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 25px 20px; text-align: center; position: relative;">
            <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-graduate" style="font-size: 32px; color: white;"></i>
            </div>
            <h3 style="margin: 0; color: white; font-size: 18px; font-weight: 700;">Login sebagai Siswa</h3>
        </div>
        
        <!-- Body -->
        <div style="padding: 24px;">
            <p style="margin: 0 0 8px; color: #64748b; font-size: 14px; text-align: center;">Anda akan masuk ke akun:</p>
            
            <!-- User Card -->
            <div style="background: linear-gradient(135deg, #f0fdfa, #ccfbf1); border: 1px solid #99f6e4; border-radius: 12px; padding: 15px; margin: 15px 0; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div style="text-align: left; flex: 1;">
                    <div id="loginSiswaNama" style="font-weight: 600; color: #0f766e; font-size: 16px;">Nama Siswa</div>
                    <div id="loginSiswaNisn" style="font-size: 12px; color: #14b8a6; font-family: monospace;">NISN: -</div>
                </div>
            </div>
            
            <!-- Info Box -->
            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 12px 15px; display: flex; align-items: flex-start; gap: 10px; text-align: left;">
                <i class="fas fa-info-circle" style="color: #f59e0b; font-size: 16px; margin-top: 2px;"></i>
                <p style="margin: 0; color: #92400e; font-size: 12px; line-height: 1.5;">
                    Sesi admin Anda akan disimpan. Anda dapat kembali ke akun admin setelah selesai.
                </p>
            </div>
            
            <!-- Buttons -->
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
                <button onclick="closeLoginModal()" style="flex: 1; background: #f1f5f9; color: #64748b; padding: 12px 20px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button id="btnConfirmLogin" style="flex: 1; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; padding: 12px 20px; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(6,182,212,0.4);">
                    <i class="fas fa-sign-in-alt"></i> Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedNisn = '';

function confirmLoginAsSiswa(nisn, nama) {
    selectedNisn = nisn;
    document.getElementById('loginSiswaNama').textContent = nama;
    document.getElementById('loginSiswaNisn').textContent = 'NISN: ' + nisn;
    
    const modal = document.getElementById('modalLoginAsSiswa');
    modal.style.display = 'flex';
}

function closeLoginModal() {
    document.getElementById('modalLoginAsSiswa').style.display = 'none';
    selectedNisn = '';
}

document.getElementById('btnConfirmLogin')?.addEventListener('click', function() {
    if (selectedNisn) {
        window.location.href = '{{ url("/admin/impersonate/siswa") }}/' + selectedNisn;
    }
});

// Close modal on backdrop click
document.getElementById('modalLoginAsSiswa')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLoginModal();
    }
});
</script>
@endpush

<style>
@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
@endsection
