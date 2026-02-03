@extends('layouts.app')

@section('title', 'Siswa Bimbingan - ' . $guruBK->nama . ' | SISMIK')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .header-text h1 { margin: 0; color: #065f46; font-size: 24px; font-weight: 700; }
    .header-text p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .stat-info h3 { margin: 0; font-size: 24px; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 2px 0 0 0; color: #6b7280; font-size: 12px; }
    
    /* Kelas Badges */
    .kelas-badges {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
        align-items: center;
    }
    .kelas-badge {
        padding: 15px 25px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }
    .kelas-badge span.count {
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        color: white;
    }
    
    /* Rombel Collapse Card */
    .rombel-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 15px;
    }
    .rombel-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .rombel-header:hover { filter: brightness(1.05); }
    .rombel-header .left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .rombel-header .icon {
        width: 45px;
        height: 45px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rombel-header .icon i { color: white; font-size: 18px; }
    .rombel-header h3 { margin: 0; color: white; font-size: 18px; font-weight: 600; }
    .rombel-header .stats { display: flex; gap: 15px; margin-top: 4px; }
    .rombel-header .stats span { color: rgba(255,255,255,0.8); font-size: 12px; }
    .rombel-header .right { display: flex; align-items: center; gap: 10px; }
    .rombel-header .badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .rombel-header .toggle-icon {
        width: 30px;
        height: 30px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }
    .rombel-header .toggle-icon i { color: white; font-size: 12px; }
    
    .rombel-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
    }
    .rombel-content.expanded { max-height: 2000px; }
    
    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .data-table th {
        padding: 12px 15px;
        text-align: left;
        color: #64748b;
        font-weight: 600;
        background: #f8fafc;
    }
    .data-table th.text-center { text-align: center; }
    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
    }
    .data-table td.text-center { text-align: center; }
    .data-table tbody tr:hover { background: #f8fafc; }
    
    /* Siswa Info */
    .siswa-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .siswa-foto {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        overflow: hidden;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .siswa-foto:hover {
        transform: scale(1.1);
        border-color: #10b981;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    .siswa-foto img { width: 100%; height: 100%; object-fit: cover; }
    .siswa-foto-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        flex-shrink: 0;
        border: 2px solid #e5e7eb;
    }
    
    /* Badges */
    .badge-jk {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-jk.l { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .badge-jk.p { background: rgba(236, 72, 153, 0.1); color: #ec4899; }
    .badge-kelas {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-kelas.k10 { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .badge-kelas.k11 { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .badge-kelas.k12 { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .badge-nisn {
        background: #f3f4f6;
        color: #4b5563;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-family: monospace;
    }
    
    /* Action Button */
    .btn-panggilan {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-panggilan:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.4);
    }
    
    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .empty-state i { font-size: 64px; color: #d1d5db; margin-bottom: 20px; }
    .empty-state h3 { margin: 0 0 10px 0; color: #374151; font-size: 20px; }
    .empty-state p { margin: 0; color: #6b7280; font-size: 14px; }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.85);
        backdrop-filter: blur(10px);
        justify-content: center;
        align-items: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: zoom-out;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    
    @media (max-width: 768px) {
        .content-header { flex-direction: column; }
        .kelas-badges { flex-direction: column; align-items: stretch; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="header-text">
                    <h1>Siswa Bimbingan</h1>
                    <p>Guru BK: <strong>{{ $guruBK->nama }}</strong> | {{ $selectedTahun }} - {{ ucfirst($selectedSemester) }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalSiswa }}</h3>
                    <p>Total Siswa</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalRombel }}</h3>
                    <p>Total Rombel</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                    <i class="fas fa-male"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $jkCounts['L'] }}</h3>
                    <p>Laki-laki</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                    <i class="fas fa-female"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $jkCounts['P'] }}</h3>
                    <p>Perempuan</p>
                </div>
            </div>
        </div>

        <!-- Kelas Badges -->
        <div class="kelas-badges">
            <div class="kelas-badge" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0;">
                <span style="color: #065f46;">Kelas 10:</span>
                <span class="count" style="background: #10b981;">{{ $kelasCounts['10'] }}</span>
            </div>
            <div class="kelas-badge" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #fcd34d;">
                <span style="color: #92400e;">Kelas 11:</span>
                <span class="count" style="background: #f59e0b;">{{ $kelasCounts['11'] }}</span>
            </div>
            <div class="kelas-badge" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); border: 1px solid #7dd3fc;">
                <span style="color: #075985;">Kelas 12:</span>
                <span class="count" style="background: #0284c7;">{{ $kelasCounts['12'] }}</span>
            </div>
            <a href="{{ route('admin.guru-bk.index') }}" class="btn btn-secondary" style="margin-left: auto;">
                <i class="fas fa-arrow-left"></i> KEMBALI
            </a>
        </div>

        <!-- Siswa Per Rombel -->
        @if(empty($siswaPerRombel))
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h3>Belum Ada Siswa Bimbingan</h3>
                <p>Guru BK ini belum memiliki siswa bimbingan untuk periode ini.</p>
            </div>
        @else
            @php $rombelIndex = 0; @endphp
            @foreach($siswaPerRombel as $rombelName => $siswaList)
                @php
                    $rombelId = 'rombel_' . $rombelIndex++;
                    $siswaCount = count($siswaList);
                    $jkL = 0;
                    $jkP = 0;
                    foreach ($siswaList as $s) {
                        if ($s['jk'] == 'Laki-laki') $jkL++;
                        else $jkP++;
                    }
                @endphp
                <div class="rombel-card">
                    <div class="rombel-header" onclick="toggleRombel('{{ $rombelId }}')">
                        <div class="left">
                            <div class="icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <h3>{{ $rombelName }}</h3>
                                <div class="stats">
                                    <span><i class="fas fa-users"></i> {{ $siswaCount }} siswa</span>
                                    <span><i class="fas fa-male"></i> {{ $jkL }} L</span>
                                    <span><i class="fas fa-female"></i> {{ $jkP }} P</span>
                                </div>
                            </div>
                        </div>
                        <div class="right">
                            <span class="badge">{{ $siswaCount }} Siswa</span>
                            <div class="toggle-icon" id="{{ $rombelId }}_icon">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="rombel-content" id="{{ $rombelId }}_content">
                        <div style="overflow-x: auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Siswa</th>
                                        <th>NISN</th>
                                        <th class="text-center">JK</th>
                                        <th class="text-center">Kelas</th>
                                        <th>Agama</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaList as $index => $siswa)
                                        @php
                                            $hasFoto = !empty($siswa['foto']) && file_exists(public_path('storage/siswa/' . $siswa['foto']));
                                            $fotoUrl = $hasFoto ? asset('storage/siswa/' . $siswa['foto']) : null;
                                        @endphp
                                        <tr>
                                            <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="siswa-info">
                                                    @if($hasFoto)
                                                        <div class="siswa-foto" onclick="showFotoModal('{{ $fotoUrl }}', '{{ addslashes($siswa['nama']) }}', '{{ $siswa['nisn'] ?? '-' }}')">
                                                            <img src="{{ $fotoUrl }}" alt="">
                                                        </div>
                                                    @else
                                                        <div class="siswa-foto-placeholder" style="background: {{ $siswa['jk'] == 'Laki-laki' ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)' : 'linear-gradient(135deg, #ec4899, #db2777)' }};">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div style="font-weight: 600; color: #1f2937;">{{ $siswa['nama'] }}</div>
                                                        <div style="font-size: 11px; color: #9ca3af;">NIS: {{ $siswa['nis'] ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-nisn">{{ $siswa['nisn'] ?? '-' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-jk {{ $siswa['jk'] == 'Laki-laki' ? 'l' : 'p' }}">
                                                    {{ $siswa['jk'] == 'Laki-laki' ? 'L' : 'P' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-kelas k{{ $siswa['kelas'] }}">{{ $siswa['kelas'] }}</span>
                                            </td>
                                            <td style="color: #4b5563;">{{ $siswa['agama'] ?? '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.panggilan-ortu.index', ['nisn' => $siswa['nisn'], 'guru_bk_id' => $guruBK->id]) }}" class="btn-panggilan" title="Panggilan Orang Tua">
                                                    <i class="fas fa-envelope"></i> Panggilan
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Foto Modal -->
<div class="modal-overlay" id="fotoModal" onclick="closeFotoModal()">
    <div style="position: relative; max-width: 90%; max-height: 90%; display: flex; flex-direction: column; align-items: center;">
        <div style="background: white; padding: 8px; border-radius: 16px; box-shadow: 0 25px 80px rgba(0,0,0,0.5);">
            <img id="fotoModalImg" src="" alt="Foto Siswa" style="max-width: 500px; max-height: 70vh; border-radius: 12px; display: block; object-fit: contain;">
        </div>
        <div style="margin-top: 20px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 15px 25px; border-radius: 12px; display: flex; align-items: center; gap: 15px; border: 1px solid rgba(255,255,255,0.2);">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-graduate" style="color: white; font-size: 16px;"></i>
            </div>
            <div>
                <div id="fotoModalNama" style="color: white; font-weight: 600; font-size: 16px;">Nama Siswa</div>
                <div id="fotoModalNisn" style="color: rgba(255,255,255,0.7); font-size: 12px;">NISN: -</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleRombel(rombelId) {
        const content = document.getElementById(rombelId + '_content');
        const icon = document.getElementById(rombelId + '_icon');
        
        content.classList.toggle('expanded');
        icon.style.transform = content.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0deg)';
    }
    
    function showFotoModal(fotoUrl, nama, nisn) {
        const modal = document.getElementById('fotoModal');
        document.getElementById('fotoModalImg').src = fotoUrl;
        document.getElementById('fotoModalNama').textContent = nama;
        document.getElementById('fotoModalNisn').textContent = 'NISN: ' + nisn;
        modal.classList.add('show');
    }
    
    function closeFotoModal() {
        document.getElementById('fotoModal').classList.remove('show');
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeFotoModal();
    });
    
    // Expand first rombel by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstContent = document.querySelector('.rombel-content');
        const firstIcon = document.querySelector('[id$="_icon"]');
        if (firstContent && firstIcon) {
            firstContent.classList.add('expanded');
            firstIcon.style.transform = 'rotate(180deg)';
        }
    });
</script>
@endpush
