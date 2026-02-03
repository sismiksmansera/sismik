@extends('layouts.app')

@section('title', 'Detail Jadwal | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div style="margin-bottom: 25px;">
            <a href="{{ route('admin.dashboard') }}"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #6b7280, #4b5563); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>

            <div style="display: flex; flex-wrap: wrap; gap: 20px; color: #6b7280; font-size: 14px; margin-top: 15px;">
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-school" style="color: var(--primary);"></i> 
                    <strong style="color: #1f2937;">{{ $namaRombel }}</strong>
                </span>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-book" style="color: var(--primary);"></i> 
                    {{ $mapel }}
                </span>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-clock" style="color: var(--primary);"></i> 
                    Jam ke-{{ $jamText }}
                </span>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-calendar" style="color: var(--primary);"></i> 
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                </span>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-user-tie" style="color: var(--primary);"></i> 
                    {{ $guru }}
                </span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #059669, #047857); border-radius: 12px 12px 0 0;">
                <h3 class="card-title" style="color: white; margin: 0;">
                    <i class="fas fa-users"></i> Daftar Siswa
                </h3>
                <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 12px; font-size: 13px;">
                    {{ count($siswaList) }} Siswa
                </span>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151; width: 60px;">No</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #374151;">Nama Siswa</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #374151;">NISN</th>
                            <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151;">Presensi</th>
                            <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #374151;">Nilai</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #374151;">Keterangan Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($siswaList) > 0)
                            @foreach($siswaList as $index => $siswa)
                            @php
                                $presensi = $presensiData[$siswa->nisn] ?? null;
                                $nilai = $penilaianData[$siswa->nisn] ?? null;
                                $status = $presensi ? ($presensi['presensi'] ?? null) : null;
                                $statusInfo = $status ? ($statusMap[$status] ?? ['text' => $status, 'class' => 'secondary', 'icon' => 'fa-minus']) : null;
                            @endphp
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 12px 15px; text-align: center;">{{ $index + 1 }}</td>
                                <td style="padding: 12px 15px; font-weight: 600; color: #1f2937;">{{ $siswa->nama }}</td>
                                <td style="padding: 12px 15px; color: #6b7280;">{{ $siswa->nisn }}</td>
                                <td style="padding: 12px 15px; text-align: center;">
                                    @if($statusInfo)
                                    <span class="badge bg-{{ $statusInfo['class'] }}" style="padding: 5px 10px; border-radius: 6px; font-size: 12px;">
                                        <i class="fas {{ $statusInfo['icon'] }}"></i>
                                        {{ $statusInfo['text'] }}
                                    </span>
                                    @else
                                    <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 15px; text-align: center; font-weight: 700;">
                                    @if($nilai)
                                    <span style="background: {{ $nilai->nilai >= 75 ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }}; color: {{ $nilai->nilai >= 75 ? '#10b981' : '#ef4444' }}; padding: 4px 12px; border-radius: 6px;">
                                        {{ $nilai->nilai }}
                                    </span>
                                    @else
                                    <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 15px; color: #6b7280; font-size: 13px;">
                                    {{ $nilai->keterangan ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center;">
                                <i class="fas fa-users-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px; display: block;"></i>
                                <span style="color: #6b7280;">Tidak ada data siswa ditemukan</span>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
