@extends('layouts.app')

@section('title', 'Catatan Piket KBM | SISMIK')

@push('styles')
<style>
    /* Header */
    .catatan-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .catatan-header .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .catatan-header .header-icon {
        width: 56px; height: 56px;
        background: rgba(255,255,255,0.2);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
    }
    .catatan-header h2 { margin: 0; font-size: 20px; font-weight: 700; }
    .catatan-header .header-sub { margin: 4px 0 0; font-size: 13px; opacity: 0.85; }
    .catatan-header .header-badges { display: flex; gap: 8px; flex-wrap: wrap; }
    .header-badge {
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .header-badge.testing { background: rgba(245,158,11,0.3); }
    .btn-back {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }

    /* Piket Team */
    .piket-team-card {
        background: white;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
    }
    .piket-team-label {
        font-size: 12px; font-weight: 600; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;
    }
    .piket-team-list { display: flex; gap: 10px; flex-wrap: wrap; }
    .piket-team-item {
        display: flex; align-items: center; gap: 8px;
        background: #f8fafc; padding: 8px 14px; border-radius: 10px;
        border: 1px solid #e2e8f0; font-size: 13px; color: #1f2937;
    }
    .piket-team-item.is-me { background: #eff6ff; border-color: #93c5fd; font-weight: 600; }
    .team-dot { width: 8px; height: 8px; border-radius: 50%; }
    .team-dot.guru { background: #10b981; }
    .team-dot.guru_bk { background: #8b5cf6; }

    /* Jam Blocks */
    .jam-container { display: flex; flex-direction: column; gap: 16px; }

    .jam-block {
        background: white;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    .jam-block-header {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .jam-block-header h4 {
        margin: 0; font-size: 15px; font-weight: 700; color: #1f2937;
        display: flex; align-items: center; gap: 8px;
    }
    .jam-badge {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
    }
    .jam-time { font-size: 12px; color: #9ca3af; }

    .jam-block-body { padding: 0; }

    /* Guru Row */
    .guru-row {
        display: grid;
        grid-template-columns: 200px 160px 120px 1fr 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .guru-row:last-child { border-bottom: none; }
    .guru-row:hover { background: #f9fafb; }

    .guru-row .guru-name {
        font-size: 13.5px; font-weight: 600; color: #1f2937;
        display: flex; align-items: center; gap: 6px;
    }
    .guru-row .guru-mapel { font-size: 12px; color: #6b7280; }
    .guru-row .guru-rombel {
        font-size: 12px; color: #3b82f6; font-weight: 500;
    }

    /* Status Select */
    .status-select {
        padding: 6px 10px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 12px;
        font-family: inherit;
        font-weight: 600;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
    }
    .status-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }
    .status-select.hadir { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }
    .status-select.tidak-hadir { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .status-select.izin { color: #d97706; border-color: #fde68a; background: #fffbeb; }
    .status-select.terlambat { color: #7c3aed; border-color: #ddd6fe; background: #f5f3ff; }

    /* Input fields */
    .catatan-input {
        padding: 6px 10px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 12px;
        font-family: inherit;
        width: 100%;
        transition: all 0.2s;
    }
    .catatan-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    /* Save button */
    .btn-save-row {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-save-row:hover { opacity: 0.9; }
    .btn-save-row:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-save-row.saved {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    /* Column Headers */
    .col-headers {
        display: grid;
        grid-template-columns: 200px 160px 120px 1fr 1fr auto;
        gap: 12px;
        padding: 10px 20px;
        background: #f1f5f9;
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Empty jam */
    .empty-jam {
        padding: 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }

    /* Saved indicator */
    .saved-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        color: #10b981;
        font-weight: 600;
    }

    @media (max-width: 1024px) {
        .guru-row, .col-headers {
            grid-template-columns: 1fr;
            gap: 6px;
        }
        .col-headers { display: none; }
        .guru-row {
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin: 8px 12px;
            background: #f9fafb;
        }
        .guru-row .guru-name::before { content: 'Guru: '; font-weight: 400; color: #9ca3af; }
        .catatan-input, .status-select { max-width: 100%; }
    }

    @media (max-width: 768px) {
        .catatan-header { flex-direction: column; text-align: center; }
        .catatan-header .header-left { flex-direction: column; }
        .catatan-header .header-badges { justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        <!-- Header -->
        <div class="catatan-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h2>Catatan Piket KBM</h2>
                    <p class="header-sub">Verifikasi kehadiran guru di kelas per jam pelajaran</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <div class="header-badges">
                    <span class="header-badge"><i class="fas fa-calendar-day"></i> {{ $hariIni }}</span>
                    <span class="header-badge"><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</span>
                    @if($isTesting)
                        <span class="header-badge testing"><i class="fas fa-flask"></i> Mode Testing</span>
                    @endif
                </div>
                <a href="{{ route('guru.tugas-tambahan') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Piket Team -->
        <div class="piket-team-card">
            <div class="piket-team-label">Tim Piket Hari Ini</div>
            <div class="piket-team-list">
                @foreach($semuaPiketHariIni as $piket)
                <div class="piket-team-item {{ $piket->guru_id == $guru->id && $piket->tipe_guru == 'guru' ? 'is-me' : '' }}">
                    <span class="team-dot {{ $piket->tipe_guru }}"></span>
                    {{ $piket->nama_guru }}
                    @if($piket->guru_id == $guru->id && $piket->tipe_guru == 'guru')
                        <span style="background:#3b82f6; color:white; font-size:9px; padding:1px 6px; border-radius:6px;">ANDA</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Jam Blocks -->
        <div class="jam-container">
            @for($jam = 1; $jam <= $maxJam; $jam++)
                @php
                    $guruList = $jadwalPerJam[$jam] ?? [];
                    $waktu = isset($jamSetting[$jam]) ? $jamSetting[$jam]['mulai'] . ' - ' . $jamSetting[$jam]['selesai'] : '';
                @endphp
                <div class="jam-block">
                    <div class="jam-block-header">
                        <h4>
                            <span class="jam-badge">{{ $jam }}</span>
                            Jam ke-{{ $jam }}
                        </h4>
                        @if($waktu)
                            <span class="jam-time"><i class="fas fa-clock"></i> {{ $waktu }}</span>
                        @endif
                    </div>

                    @if(count($guruList) > 0)
                        <div class="col-headers">
                            <span>Nama Guru</span>
                            <span>Mata Pelajaran</span>
                            <span>Rombel</span>
                            <span>Status & Keterangan</span>
                            <span>Penugasan</span>
                            <span>Aksi</span>
                        </div>
                        <div class="jam-block-body">
                            @foreach($guruList as $guruNama => $info)
                                @php
                                    $key = $jam . '-' . $guruNama;
                                    $existing = $catatanHariIni[$key] ?? null;
                                    $status = $existing->status_kehadiran ?? '';
                                    $statusClass = '';
                                    if ($status == 'Hadir') $statusClass = 'hadir';
                                    elseif ($status == 'Tidak Hadir') $statusClass = 'tidak-hadir';
                                    elseif ($status == 'Izin') $statusClass = 'izin';
                                    elseif ($status == 'Terlambat') $statusClass = 'terlambat';
                                @endphp
                                <div class="guru-row" id="row-{{ $jam }}-{{ Str::slug($guruNama) }}">
                                    <div class="guru-name">
                                        <i class="fas fa-chalkboard-teacher" style="color: #6b7280;"></i>
                                        {{ $guruNama }}
                                        @if($existing)
                                            <span class="saved-indicator"><i class="fas fa-check-circle"></i></span>
                                        @endif
                                    </div>
                                    <div class="guru-mapel">{{ $info['mapel'] }}</div>
                                    <div class="guru-rombel">{{ $info['rombel'] }}</div>
                                    <div style="display:flex; gap:6px; flex-direction: column;">
                                        <select class="status-select {{ $statusClass }}" id="status-{{ $jam }}-{{ Str::slug($guruNama) }}" onchange="updateStatusColor(this)">
                                            <option value="">-- Pilih --</option>
                                            <option value="Hadir" {{ $status == 'Hadir' ? 'selected' : '' }}>✅ Hadir</option>
                                            <option value="Tidak Hadir" {{ $status == 'Tidak Hadir' ? 'selected' : '' }}>❌ Tidak Hadir</option>
                                            <option value="Izin" {{ $status == 'Izin' ? 'selected' : '' }}>📋 Izin</option>
                                            <option value="Terlambat" {{ $status == 'Terlambat' ? 'selected' : '' }}>⏰ Terlambat</option>
                                        </select>
                                        <input type="text" class="catatan-input" id="keterangan-{{ $jam }}-{{ Str::slug($guruNama) }}" placeholder="Keterangan..." value="{{ $existing->keterangan ?? '' }}">
                                    </div>
                                    <div>
                                        <input type="text" class="catatan-input" id="penugasan-{{ $jam }}-{{ Str::slug($guruNama) }}" placeholder="Penugasan guru..." value="{{ $existing->penugasan ?? '' }}">
                                    </div>
                                    <div>
                                        <button class="btn-save-row {{ $existing ? 'saved' : '' }}" id="btn-{{ $jam }}-{{ Str::slug($guruNama) }}"
                                            onclick="saveCatatan({{ $jam }}, '{{ addslashes($guruNama) }}', '{{ addslashes($info['mapel']) }}', '{{ addslashes($info['rombel']) }}', '{{ Str::slug($guruNama) }}')">
                                            <i class="fas {{ $existing ? 'fa-check' : 'fa-save' }}"></i> {{ $existing ? 'Tersimpan' : 'Simpan' }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-jam">
                            <i class="fas fa-minus-circle" style="color: #d1d5db;"></i>
                            Tidak ada jadwal guru pada jam ini
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function updateStatusColor(select) {
        select.className = 'status-select';
        const val = select.value;
        if (val === 'Hadir') select.classList.add('hadir');
        else if (val === 'Tidak Hadir') select.classList.add('tidak-hadir');
        else if (val === 'Izin') select.classList.add('izin');
        else if (val === 'Terlambat') select.classList.add('terlambat');
    }

    function saveCatatan(jamKe, namaGuru, namaMapel, namaRombel, slug) {
        const status = document.getElementById('status-' + jamKe + '-' + slug).value;
        const keterangan = document.getElementById('keterangan-' + jamKe + '-' + slug).value;
        const penugasan = document.getElementById('penugasan-' + jamKe + '-' + slug).value;
        const btn = document.getElementById('btn-' + jamKe + '-' + slug);

        if (!status) {
            alert('Pilih status kehadiran terlebih dahulu!');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch('{{ route("guru.catatan-piket.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                piket_kbm_id: {{ $piketHariIni->id }},
                tanggal: '{{ $tanggalHariIni }}',
                jam_ke: jamKe,
                nama_guru: namaGuru,
                nama_mapel: namaMapel,
                nama_rombel: namaRombel,
                status_kehadiran: status,
                keterangan: keterangan,
                penugasan: penugasan,
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                btn.classList.add('saved');
            } else {
                alert(result.message || 'Gagal menyimpan');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        });
    }
</script>
@endpush
