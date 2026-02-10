@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Dashboard Siswa | SISMIK')

@section('content')
@php
    $hasFoto = $siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto);
    $initials = collect(explode(' ', $siswa->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
@endphp
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        @if(session('impersonating'))
        <!-- Impersonation Banner -->
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-secret" style="font-size: 20px;"></i>
                <span style="font-weight: 600;">Anda sedang login sebagai siswa: {{ $siswa->nama }}</span>
            </div>
            <a href="{{ route('siswa.stop-impersonate') }}" style="background: white; color: #d97706; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Admin
            </a>
        </div>
        @endif

        <!-- Enhanced Header -->
        <div class="siswa-dashboard-header">
            <div class="header-content-wrapper">
                <div class="header-avatar" onclick="openPhotoModal()" style="cursor: pointer;">
                    @if($hasFoto)
                        <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="Foto {{ $siswa->nama }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">
                    @else
                        <div style="width: 100%; height: 100%; background: rgba(255,255,255,0.2); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; font-weight: bold;">
                            {{ $initials ?: 'S' }}
                        </div>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-greeting">
                        <span class="greeting-text">Selamat Datang,</span>
                        <h1>{{ $siswa->nama }} 👋</h1>
                    </div>
                    <div class="header-details">
                        @if($periodik)
                        <span class="detail-badge periode">
                            <i class="fas fa-calendar-alt"></i> {{ $periodik->tahun_pelajaran }} - {{ $periodik->semester }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="header-decoration">
                <div class="decoration-circle circle-1"></div>
                <div class="decoration-circle circle-2"></div>
                <div class="decoration-circle circle-3"></div>
            </div>
        </div>

        <!-- Photo Modal -->
        <div id="photoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
            <div style="position: relative; max-width: 90%; max-height: 80%;">
                <button onclick="closePhotoModal()" style="position: absolute; top: -40px; right: 0; background: transparent; border: none; color: white; font-size: 28px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
                @if($hasFoto)
                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="Foto {{ $siswa->nama }}" style="max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
                @else
                    <div style="width: 200px; height: 200px; background: linear-gradient(135deg, #059669, #047857); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 80px; color: white; font-weight: bold;">{{ $initials ?: 'S' }}</span>
                    </div>
                @endif
            </div>
            <a href="{{ route('siswa.profil') }}" style="margin-top: 20px; display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="card jadwal-section" style="margin-bottom: 24px;">
            <div class="card-header jadwal-header">
                <h3 style="margin: 0; color: white;">
                    <i class="fas fa-calendar-day"></i> Jadwal Hari Ini - {{ $hariIni }}, {{ $tanggalFormatted }}
                    @if($isTesting)
                    <span class="testing-badge">MODE TESTING</span>
                    @endif
                </h3>
                <span class="jadwal-count">{{ count($jadwalPerMapel) }} Mapel</span>
            </div>
            <div class="card-body" style="padding: 20px;">
                @if($hariIni == 'Minggu')
                <div class="empty-state">
                    <i class="fas fa-sun" style="font-size: 48px; color: #f59e0b;"></i>
                    <h4>Hari Libur</h4>
                    <p>Tidak ada jadwal pelajaran hari Minggu.</p>
                </div>
                @elseif(empty($jadwalPerMapel))
                <div class="empty-state">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #d1d5db;"></i>
                    <h4>Tidak Ada Jadwal</h4>
                    <p>Belum ada jadwal pelajaran untuk hari ini.</p>
                </div>
                @else
                <div class="jadwal-cards-container">
                    @php
                        $statusMap = [
                            'H' => ['text' => 'Hadir', 'class' => 'bg-success', 'icon' => 'fa-check'],
                            'S' => ['text' => 'Sakit', 'class' => 'bg-warning text-dark', 'icon' => 'fa-bed'],
                            'I' => ['text' => 'Izin', 'class' => 'bg-info', 'icon' => 'fa-envelope'],
                            'A' => ['text' => 'Alpha', 'class' => 'bg-danger', 'icon' => 'fa-times'],
                            'D' => ['text' => 'Dispensasi', 'class' => 'bg-primary', 'icon' => 'fa-certificate'],
                            'B' => ['text' => 'Bolos', 'class' => 'bg-danger', 'icon' => 'fa-exclamation-triangle']
                        ];
                    @endphp
                    @foreach($jadwalPerMapel as $jadwal)
                    @php
                        $jamList = $jadwal['jam_list'];
                        $jamText = count($jamList) === 1 ? $jamList[0] : min($jamList) . '-' . max($jamList);
                        $presensi = $jadwal['presensi_status'];
                        $nilai = $jadwal['nilai'];
                        $izin = $jadwal['izin_guru'];
                        $firstJam = min($jadwal['jam_list']);
                        $recordId = $jadwal['presensi_record_id'] ?? null;
                        $kehadiranGuru = $jadwal['kehadiran_guru'] ?? null;
                        
                        $cardClass = 'jadwal-card';
                        if ($izin) {
                            $cardClass .= ' card-izin';
                        } elseif ($presensi) {
                            $cardClass .= ' card-done';
                        }
                    @endphp
                    <div class="{{ $cardClass }}">
                        <div class="jadwal-card-header">
                            <div class="jam-badge">
                                <i class="fas fa-clock"></i>
                                <span>Jam {{ $jamText }}</span>
                            </div>
                            @if($izin)
                            <span class="status-badge warning"><i class="fas fa-user-clock"></i> Guru Izin</span>
                            @elseif($presensi)
                            <span class="status-badge success"><i class="fas fa-check-circle"></i> Tercatat</span>
                            @else
                            <span class="status-badge pending"><i class="fas fa-hourglass-half"></i> Menunggu</span>
                            @endif
                        </div>

                        <div class="jadwal-card-body">
                            <div class="mapel-info">
                                <i class="fas fa-book"></i>
                                <div>
                                    <h4>{{ $jadwal['nama_mapel'] }}</h4>
                                    <p><i class="fas fa-chalkboard-teacher"></i> {{ $jadwal['nama_guru'] }}</p>
                                </div>
                            </div>

                            <div class="jadwal-card-footer">
                                <!-- Presensi -->
                                <div class="footer-item">
                                    <span class="footer-label">Presensi</span>
                                    @if($presensi)
                                    @php
                                        $status = $statusMap[$presensi] ?? ['text' => $presensi, 'class' => 'bg-secondary', 'icon' => 'fa-question'];
                                    @endphp
                                    <span class="badge {{ $status['class'] }}">
                                        <i class="fas {{ $status['icon'] }}"></i> {{ $status['text'] }}
                                    </span>
                                    @else
                                    <span class="badge bg-light text-muted">-</span>
                                    @endif
                                </div>

                                <!-- Nilai -->
                                <div class="footer-item">
                                    <span class="footer-label">Nilai</span>
                                    @if($nilai)
                                    <span class="nilai-badge {{ $nilai->nilai >= 75 ? 'good' : 'poor' }}">
                                        {{ $nilai->nilai }}
                                    </span>
                                    @else
                                    <span class="badge bg-light text-muted">-</span>
                                    @endif
                                </div>
                            </div>

                            @if($izin)
                            <div class="izin-detail-box">
                                <div class="izin-header">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Guru Berhalangan Hadir</span>
                                </div>
                                @if(!empty($izin->alasan_izin))
                                <div class="izin-row">
                                    <span class="izin-label"><i class="fas fa-info-circle"></i> Alasan:</span>
                                    <span class="izin-value">{{ $izin->alasan_izin }}</span>
                                </div>
                                @endif
                                @if(!empty($izin->materi))
                                <div class="izin-row">
                                    <span class="izin-label"><i class="fas fa-book-open"></i> Materi:</span>
                                    <span class="izin-value">{{ $izin->materi }}</span>
                                </div>
                                @endif
                                @if(!empty($izin->uraian_tugas))
                                <div class="izin-row tugas-box">
                                    <span class="izin-label"><i class="fas fa-tasks"></i> Tugas yang Harus Dikerjakan:</span>
                                    <div class="tugas-content">{!! nl2br(e($izin->uraian_tugas)) !!}</div>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Kehadiran Guru Section: only show when Hadir or Belum Presensi --}}
                            @if(($presensi === 'H' || !$presensi) && !$izin)
                            <div class="kehadiran-guru-section" id="kehadiran-section-{{ $firstJam }}-{{ $loop->index }}">
                                @if($kehadiranGuru)
                                    @php
                                        $kgMap = [
                                            'Tepat Waktu' => ['class' => 'kg-tepat', 'icon' => 'fa-check-circle'],
                                            'Terlambat' => ['class' => 'kg-terlambat', 'icon' => 'fa-clock'],
                                            'Tidak Hadir' => ['class' => 'kg-tidak', 'icon' => 'fa-times-circle'],
                                        ];
                                        $kgInfo = $kgMap[$kehadiranGuru] ?? ['class' => 'kg-tepat', 'icon' => 'fa-question-circle'];
                                    @endphp
                                    <div class="kg-result {{ $kgInfo['class'] }}">
                                        <i class="fas {{ $kgInfo['icon'] }}"></i>
                                        <span>Guru: {{ $kehadiranGuru }}</span>
                                    </div>
                                @else
                                    <button class="btn-konfirmasi-guru" 
                                            onclick="openKehadiranModal({{ $recordId }}, {{ $firstJam }}, {{ $loop->index }}, '{{ addslashes($jadwal['nama_guru']) }}')">
                                        <i class="fas fa-user-check"></i>
                                        <span>Konfirmasi Kehadiran Guru</span>
                                    </button>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Attendance Summary - Today -->
        @if($presensiSummary)
        <div style="margin-bottom: 10px;">
            <p style="font-size: 13px; color: #6b7280; margin: 0;"><i class="fas fa-calendar-day" style="color: #10b981; margin-right: 5px;"></i>Rekap Presensi Hari Ini</p>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $presensiSummary->hadir ?? 0 }}</h3>
                    <p>Hadir</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $presensiSummary->izin ?? 0 }}</h3>
                    <p>Izin</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $presensiSummary->sakit ?? 0 }}</h3>
                    <p>Sakit</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $presensiSummary->alfa ?? 0 }}</h3>
                    <p>Alfa</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Grades -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line" style="margin-right: 10px; color: var(--primary);"></i>Nilai Terbaru</h3>
                <a href="#" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">Lihat Semua</a>
            </div>
            @if($nilaiTerbaru && $nilaiTerbaru->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--gray-100);">
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: var(--gray-600);">Mata Pelajaran</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: var(--gray-600);">Materi</th>
                            <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: var(--gray-600);">Nilai</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: var(--gray-600);">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nilaiTerbaru as $nilai)
                        <tr style="border-bottom: 1px solid var(--gray-200);">
                            <td style="padding: 12px 15px;">{{ $nilai->mapel }}</td>
                            <td style="padding: 12px 15px;">{{ $nilai->materi ?? '-' }}</td>
                            <td style="padding: 12px 15px; text-align: center;">
                                <span style="background: {{ $nilai->nilai >= 75 ? 'var(--primary)' : 'var(--danger)' }}; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 600;">
                                    {{ $nilai->nilai }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px;">{{ $nilai->tanggal_penilaian ? $nilai->tanggal_penilaian->format('d M Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p style="text-align: center; color: var(--gray-500); padding: 30px;">Belum ada data nilai.</p>
            @endif
        </div>
    </div>
</div>

<style>
/* Siswa Dashboard Header */
.siswa-dashboard-header {
    background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(5, 150, 105, 0.3);
}

.header-content-wrapper {
    display: flex;
    align-items: center;
    gap: 24px;
    position: relative;
    z-index: 2;
}

.header-avatar {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
    flex-shrink: 0;
}

.header-avatar i {
    font-size: 36px;
    color: white;
}

.header-info {
    flex: 1;
}

.header-greeting .greeting-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    display: block;
    margin-bottom: 4px;
}

.header-greeting h1 {
    color: white;
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-details {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
}

.detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.detail-badge i {
    font-size: 11px;
    opacity: 0.9;
}

.header-decoration {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 50%;
    pointer-events: none;
    z-index: 1;
}

.decoration-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
}

.circle-1 {
    width: 200px;
    height: 200px;
    top: -50px;
    right: -50px;
}

.circle-2 {
    width: 120px;
    height: 120px;
    bottom: -30px;
    right: 100px;
}

.circle-3 {
    width: 80px;
    height: 80px;
    top: 50%;
    right: 30%;
    transform: translateY(-50%);
}

/* Jadwal Section */
.jadwal-section .jadwal-header {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.jadwal-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
}

.testing-badge {
    background: #fbbf24;
    color: #78350f;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    margin-left: 10px;
}

.jadwal-count {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.empty-state h4 {
    margin: 15px 0 0 0;
}

.empty-state p {
    margin: 10px 0 0 0;
}

/* Jadwal Cards */
.jadwal-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
}

.jadwal-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.jadwal-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.jadwal-card.card-done {
    border-left: 4px solid #10b981;
}

.jadwal-card.card-izin {
    border-left: 4px solid #f59e0b;
}

.jadwal-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
}

.jam-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.jam-badge i {
    font-size: 12px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 16px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge.success {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.warning {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.pending {
    background: #f3f4f6;
    color: #6b7280;
}

.jadwal-card-body {
    padding: 16px;
}

.mapel-info {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
}

.mapel-info > i {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #059669;
    font-size: 18px;
    flex-shrink: 0;
}

.mapel-info h4 {
    margin: 0 0 4px 0;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
}

.mapel-info p {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
}

.mapel-info p i {
    margin-right: 6px;
    color: #9ca3af;
}

.jadwal-card-footer {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.footer-item {
    text-align: center;
}

.footer-label {
    display: block;
    font-size: 11px;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.footer-item .badge {
    font-size: 11px;
    padding: 4px 10px;
}

.nilai-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 14px;
}

.nilai-badge.good {
    background: #d1fae5;
    color: #065f46;
}

.nilai-badge.poor {
    background: #fee2e2;
    color: #991b1b;
}

/* Izin Detail Box */
.izin-detail-box {
    margin-top: 16px;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fcd34d;
    border-radius: 12px;
    overflow: hidden;
}

.izin-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
}

.izin-header i {
    font-size: 14px;
}

.izin-row {
    padding: 10px 14px;
    border-bottom: 1px solid rgba(252, 211, 77, 0.5);
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.izin-row:last-child {
    border-bottom: none;
}

.izin-label {
    font-size: 11px;
    color: #92400e;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.izin-label i {
    font-size: 10px;
    color: #b45309;
}

.izin-value {
    font-size: 13px;
    color: #78350f;
    line-height: 1.4;
}

.izin-row.tugas-box {
    background: rgba(245, 158, 11, 0.1);
}

.tugas-content {
    margin-top: 8px;
    padding: 12px;
    background: white;
    border-radius: 8px;
    font-size: 13px;
    color: #1f2937;
    line-height: 1.6;
    border: 1px dashed #fbbf24;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .siswa-dashboard-header {
        padding: 20px;
    }

    .header-content-wrapper {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }

    .header-avatar {
        width: 70px;
        height: 70px;
    }

    .header-avatar i {
        font-size: 30px;
    }

    .header-greeting h1 {
        font-size: 22px;
    }

    .header-details {
        justify-content: center;
    }

    .detail-badge {
        font-size: 11px;
        padding: 5px 10px;
    }

    .header-decoration {
        display: none;
    }

    .jadwal-cards-container {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .jadwal-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .jadwal-header h3 {
        font-size: 16px !important;
    }

    .jadwal-card-header {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
        text-align: center;
    }

    .jam-badge {
        justify-content: center;
    }

    .mapel-info {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }

    .jadwal-card-footer {
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .header-greeting h1 {
        font-size: 18px;
    }

    .header-details {
        flex-direction: column;
        align-items: center;
    }

    .jadwal-card-body {
        padding: 12px;
    }

    .izin-header {
        font-size: 12px;
        padding: 8px 12px;
    }

    .izin-row {
        padding: 8px 12px;
    }

    .tugas-content {
        padding: 10px;
        font-size: 12px;
    }
    
    /* Stats grid mobile - 4 in a row */
    .stats-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 6px !important;
    }
    
    .stats-grid .stat-card {
        flex-direction: column !important;
        padding: 8px 4px !important;
        gap: 4px !important;
        text-align: center !important;
    }
    
    .stats-grid .stat-icon {
        width: 32px !important;
        height: 32px !important;
        font-size: 12px !important;
        margin: 0 auto !important;
        border-radius: 8px !important;
    }
    
    .stats-grid .stat-content h3 {
        font-size: 14px !important;
        margin-bottom: 0 !important;
    }
    
    .stats-grid .stat-content p {
        font-size: 8px !important;
    }
}

/* Kehadiran Guru Section */
.kehadiran-guru-section {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed #e5e7eb;
}

.btn-konfirmasi-guru {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-konfirmasi-guru:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-konfirmasi-guru:active {
    transform: translateY(0);
}

.kg-result {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
}

.kg-result.kg-tepat {
    background: #d1fae5;
    color: #065f46;
}

.kg-result.kg-terlambat {
    background: #fef3c7;
    color: #92400e;
}

.kg-result.kg-tidak {
    background: #fee2e2;
    color: #991b1b;
}

/* Kehadiran Guru Modal */
.kg-modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.kg-modal-overlay.show {
    display: flex;
}

.kg-modal-box {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 380px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: kgSlideUp 0.3s ease;
}

@keyframes kgSlideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

.kg-modal-header {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kg-modal-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.kg-modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 30px; height: 30px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kg-modal-body {
    padding: 20px;
}

.kg-guru-info {
    text-align: center;
    margin-bottom: 16px;
    padding: 10px;
    background: #f8fafc;
    border-radius: 10px;
    font-size: 13px;
    color: #6b7280;
}

.kg-guru-info strong {
    color: #1f2937;
}

.kg-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.kg-option-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.kg-option-btn:hover {
    transform: translateX(4px);
}

.kg-option-btn.tepat:hover {
    border-color: #10b981;
    background: #ecfdf5;
    color: #065f46;
}

.kg-option-btn.terlambat:hover {
    border-color: #f59e0b;
    background: #fffbeb;
    color: #92400e;
}

.kg-option-btn.tidak:hover {
    border-color: #ef4444;
    background: #fef2f2;
    color: #991b1b;
}

.kg-option-btn .kg-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.kg-option-btn.tepat .kg-icon {
    background: #d1fae5;
    color: #059669;
}

.kg-option-btn.terlambat .kg-icon {
    background: #fef3c7;
    color: #d97706;
}

.kg-option-btn.tidak .kg-icon {
    background: #fee2e2;
    color: #dc2626;
}

/* Toast Notification */
.kg-toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(100px);
    padding: 12px 24px;
    border-radius: 12px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    z-index: 99999;
    transition: transform 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 8px;
}

.kg-toast.show {
    transform: translateX(-50%) translateY(0);
}

.kg-toast.success {
    background: linear-gradient(135deg, #10b981, #059669);
}

.kg-toast.error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}
</style>

<!-- Kehadiran Guru Modal -->
<div class="kg-modal-overlay" id="kgModal">
    <div class="kg-modal-box">
        <div class="kg-modal-header">
            <h4><i class="fas fa-user-check" style="margin-right: 8px;"></i>Kehadiran Guru</h4>
            <button class="kg-modal-close" onclick="closeKehadiranModal()">&times;</button>
        </div>
        <div class="kg-modal-body">
            <div class="kg-guru-info">
                <i class="fas fa-chalkboard-teacher"></i>
                <strong id="kgGuruNama">-</strong>
            </div>
            <div class="kg-options">
                <button class="kg-option-btn tepat" onclick="selectKehadiran('Tepat Waktu')">
                    <div class="kg-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Guru Hadir Tepat Waktu</span>
                </button>
                <button class="kg-option-btn terlambat" onclick="selectKehadiran('Terlambat')">
                    <div class="kg-icon"><i class="fas fa-clock"></i></div>
                    <span>Guru Hadir Terlambat</span>
                </button>
                <button class="kg-option-btn tidak" onclick="selectKehadiran('Tidak Hadir')">
                    <div class="kg-icon"><i class="fas fa-times-circle"></i></div>
                    <span>Guru Tidak Hadir</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="kg-toast" id="kgToast"></div>

<script>
let _kgPresensiId = null;
let _kgJamKe = null;
let _kgLoopIndex = null;

function openPhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openKehadiranModal(presensiId, jamKe, loopIndex, namaGuru) {
    _kgPresensiId = presensiId;
    _kgJamKe = jamKe;
    _kgLoopIndex = loopIndex;
    document.getElementById('kgGuruNama').textContent = namaGuru;
    document.getElementById('kgModal').classList.add('show');
    document.getElementById('kgModal').style.display = 'flex';
}

function closeKehadiranModal() {
    document.getElementById('kgModal').classList.remove('show');
    document.getElementById('kgModal').style.display = 'none';
}

function selectKehadiran(status) {
    closeKehadiranModal();

    // Disable the button immediately
    const section = document.getElementById('kehadiran-section-' + _kgJamKe + '-' + _kgLoopIndex);
    if (section) {
        section.innerHTML = '<div style="text-align:center;padding:8px;"><i class="fas fa-spinner fa-spin" style="color:#3b82f6;"></i> Menyimpan...</div>';
    }

    fetch('{{ route("siswa.save-kehadiran-guru") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            presensi_id: _kgPresensiId,
            jam_ke: _kgJamKe,
            status: status
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showKgToast(data.message, 'success');
            // Update card inline
            if (section) {
                const map = {
                    'Tepat Waktu': { cls: 'kg-tepat', icon: 'fa-check-circle' },
                    'Terlambat': { cls: 'kg-terlambat', icon: 'fa-clock' },
                    'Tidak Hadir': { cls: 'kg-tidak', icon: 'fa-times-circle' }
                };
                const info = map[status] || map['Tepat Waktu'];
                section.innerHTML = `<div class="kg-result ${info.cls}"><i class="fas ${info.icon}"></i><span>Guru: ${status}</span></div>`;
            }
        } else {
            showKgToast(data.message || 'Gagal menyimpan', 'error');
            if (section) {
                section.innerHTML = '<button class="btn-konfirmasi-guru" onclick="openKehadiranModal(' + _kgPresensiId + ',' + _kgJamKe + ',' + _kgLoopIndex + ',\'' + document.getElementById('kgGuruNama').textContent + '\')"><i class="fas fa-user-check"></i><span>Konfirmasi Kehadiran Guru</span></button>';
            }
        }
    })
    .catch(err => {
        showKgToast('Terjadi kesalahan jaringan', 'error');
        console.error(err);
    });
}

function showKgToast(message, type) {
    const toast = document.getElementById('kgToast');
    toast.className = 'kg-toast ' + type;
    toast.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> ' + message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// Close modals on backdrop click
document.addEventListener('DOMContentLoaded', function() {
    const photoModal = document.getElementById('photoModal');
    if (photoModal) {
        photoModal.addEventListener('click', function(e) {
            if (e.target === photoModal) closePhotoModal();
        });
    }
    const kgModal = document.getElementById('kgModal');
    if (kgModal) {
        kgModal.addEventListener('click', function(e) {
            if (e.target === kgModal) closeKehadiranModal();
        });
    }
});
</script>
@endsection
