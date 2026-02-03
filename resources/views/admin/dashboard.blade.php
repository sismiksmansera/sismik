@extends('layouts.app')

@section('title', 'Dashboard Admin | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header" style="margin-bottom: 30px;">
            <div class="welcome-section">
                <h1 style="font-size: 28px; font-weight: 700; color: var(--dark);">
                    Selamat Datang, <span style="color: var(--primary);">{{ $admin->nama }}</span>! 👋
                </h1>
                <p style="color: var(--gray-500); margin-top: 5px;">
                    @if($periodik)
                        Periode Aktif: {{ $periodik->tahun_pelajaran }} - Semester {{ $periodik->semester }}
                    @else
                        Belum ada periode aktif
                    @endif
                </p>
            </div>
            <div class="datetime-section" style="text-align: right;">
                <div id="currentDate" style="font-weight: 600; color: var(--dark);"></div>
                <div id="currentTime" style="font-size: 24px; font-weight: 700; color: var(--primary);"></div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid-mobile-scroll">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalSiswa) }}</h3>
                    <p>Total Siswa</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalGuru) }}</h3>
                    <p>Guru Mapel Aktif</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalRombel) }}</h3>
                    <p>Rombel Aktif</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($kelasKosong) }}</h3>
                    <p>Kelas Kosong</p>
                </div>
            </div>
        </div>

        <style>
            .stats-grid-mobile-scroll {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 0;
            }
            
            @media (max-width: 992px) {
                .stats-grid-mobile-scroll {
                    display: flex;
                    overflow-x: auto;
                    gap: 10px;
                    padding-bottom: 10px;
                    scroll-snap-type: x mandatory;
                    -webkit-overflow-scrolling: touch;
                    width: 100%;
                    max-width: 100%;
                    margin: 0;
                }
                
                .stats-grid-mobile-scroll::-webkit-scrollbar {
                    height: 4px;
                }
                
                .stats-grid-mobile-scroll::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }
                
                .stats-grid-mobile-scroll::-webkit-scrollbar-thumb {
                    background: #c1c1c1;
                    border-radius: 10px;
                }
                
                .stats-grid-mobile-scroll .stat-card {
                    min-width: 140px;
                    flex-shrink: 0;
                    scroll-snap-align: start;
                    padding: 15px;
                }
                
                .stats-grid-mobile-scroll .stat-card .stat-icon {
                    width: 40px;
                    height: 40px;
                }
                
                .stats-grid-mobile-scroll .stat-card .stat-icon i {
                    font-size: 16px;
                }
                
                .stats-grid-mobile-scroll .stat-card .stat-content h3 {
                    font-size: 20px;
                }
                
                .stats-grid-mobile-scroll .stat-card .stat-content p {
                    font-size: 11px;
                }
                
                /* Widen jam-ke column on mobile */
                .modern-table th:first-child,
                .modern-table td:first-child {
                    min-width: 70px !important;
                    width: auto !important;
                }
            }
        </style>

        <!-- Jadwal Pelajaran Hari Ini -->
        <div class="schedule-section" style="margin-top: 30px; margin-bottom: 30px;">
            <div class="schedule-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-day" style="color: white; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h2 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 700;">
                            Jadwal Pelajaran Hari Ini
                            @if($isTesting)
                            <span style="background: #f59e0b; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; margin-left: 8px;">TESTING</span>
                            @endif
                        </h2>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">{{ $hariIni }}, {{ $tanggalFormatted }}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> Sudah Presensi
                    </span>
                    <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <i class="fas fa-times-circle"></i> Belum Presensi
                    </span>
                </div>
            </div>

            @if(empty($jadwalPerRombel))
            <div style="background: white; border-radius: 12px; padding: 40px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <i class="fas fa-calendar-times" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
                <h3 style="color: #4b5563; margin: 0 0 10px 0;">Tidak Ada Jadwal</h3>
                <p style="color: #9ca3af; margin: 0;">Tidak ada jadwal pelajaran untuk hari {{ $hariIni }}</p>
            </div>
            @else
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($jadwalPerRombel as $rombelName => $rombelData)
                <div style="background: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); overflow: hidden;">
                    <!-- Rombel Header -->
                    <div style="background: linear-gradient(135deg, #059669, #047857); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-chalkboard"></i>
                            <strong style="font-size: 16px;">{{ $rombelName }}</strong>
                        </div>
                        <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                            {{ count($rombelData['jadwal_items']) }} Mapel
                        </span>
                    </div>
                    <!-- Table for this Rombel -->
                    <div style="overflow-x: auto;">
                        <table class="modern-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb;">
                                    <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151; width: 160px;">Jam ke -</th>
                                    <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #374151;">Mata Pelajaran</th>
                                    <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151;">Kehadiran Guru</th>
                                    <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151;">Presensi Siswa</th>
                                    <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151;">Penilaian</th>
                                    <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151; width: 100px;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rombelData['jadwal_items'] as $jadwal)
                                @php
                                    $jamList = $jadwal['jam_list'];
                                    $jamText = count($jamList) === 1 ? $jamList[0] : min($jamList) . '-' . max($jamList);
                                    
                                    // Kehadiran guru styling
                                    $kehadiranStatus = $jadwal['kehadiran_status'];
                                    if ($kehadiranStatus === 'hadir') {
                                        $kehadiranStyle = 'background: rgba(16,185,129,0.1); color: #10b981;';
                                        $kehadiranIcon = 'fa-check-circle';
                                        $kehadiranText = 'Hadir';
                                    } elseif ($kehadiranStatus === 'izin') {
                                        $kehadiranStyle = 'background: rgba(245,158,11,0.1); color: #f59e0b;';
                                        $kehadiranIcon = 'fa-clock';
                                        $kehadiranText = 'Izin';
                                    } else {
                                        $kehadiranStyle = 'background: rgba(156,163,175,0.1); color: #6b7280;';
                                        $kehadiranIcon = 'fa-minus-circle';
                                        $kehadiranText = 'Belum Hadir';
                                    }
                                    
                                    // Presensi styling
                                    $presensiPersen = $jadwal['presensi_persen'];
                                    if ($presensiPersen !== null) {
                                        if ($presensiPersen >= 80) {
                                            $presensiStyle = 'background: rgba(16,185,129,0.1); color: #10b981;';
                                        } elseif ($presensiPersen >= 50) {
                                            $presensiStyle = 'background: rgba(245,158,11,0.1); color: #f59e0b;';
                                        } else {
                                            $presensiStyle = 'background: rgba(239,68,68,0.1); color: #ef4444;';
                                        }
                                    }
                                @endphp
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 12px 15px; text-align: center;">
                                        <span style="background: #f3f4f6; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 13px;">{{ $jamText }}</span>
                                    </td>
                                    <td style="padding: 12px 15px;">
                                        <div style="font-weight: 600; color: var(--primary);">{{ $jadwal['nama_mapel'] }}</div>
                                        <small style="color: #6b7280;">Guru: {{ $jadwal['nama_guru'] ?? '-' }}</small>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; {{ $kehadiranStyle }}">
                                            <i class="fas {{ $kehadiranIcon }}"></i>
                                            {{ $kehadiranText }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        @if($presensiPersen !== null)
                                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; {{ $presensiStyle }}">
                                            {{ $presensiPersen }}%
                                        </span>
                                        @else
                                        <span style="color: #9ca3af;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        @if($jadwal['has_penilaian'])
                                        <span style="color: #10b981; font-size: 18px;" title="Sudah melakukan penilaian"><i class="fas fa-check-circle"></i></span>
                                        @else
                                        <span style="color: #ef4444; font-size: 18px;" title="Belum melakukan penilaian"><i class="fas fa-times-circle"></i></span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        <a href="{{ route('admin.jadwal.detail', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'tanggal' => $tanggalHariIni, 'guru' => $jadwal['nama_guru'], 'jam_ke' => implode(',', $jadwal['jam_list'])]) }}" 
                                           class="btn btn-sm" 
                                           style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 6px 12px; border-radius: 8px; font-size: 12px; text-decoration: none;"
                                           title="Lihat Detail">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Charts Row -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
            <!-- Gender Distribution -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-venus-mars" style="margin-right: 10px; color: var(--primary);"></i>Distribusi Jenis Kelamin</h3>
                </div>
                <div style="display: flex; justify-content: center; gap: 40px; padding: 20px;">
                    <div style="text-align: center;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #3B82F6, #60A5FA); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-male" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h3 style="font-size: 28px; font-weight: 700; color: var(--dark);">{{ number_format($siswaLaki) }}</h3>
                        <p style="color: var(--gray-500);">Laki-laki</p>
                    </div>
                    <div style="text-align: center;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #EC4899, #F472B6); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-female" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h3 style="font-size: 28px; font-weight: 700; color: var(--dark);">{{ number_format($siswaPerempuan) }}</h3>
                        <p style="color: var(--gray-500);">Perempuan</p>
                    </div>
                </div>
            </div>

            <!-- Siswa per Tingkat -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layer-group" style="margin-right: 10px; color: var(--primary);"></i>Siswa per Tingkat</h3>
                </div>
                <div style="padding: 20px;">
                    @foreach($siswaTingkat as $tingkat)
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-weight: 600; color: var(--dark);">Kelas {{ $tingkat->tingkat }}</span>
                            <span style="color: var(--gray-500);">{{ number_format($tingkat->jumlah) }} siswa</span>
                        </div>
                        <div style="height: 10px; background: var(--gray-200); border-radius: 5px; overflow: hidden;">
                            <div style="height: 100%; width: {{ min(100, ($tingkat->jumlah / max($totalSiswa, 1)) * 300) }}%; background: linear-gradient(90deg, var(--primary), var(--primary-light)); border-radius: 5px;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt" style="margin-right: 10px; color: var(--warning);"></i>Aksi Cepat</h3>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px 0;">
                <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah Siswa</a>
                <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary"><i class="fas fa-chalkboard-teacher"></i> Kelola Guru</a>
                <a href="{{ route('admin.rombel.index') }}" class="btn btn-secondary"><i class="fas fa-door-open"></i> Kelola Rombel</a>
                <a href="{{ route('admin.manajemen-sekolah') }}" class="btn btn-secondary"><i class="fas fa-cog"></i> Pengaturan</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', options);
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>
@endpush
