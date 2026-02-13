@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content jurnal-manual-page">

    {{-- Header --}}
    <div class="page-header-center">
        <div class="header-icon-large">
            <i class="fas fa-edit"></i>
        </div>
        <h1>Edit Jurnal Manual</h1>
        <p class="header-subtitle">Perbarui data aktivitas jurnal harian</p>
    </div>

    {{-- Form --}}
    <div class="form-container">
        <form method="POST" action="{{ route('guru_bk.jurnal-manual.update', $jurnal->id) }}" id="jurnalForm">
            @csrf
            @method('PUT')

            {{-- Row 1: Tanggal & Waktu --}}
            <div class="form-row">
                <div class="form-group half">
                    <label><i class="fas fa-calendar"></i> Tanggal <span class="req">*</span></label>
                    <input type="date" name="tanggal" class="form-input" value="{{ $jurnal->tanggal }}" required>
                </div>
                <div class="form-group half">
                    <label><i class="fas fa-clock"></i> Waktu</label>
                    <input type="time" name="waktu" class="form-input" value="{{ $jurnal->waktu }}">
                </div>
            </div>

            {{-- Row 2: Jenis Aktivitas --}}
            <div class="form-group">
                <label><i class="fas fa-tasks"></i> Jenis Aktivitas <span class="req">*</span></label>
                <select name="jenis_aktivitas" class="form-input" required id="jenisAktivitas">
                    <option value="">-- Pilih Jenis Aktivitas --</option>
                    @foreach([
                        'Konsultasi Individu', 'Konsultasi Kelompok', 'Kunjungan Rumah (Home Visit)',
                        'Konferensi Kasus', 'Mediasi', 'Advokasi', 'Kolaborasi dengan Guru',
                        'Kolaborasi dengan Wali Kelas', 'Terima Tamu', 'Rapat/Koordinasi',
                        'Penyusunan Program BK', 'Penyusunan Laporan', 'Asesmen/Pengumpulan Data',
                        'Layanan Klasikal', 'Layanan Informasi', 'Bimbingan Karir',
                        'Penanganan Krisis', 'Kegiatan Administratif', 'Lainnya'
                    ] as $opt)
                    <option value="{{ $opt }}" {{ $jurnal->jenis_aktivitas === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Row 3: Tipe Subyek --}}
            <div class="form-group">
                <label><i class="fas fa-user"></i> Obyek/Subyek Aktivitas <span class="req">*</span></label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="tipe_subyek" value="Siswa" id="tipeSiswa" {{ $jurnal->tipe_subyek === 'Siswa' ? 'checked' : '' }}>
                        <span class="radio-label"><i class="fas fa-user-graduate"></i> Siswa</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tipe_subyek" value="Lainnya" id="tipeLainnya" {{ $jurnal->tipe_subyek === 'Lainnya' ? 'checked' : '' }}>
                        <span class="radio-label"><i class="fas fa-users"></i> Lainnya</span>
                    </label>
                </div>
            </div>

            {{-- Siswa Search --}}
            <div class="form-group" id="siswaSearchGroup" style="{{ $jurnal->tipe_subyek === 'Siswa' ? '' : 'display: none;' }}">
                <label><i class="fas fa-search"></i> Cari Siswa</label>
                <div class="search-container">
                    <input type="text" class="form-input" id="searchSiswaInput" placeholder="Ketik nama, NIS, atau NISN siswa...">
                    <div class="search-results" id="searchResults" style="display: none;"></div>
                </div>
                <input type="hidden" name="nisn" id="selectedNisn" value="{{ $jurnal->nisn }}">
                <div id="selectedSiswaInfo" style="{{ $siswaData ? '' : 'display: none;' }}" class="selected-siswa-card">
                    <div class="siswa-info-row">
                        <span class="siswa-badge" id="siswaAvatar">{{ $siswaData ? strtoupper(substr($siswaData->nama, 0, 1)) : '' }}</span>
                        <div>
                            <div class="siswa-name" id="siswaName">{{ $siswaData->nama ?? '' }}</div>
                            <div class="siswa-detail" id="siswaDetail">{{ $siswaData ? ($siswaData->nama_rombel ?? '-') . ' · NISN: ' . $siswaData->nisn . ' · NIS: ' . ($siswaData->nis ?? '-') : '' }}</div>
                        </div>
                        <button type="button" class="btn-remove-siswa" onclick="removeSiswa()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>

            {{-- Manual Subyek --}}
            <div class="form-group" id="manualSubyekGroup" style="{{ $jurnal->tipe_subyek === 'Lainnya' ? '' : 'display: none;' }}">
                <label><i class="fas fa-pen"></i> Nama Subyek/Obyek</label>
                <input type="text" name="subyek_manual" class="form-input" id="subyekManual" value="{{ $jurnal->subyek_manual }}" placeholder="Contoh: Orang Tua Siswa, Guru Mapel, Kepala Sekolah, dll">
            </div>

            {{-- Deskripsi --}}
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Deskripsi Aktivitas <span class="req">*</span></label>
                <textarea name="deskripsi" class="form-input textarea" rows="4" required placeholder="Jelaskan detail aktivitas yang dilakukan...">{{ $jurnal->deskripsi }}</textarea>
            </div>

            {{-- Keterangan --}}
            <div class="form-group">
                <label><i class="fas fa-sticky-note"></i> Keterangan Lain</label>
                <textarea name="keterangan" class="form-input textarea" rows="3" placeholder="Catatan tambahan (opsional)...">{{ $jurnal->keterangan }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('guru_bk.jurnal-harian') }}" class="btn-cancel"><i class="fas fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<style>
.jurnal-manual-page { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }
.jurnal-manual-page .page-header-center { text-align: center; margin-bottom: 25px; }
.jurnal-manual-page .header-icon-large {
    width: 70px; height: 70px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white; margin: 0 auto 16px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 8px 25px rgba(245,158,11,0.4);
}
.jurnal-manual-page h1 { font-size: 24px; font-weight: 700; margin: 0 0 6px 0; color: #1f2937; }
.header-subtitle { color: #6b7280; font-size: 14px; margin: 0; }
.form-container { max-width: 700px; margin: 0 auto; background: white; border-radius: 16px; padding: 30px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; }
.form-row { display: flex; gap: 15px; }
.form-group { margin-bottom: 20px; }
.form-group.half { flex: 1; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.form-group label i { color: #f59e0b; margin-right: 5px; }
.req { color: #ef4444; }
.form-input { width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; transition: all 0.3s; box-sizing: border-box; font-family: inherit; }
.form-input:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 4px rgba(245,158,11,0.1); }
.form-input.textarea { resize: vertical; min-height: 80px; }
select.form-input { appearance: auto; }
.radio-group { display: flex; gap: 12px; }
.radio-option { display: flex; align-items: center; cursor: pointer; }
.radio-option input[type="radio"] { display: none; }
.radio-label { padding: 10px 20px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 13px; font-weight: 600; color: #6b7280; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
.radio-option input[type="radio"]:checked + .radio-label { border-color: #f59e0b; background: rgba(245,158,11,0.08); color: #b45309; }
.radio-label:hover { border-color: #f59e0b; }
.search-container { position: relative; }
.search-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 50; background: white; border: 2px solid #e5e7eb; border-top: 0; border-radius: 0 0 10px 10px; max-height: 280px; overflow-y: auto; box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
.search-item { padding: 10px 14px; cursor: pointer; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
.search-item:hover { background: #f9fafb; }
.search-item:last-child { border-bottom: 0; }
.search-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; color: white; flex-shrink: 0; }
.search-avatar.l { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.search-avatar.p { background: linear-gradient(135deg, #ec4899, #db2777); }
.search-name { font-weight: 600; font-size: 13px; color: #1f2937; }
.search-meta { font-size: 11px; color: #6b7280; }
.search-empty { padding: 20px; text-align: center; color: #9ca3af; font-size: 13px; }
.selected-siswa-card { margin-top: 10px; padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; }
.siswa-info-row { display: flex; align-items: center; gap: 10px; }
.siswa-badge { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; color: white; background: #10b981; flex-shrink: 0; }
.siswa-name { font-weight: 600; font-size: 14px; color: #065f46; }
.siswa-detail { font-size: 12px; color: #6b7280; }
.btn-remove-siswa { margin-left: auto; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 8px; width: 30px; height: 30px; cursor: pointer; font-size: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.btn-remove-siswa:hover { background: #fee2e2; }
.form-actions { display: flex; justify-content: space-between; gap: 12px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
.btn-cancel { padding: 12px 24px; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
.btn-cancel:hover { background: #e5e7eb; color: #374151; }
.btn-save { padding: 12px 30px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,185,129,0.4); }

@media (max-width: 768px) {
    .jurnal-manual-page { padding: 12px; }
    .form-container { padding: 20px; }
    .form-row { flex-direction: column; gap: 0; }
    .radio-group { flex-direction: column; }
    .form-actions { flex-direction: column-reverse; }
    .btn-save, .btn-cancel { justify-content: center; width: 100%; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSiswa = document.getElementById('tipeSiswa');
    const tipeLainnya = document.getElementById('tipeLainnya');
    const siswaSearchGroup = document.getElementById('siswaSearchGroup');
    const manualSubyekGroup = document.getElementById('manualSubyekGroup');
    const searchInput = document.getElementById('searchSiswaInput');
    const searchResults = document.getElementById('searchResults');

    tipeSiswa.addEventListener('change', function() {
        siswaSearchGroup.style.display = 'block';
        manualSubyekGroup.style.display = 'none';
        document.getElementById('subyekManual').value = '';
    });

    tipeLainnya.addEventListener('change', function() {
        siswaSearchGroup.style.display = 'none';
        manualSubyekGroup.style.display = 'block';
        removeSiswa();
    });

    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { searchResults.style.display = 'none'; return; }
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('guru_bk.jurnal-manual.search-siswa') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="search-empty"><i class="fas fa-search"></i> Siswa tidak ditemukan</div>';
                    } else {
                        searchResults.innerHTML = data.map(s => `
                            <div class="search-item" onclick="selectSiswa('${s.nisn}', '${s.nama}', '${s.jk || ''}', '${s.nama_rombel || '-'}', '${s.nis || '-'}')">
                                <div class="search-avatar ${s.jk === 'Laki-laki' ? 'l' : 'p'}">${s.nama.charAt(0).toUpperCase()}</div>
                                <div>
                                    <div class="search-name">${s.nama}</div>
                                    <div class="search-meta">${s.nama_rombel || '-'} · NISN: ${s.nisn} · NIS: ${s.nis || '-'}</div>
                                </div>
                            </div>
                        `).join('');
                    }
                    searchResults.style.display = 'block';
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) searchResults.style.display = 'none';
    });
});

function selectSiswa(nisn, nama, jk, rombel, nis) {
    document.getElementById('selectedNisn').value = nisn;
    document.getElementById('siswaAvatar').textContent = nama.charAt(0).toUpperCase();
    document.getElementById('siswaName').textContent = nama;
    document.getElementById('siswaDetail').textContent = `${rombel} · NISN: ${nisn} · NIS: ${nis}`;
    document.getElementById('selectedSiswaInfo').style.display = 'block';
    document.getElementById('searchSiswaInput').value = '';
    document.getElementById('searchResults').style.display = 'none';
}

function removeSiswa() {
    document.getElementById('selectedNisn').value = '';
    document.getElementById('selectedSiswaInfo').style.display = 'none';
    document.getElementById('searchSiswaInput').value = '';
}
</script>
@endsection
