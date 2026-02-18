@extends($layout ?? 'layouts.app')

@section('title', 'Tugas Tambahan Lainnya | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        {{-- Header --}}
        <div class="tt-header">
            <div class="tt-header-content">
                <div class="tt-header-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="tt-header-text">
                    <h1>Tugas Tambahan Lainnya</h1>
                    <p>Kelola jenis tugas tambahan guru di luar KBM</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="tt-actions">
            <button onclick="openJenisModal()" class="btn-jenis-tugas">
                <i class="fas fa-list-alt"></i> Jenis Tugas Tambahan
            </button>
            <button onclick="openInputModal()" class="btn-input-tugas">
                <i class="fas fa-plus-circle"></i> Input Tugas Tambahan
            </button>
        </div>

        {{-- Tugas Tambahan List --}}
        <div class="tt-content-section">
            <div class="tt-section-header">
                <div class="tt-section-title">
                    <i class="fas fa-clipboard-list"></i>
                    <h2>Daftar Tugas Tambahan Guru</h2>
                </div>
                <span class="tt-section-count">{{ count($tugasList) }} Data</span>
            </div>

            @if(count($tugasList) == 0)
            <div class="tt-empty-state">
                <div class="tt-empty-icon">
                    <i class="fas fa-clipboard"></i>
                </div>
                <h3>Belum Ada Tugas Tambahan</h3>
                <p>Klik "Input Tugas Tambahan" untuk menambahkan data.</p>
            </div>
            @else
            <div class="tt-tugas-table-wrapper">
                <table class="tt-tugas-table">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Jenis Tugas</th>
                            <th>Nama Guru</th>
                            <th>Tipe</th>
                            <th>Keterangan</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tugasList as $i => $tugas)
                        <tr data-tugas-id="{{ $tugas->id }}">
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>
                                <span class="tt-badge-jenis">{{ $tugas->jenis_nama }}</span>
                            </td>
                            <td>
                                <div class="tt-guru-name">{{ $tugas->nama_guru }}</div>
                                <div class="tt-guru-nip">{{ $tugas->nip_guru }}</div>
                            </td>
                            <td>
                                <span class="tt-badge-tipe {{ $tugas->tipe_guru === 'guru' ? 'tipe-guru' : 'tipe-bk' }}">
                                    {{ $tugas->tipe_guru === 'guru' ? 'Guru' : 'Guru BK' }}
                                </span>
                            </td>
                            <td>{{ $tugas->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <div class="tt-tugas-actions">
                                    <button class="tt-btn-edit-tugas" onclick="openEditTugas({{ $tugas->id }}, {{ $tugas->jenis_tugas_id }}, '{{ addslashes($tugas->keterangan ?? '') }}')" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="tt-btn-del-tugas" onclick="hapusTugas({{ $tugas->id }})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===================== MODAL: JENIS TUGAS ===================== --}}
<div id="jenisModal" class="tt-modal">
    <div class="tt-modal-content">
        <div class="tt-modal-header">
            <h3><i class="fas fa-list-alt"></i> Jenis Tugas Tambahan Lainnya</h3>
            <button class="tt-modal-close" onclick="closeJenisModal()">&times;</button>
        </div>
        <div class="tt-modal-body">
            <div class="tt-input-section">
                <div class="tt-input-group">
                    <label for="inputNamaTugas"><i class="fas fa-tag"></i> Nama Jenis Tugas</label>
                    <input type="text" id="inputNamaTugas" placeholder="Contoh: Wali Kelas, Pembina OSIS..." maxlength="255">
                </div>
                <div class="tt-input-group">
                    <label for="inputDeskripsi"><i class="fas fa-align-left"></i> Deskripsi <span class="optional">(opsional)</span></label>
                    <textarea id="inputDeskripsi" placeholder="Deskripsi singkat tugas..." rows="2" maxlength="500"></textarea>
                </div>
                <button type="button" id="btnSimpanJenis" onclick="simpanJenis()">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>

            <div class="tt-divider">
                <span><i class="fas fa-list"></i> Daftar Jenis Tugas Tambahan</span>
            </div>

            <div class="tt-jenis-list" id="jenisListContainer">
                @if(count($jenisList) == 0)
                    <div class="tt-jenis-empty" id="jenisEmptyState">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada jenis tugas tambahan.</p>
                    </div>
                @else
                    @foreach($jenisList as $jenis)
                    <div class="tt-jenis-item" data-id="{{ $jenis->id }}">
                        <div class="tt-jenis-info">
                            <div class="tt-jenis-nama">{{ $jenis->nama_tugas }}</div>
                            @if($jenis->deskripsi)
                                <div class="tt-jenis-desc">{{ $jenis->deskripsi }}</div>
                            @endif
                        </div>
                        <div class="tt-jenis-actions">
                            <button class="tt-jenis-edit" onclick="editJenis({{ $jenis->id }}, '{{ addslashes($jenis->nama_tugas) }}', '{{ addslashes($jenis->deskripsi ?? '') }}')" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="tt-jenis-delete" onclick="hapusJenis({{ $jenis->id }}, '{{ addslashes($jenis->nama_tugas) }}')" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL: INPUT TUGAS ===================== --}}
<div id="inputTugasModal" class="tt-modal">
    <div class="tt-modal-content" style="max-width: 620px;">
        <div class="tt-modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <h3><i class="fas fa-plus-circle"></i> Input Tugas Tambahan</h3>
            <button class="tt-modal-close" onclick="closeInputModal()">&times;</button>
        </div>
        <div class="tt-modal-body">
            {{-- Jenis Tugas Select --}}
            <div class="tt-input-group">
                <label><i class="fas fa-tag"></i> Jenis Tugas Tambahan</label>
                <select id="selectJenisTugas">
                    <option value="">-- Pilih Jenis Tugas --</option>
                    @foreach($jenisList as $jenis)
                        <option value="{{ $jenis->id }}">{{ $jenis->nama_tugas }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Guru Selection --}}
            <div class="tt-input-group">
                <label><i class="fas fa-chalkboard-teacher"></i> Pilih Guru / Guru BK</label>
                <button type="button" class="btn-tambah-guru" onclick="openGuruPickerModal()">
                    <i class="fas fa-user-plus"></i> Tambah Guru
                </button>
                <div class="tt-guru-chips" id="guruChipsContainer">
                    <div class="tt-guru-chips-empty" id="guruChipsEmpty">Belum ada guru dipilih</div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="tt-input-group">
                <label><i class="fas fa-sticky-note"></i> Keterangan <span class="optional">(opsional)</span></label>
                <textarea id="inputKeteranganTugas" placeholder="Keterangan tambahan..." rows="2" maxlength="1000"></textarea>
            </div>

            {{-- Submit --}}
            <button type="button" id="btnSimpanTugas" onclick="simpanTugas()" class="btn-simpan-tugas">
                <i class="fas fa-save"></i> Simpan Tugas Tambahan
            </button>
        </div>
    </div>
</div>

{{-- ===================== MODAL: EDIT TUGAS ===================== --}}
<div id="editTugasModal" class="tt-modal">
    <div class="tt-modal-content" style="max-width: 500px;">
        <div class="tt-modal-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <h3><i class="fas fa-edit"></i> Edit Tugas Tambahan</h3>
            <button class="tt-modal-close" onclick="closeEditTugas()">&times;</button>
        </div>
        <div class="tt-modal-body">
            <input type="hidden" id="editTugasId">

            <div class="tt-input-group">
                <label><i class="fas fa-tag"></i> Jenis Tugas Tambahan</label>
                <select id="editSelectJenisTugas">
                    <option value="">-- Pilih Jenis Tugas --</option>
                    @foreach($jenisList as $jenis)
                        <option value="{{ $jenis->id }}">{{ $jenis->nama_tugas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="tt-input-group">
                <label><i class="fas fa-sticky-note"></i> Keterangan <span class="optional">(opsional)</span></label>
                <textarea id="editKeteranganTugas" placeholder="Keterangan tambahan..." rows="2" maxlength="1000"></textarea>
            </div>

            <button type="button" id="btnUpdateTugas" onclick="updateTugas()" class="btn-update-tugas">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

{{-- ===================== MODAL: PILIH GURU ===================== --}}
<div id="guruPickerModal" class="tt-modal">
    <div class="tt-modal-content" style="max-width: 520px;">
        <div class="tt-modal-header" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
            <h3><i class="fas fa-chalkboard-teacher"></i> Pilih Guru</h3>
            <button class="tt-modal-close" onclick="closeGuruPickerModal()">&times;</button>
        </div>
        <div class="tt-modal-body">
            {{-- Search --}}
            <div class="tt-input-group" style="margin-bottom: 12px;">
                <input type="text" id="searchGuruInput" placeholder="Cari nama guru..." onkeyup="filterGuruList()">
            </div>

            {{-- Selected count --}}
            <div class="tt-selected-preview" id="selectedGuruPreview" style="display:none;">
                <i class="fas fa-check-circle"></i> <span id="selectedGuruCount">0</span> guru dipilih
            </div>

            {{-- Guru List --}}
            <div class="tt-guru-select-list" id="guruSelectList">
                @if(count($guruList) > 0)
                <div class="tt-guru-group-label">Guru Mapel</div>
                @foreach($guruList as $guru)
                <div class="tt-guru-select-item" data-nama="{{ strtolower($guru->nama) }}" data-value="guru_{{ $guru->id }}" data-display="{{ $guru->nama }}" data-tipe="Guru" onclick="toggleGuruSelect(this)">
                    <input type="checkbox" class="guru-checkbox" value="guru_{{ $guru->id }}" onclick="event.stopPropagation()">
                    <div class="tt-guru-select-info">
                        <span class="tt-guru-select-nama">{{ $guru->nama }}</span>
                        <span class="tt-guru-select-nip">{{ $guru->nip }}</span>
                    </div>
                    <span class="tt-guru-tipe-badge tipe-guru">Guru</span>
                </div>
                @endforeach
                @endif

                @if(count($guruBKList) > 0)
                <div class="tt-guru-group-label">Guru BK</div>
                @foreach($guruBKList as $bk)
                <div class="tt-guru-select-item" data-nama="{{ strtolower($bk->nama) }}" data-value="gurubk_{{ $bk->id }}" data-display="{{ $bk->nama }}" data-tipe="BK" onclick="toggleGuruSelect(this)">
                    <input type="checkbox" class="guru-checkbox" value="gurubk_{{ $bk->id }}" onclick="event.stopPropagation()">
                    <div class="tt-guru-select-info">
                        <span class="tt-guru-select-nama">{{ $bk->nama }}</span>
                        <span class="tt-guru-select-nip">{{ $bk->nip }}</span>
                    </div>
                    <span class="tt-guru-tipe-badge tipe-bk">BK</span>
                </div>
                @endforeach
                @endif
            </div>

            {{-- Confirm Button --}}
            <button type="button" class="btn-konfirmasi-guru" onclick="confirmGuruSelection()">
                <i class="fas fa-check"></i> Konfirmasi Pilihan
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="ttToast" class="tt-toast"></div>

<style>
/* ===== PAGE LAYOUT ===== */
.tt-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    color: white;
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
}
.tt-header-content { display: flex; align-items: center; gap: 1.25rem; }
.tt-header-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
.tt-header-text h1 { margin: 0 0 0.25rem 0; font-size: 1.6rem; font-weight: 700; }
.tt-header-text p { margin: 0; font-size: 0.85rem; opacity: 0.9; }

/* ===== ACTIONS ===== */
.tt-actions { display: flex; gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }

.btn-jenis-tugas, .btn-input-tugas {
    display: inline-flex; align-items: center; gap: 10px; padding: 14px 24px;
    color: white; border: none; border-radius: 12px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.3s ease;
}
.btn-jenis-tugas {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.35);
}
.btn-input-tugas {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
}
.btn-jenis-tugas:hover, .btn-input-tugas:hover { transform: translateY(-2px); }

/* ===== CONTENT SECTION ===== */
.tt-content-section {
    background: white; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07); overflow: hidden;
}
.tt-section-header {
    padding: 20px 24px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.tt-section-title { display: flex; align-items: center; gap: 10px; }
.tt-section-title i { color: #8b5cf6; font-size: 18px; }
.tt-section-title h2 { margin: 0; font-size: 1.1rem; color: #1f2937; }
.tt-section-count { padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: rgba(139,92,246,0.1); color: #8b5cf6; }

.tt-empty-state { text-align: center; padding: 60px 20px; }
.tt-empty-icon { width: 90px; height: 90px; background: linear-gradient(135deg, #f3e8ff, #ede9fe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
.tt-empty-icon i { font-size: 36px; color: #8b5cf6; }
.tt-empty-state h3 { margin: 0 0 10px; color: #374151; font-size: 1.2rem; }
.tt-empty-state p { color: #6b7280; margin: 0; }

/* ===== TUGAS TABLE ===== */
.tt-tugas-table-wrapper { overflow-x: auto; }
.tt-tugas-table { width: 100%; border-collapse: collapse; }
.tt-tugas-table thead th {
    padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700;
    color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;
    background: #f9fafb; border-bottom: 2px solid #e5e7eb;
}
.tt-tugas-table tbody td {
    padding: 14px 16px; border-bottom: 1px solid #f3f4f6;
    font-size: 14px; color: #374151; vertical-align: middle;
}
.tt-tugas-table tbody tr:hover { background: #faf5ff; }
.text-center { text-align: center; }

.tt-badge-jenis {
    display: inline-block; padding: 4px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    background: rgba(139,92,246,0.1); color: #7c3aed;
}
.tt-badge-tipe {
    display: inline-block; padding: 3px 10px; border-radius: 15px;
    font-size: 11px; font-weight: 600;
}
.tipe-guru { background: rgba(59,130,246,0.1); color: #3b82f6; }
.tipe-bk { background: rgba(236,72,153,0.1); color: #ec4899; }

.tt-guru-name { font-weight: 600; color: #1f2937; }
.tt-guru-nip { font-size: 12px; color: #9ca3af; margin-top: 2px; }

.tt-tugas-actions { display: flex; gap: 6px; justify-content: center; }

.tt-btn-edit-tugas {
    width: 34px; height: 34px; border-radius: 8px;
    background: rgba(245,158,11,0.08); border: none; color: #f59e0b;
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; transition: all 0.2s;
}
.tt-btn-edit-tugas:hover { background: rgba(245,158,11,0.18); transform: scale(1.1); }

.tt-btn-del-tugas {
    width: 34px; height: 34px; border-radius: 8px;
    background: rgba(239,68,68,0.08); border: none; color: #ef4444;
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; transition: all 0.2s;
}
.tt-btn-del-tugas:hover { background: rgba(239,68,68,0.15); transform: scale(1.1); }

.btn-update-tugas {
    width: 100%; padding: 12px;
    background: linear-gradient(135deg, #f59e0b, #d97706); color: white;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all 0.2s; box-shadow: 0 3px 10px rgba(245,158,11,0.3);
}
.btn-update-tugas:hover { transform: translateY(-1px); }
.btn-update-tugas:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

/* ===== MODAL ===== */
.tt-modal {
    display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 10000; justify-content: center; align-items: center; padding: 20px;
}
.tt-modal.active { display: flex; animation: ttFadeIn 0.25s ease; }
@keyframes ttFadeIn { from { opacity: 0; } to { opacity: 1; } }

.tt-modal-content {
    background: white; border-radius: 16px; width: 100%; max-width: 560px; max-height: 85vh;
    display: flex; flex-direction: column;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25); animation: ttSlideUp 0.3s ease;
}
@keyframes ttSlideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.tt-modal-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 24px; border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 16px 16px 0 0; color: white;
}
.tt-modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px; }
.tt-modal-close {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.2); border: none; color: white;
    font-size: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.tt-modal-close:hover { background: rgba(255,255,255,0.35); }
.tt-modal-body { padding: 24px; overflow-y: auto; flex: 1; }

/* ===== INPUT SECTIONS ===== */
.tt-input-section { background: #f9fafb; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.tt-input-group { margin-bottom: 14px; }
.tt-input-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.tt-input-group label i { color: #8b5cf6; margin-right: 4px; }
.tt-input-group .optional { font-weight: 400; color: #9ca3af; font-size: 11px; }

.tt-input-group input,
.tt-input-group textarea,
.tt-input-group select {
    width: 100%; padding: 10px 14px; border: 1.5px solid #d1d5db; border-radius: 10px;
    font-size: 14px; font-family: inherit; transition: all 0.2s; background: white; box-sizing: border-box;
}
.tt-input-group input:focus,
.tt-input-group textarea:focus,
.tt-input-group select:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.12); outline: none; }
.tt-input-group textarea { resize: vertical; min-height: 60px; }

#btnSimpanJenis {
    width: 100%; padding: 12px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all 0.2s ease; box-shadow: 0 3px 10px rgba(139,92,246,0.3);
}
#btnSimpanJenis:hover { transform: translateY(-1px); }
#btnSimpanJenis:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

.btn-simpan-tugas {
    width: 100%; padding: 14px;
    background: linear-gradient(135deg, #10b981, #059669); color: white;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all 0.2s ease; box-shadow: 0 3px 10px rgba(16,185,129,0.3);
}
.btn-simpan-tugas:hover { transform: translateY(-1px); }
.btn-simpan-tugas:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

/* ===== TAMBAH GURU BUTTON ===== */
.btn-tambah-guru {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
    background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;
    border: none; border-radius: 10px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.2s; box-shadow: 0 3px 10px rgba(59,130,246,0.25);
}
.btn-tambah-guru:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(59,130,246,0.35); }
.btn-tambah-guru i { font-size: 14px; }

/* ===== GURU CHIPS ===== */
.tt-guru-chips {
    display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;
    min-height: 36px; padding: 8px 0;
}
.tt-guru-chips-empty {
    font-size: 13px; color: #9ca3af; font-style: italic; padding: 4px 0;
}
.tt-guru-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
    animation: chipIn 0.2s ease;
}
@keyframes chipIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.tt-guru-chip.chip-guru { background: rgba(59,130,246,0.1); color: #3b82f6; }
.tt-guru-chip.chip-bk { background: rgba(236,72,153,0.1); color: #ec4899; }
.tt-guru-chip-remove {
    width: 18px; height: 18px; border-radius: 50%; border: none;
    background: rgba(0,0,0,0.1); color: inherit; font-size: 11px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.15s; padding: 0; line-height: 1;
}
.tt-guru-chip-remove:hover { background: rgba(0,0,0,0.2); transform: scale(1.1); }

/* ===== KONFIRMASI GURU BUTTON ===== */
.btn-konfirmasi-guru {
    width: 100%; padding: 12px; margin-top: 16px;
    background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all 0.2s; box-shadow: 0 3px 10px rgba(59,130,246,0.3);
}
.btn-konfirmasi-guru:hover { transform: translateY(-1px); }

/* ===== DIVIDER ===== */
.tt-divider { display: flex; align-items: center; margin: 24px 0 16px; }
.tt-divider::before, .tt-divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }
.tt-divider span { padding: 0 14px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
.tt-divider span i { margin-right: 5px; color: #8b5cf6; }

/* ===== JENIS LIST ===== */
.tt-jenis-list { display: flex; flex-direction: column; gap: 8px; max-height: 280px; overflow-y: auto; }
.tt-jenis-list::-webkit-scrollbar { width: 5px; }
.tt-jenis-list::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }

.tt-jenis-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px; background: white; border: 1px solid #e5e7eb; border-radius: 10px; transition: all 0.2s ease;
}
.tt-jenis-item:hover { border-color: #c4b5fd; box-shadow: 0 2px 8px rgba(139,92,246,0.1); }
.tt-jenis-info { flex: 1; min-width: 0; }
.tt-jenis-nama { font-size: 14px; font-weight: 600; color: #1f2937; }
.tt-jenis-desc { font-size: 12px; color: #6b7280; margin-top: 3px; line-height: 1.4; }

.tt-jenis-actions { display: flex; gap: 6px; flex-shrink: 0; margin-left: 10px; }
.tt-jenis-edit, .tt-jenis-delete {
    width: 34px; height: 34px; border-radius: 8px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.2s;
}
.tt-jenis-edit { background: rgba(139,92,246,0.08); color: #8b5cf6; }
.tt-jenis-edit:hover { background: rgba(139,92,246,0.18); transform: scale(1.1); }
.tt-jenis-delete { background: rgba(239,68,68,0.08); color: #ef4444; }
.tt-jenis-delete:hover { background: rgba(239,68,68,0.15); transform: scale(1.1); }

/* INLINE EDIT */
.tt-jenis-item.editing { border-color: #8b5cf6; box-shadow: 0 2px 12px rgba(139,92,246,0.15); background: #faf5ff; }
.tt-edit-form { flex: 1; display: flex; flex-direction: column; gap: 8px; min-width: 0; }
.tt-edit-form input, .tt-edit-form textarea { width: 100%; padding: 8px 12px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 13px; font-family: inherit; box-sizing: border-box; }
.tt-edit-form input:focus, .tt-edit-form textarea:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.1); outline: none; }
.tt-edit-actions { display: flex; gap: 6px; flex-shrink: 0; margin-left: 10px; align-self: flex-start; }
.tt-edit-save, .tt-edit-cancel { width: 34px; height: 34px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.2s; }
.tt-edit-save { background: rgba(16,185,129,0.1); color: #10b981; }
.tt-edit-save:hover { background: rgba(16,185,129,0.2); transform: scale(1.1); }
.tt-edit-cancel { background: rgba(107,114,128,0.1); color: #6b7280; }
.tt-edit-cancel:hover { background: rgba(107,114,128,0.2); transform: scale(1.1); }

/* ===== GURU SELECT LIST ===== */
.tt-selected-preview { padding: 8px 14px; background: #ecfdf5; border-radius: 8px; font-size: 13px; color: #059669; margin: 8px 0; }

.tt-guru-select-list { max-height: 240px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 10px; margin-top: 8px; }
.tt-guru-select-list::-webkit-scrollbar { width: 5px; }
.tt-guru-select-list::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }

.tt-guru-group-label {
    padding: 8px 14px; background: #f3f4f6; font-size: 11px; font-weight: 700;
    color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;
    position: sticky; top: 0; z-index: 1;
}
.tt-guru-select-item {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f3f4f6;
}
.tt-guru-select-item:hover { background: #faf5ff; }
.tt-guru-select-item.selected { background: #ecfdf5; }
.tt-guru-select-info { flex: 1; min-width: 0; }
.tt-guru-select-nama { font-size: 13px; font-weight: 600; color: #1f2937; display: block; }
.tt-guru-select-nip { font-size: 11px; color: #9ca3af; }
.tt-guru-tipe-badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; flex-shrink: 0; }

/* ===== JENIS EMPTY ===== */
.tt-jenis-empty { text-align: center; padding: 30px 20px; color: #9ca3af; }
.tt-jenis-empty i { font-size: 28px; margin-bottom: 8px; display: block; }
.tt-jenis-empty p { margin: 0; font-size: 13px; }

/* ===== TOAST ===== */
.tt-toast {
    position: fixed; top: 20px; right: 20px; min-width: 280px; padding: 16px 20px;
    border-radius: 12px; color: white; font-size: 14px; font-weight: 600;
    display: none; align-items: center; gap: 10px; z-index: 20000;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2); animation: ttSlideInRight 0.3s ease;
}
@keyframes ttSlideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.tt-toast.success { background: linear-gradient(135deg, #10b981, #059669); }
.tt-toast.error { background: linear-gradient(135deg, #ef4444, #dc2626); }
.tt-toast.show { display: flex; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .tt-header { padding: 1.25rem; }
    .tt-header-text h1 { font-size: 1.3rem; }
    .tt-header-icon { width: 48px; height: 48px; font-size: 1.2rem; }
    .tt-modal-content { max-height: 90vh; }
    .tt-modal-body { padding: 16px; }
    .tt-tugas-table thead th, .tt-tugas-table tbody td { padding: 10px 12px; font-size: 13px; }
}
</style>

<script>
/* ===== MODAL: JENIS ===== */
function openJenisModal() {
    document.getElementById('jenisModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeJenisModal() {
    document.getElementById('jenisModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('jenisModal').addEventListener('click', function(e) { if (e.target === this) closeJenisModal(); });

/* ===== MODAL: INPUT TUGAS ===== */
function openInputModal() {
    document.getElementById('inputTugasModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeInputModal() {
    document.getElementById('inputTugasModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('inputTugasModal').addEventListener('click', function(e) { if (e.target === this) closeInputModal(); });

/* ===== MODAL: GURU PICKER ===== */
function openGuruPickerModal() {
    // Hide input modal temporarily
    document.getElementById('inputTugasModal').classList.remove('active');
    document.getElementById('guruPickerModal').classList.add('active');
    document.getElementById('searchGuruInput').value = '';
    filterGuruList();
}
function closeGuruPickerModal() {
    document.getElementById('guruPickerModal').classList.remove('active');
    // Re-show input modal
    document.getElementById('inputTugasModal').classList.add('active');
}
document.getElementById('guruPickerModal').addEventListener('click', function(e) { if (e.target === this) closeGuruPickerModal(); });

/* ===== TOAST ===== */
function showToast(message, type = 'success') {
    const toast = document.getElementById('ttToast');
    toast.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> ' + message;
    toast.className = 'tt-toast ' + type + ' show';
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

/* ===== SIMPAN JENIS ===== */
function simpanJenis() {
    const namaTugas = document.getElementById('inputNamaTugas').value.trim();
    const deskripsi = document.getElementById('inputDeskripsi').value.trim();
    if (!namaTugas) { showToast('Nama jenis tugas tidak boleh kosong!', 'error'); return; }

    const btn = document.getElementById('btnSimpanJenis');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route("admin.tugas-tambahan.jenis.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ nama_tugas: namaTugas, deskripsi: deskripsi })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            document.getElementById('inputNamaTugas').value = '';
            document.getElementById('inputDeskripsi').value = '';
            renderJenisList(data.data);
            updateJenisSelect(data.data);
        } else { showToast(data.message, 'error'); }
    })
    .catch(() => showToast('Terjadi kesalahan!', 'error'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan'; });
}

/* ===== HAPUS JENIS ===== */
function hapusJenis(id, nama) {
    if (!confirm('Yakin ingin menghapus "' + nama + '"?')) return;
    fetch('{{ route("admin.tugas-tambahan.jenis.delete") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { showToast(data.message, 'success'); renderJenisList(data.data); updateJenisSelect(data.data); }
        else { showToast(data.message, 'error'); }
    })
    .catch(() => showToast('Terjadi kesalahan!', 'error'));
}

/* ===== EDIT JENIS ===== */
function editJenis(id, nama, deskripsi) {
    const item = document.querySelector('.tt-jenis-item[data-id="' + id + '"]');
    if (!item || item.classList.contains('editing')) return;
    item.classList.add('editing');
    item.innerHTML =
        '<div class="tt-edit-form">' +
        '  <input type="text" class="edit-nama" value="' + escapeAttr(nama) + '" maxlength="255">' +
        '  <textarea class="edit-desc" rows="2" maxlength="500">' + escapeHtml(deskripsi) + '</textarea>' +
        '</div>' +
        '<div class="tt-edit-actions">' +
        '  <button class="tt-edit-save" onclick="updateJenis(' + id + ')" title="Simpan"><i class="fas fa-check"></i></button>' +
        '  <button class="tt-edit-cancel" onclick="cancelEdit()" title="Batal"><i class="fas fa-times"></i></button>' +
        '</div>';
    item.querySelector('.edit-nama').focus();
    item.querySelector('.edit-nama').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); updateJenis(id); }
        if (e.key === 'Escape') cancelEdit();
    });
}
function cancelEdit() { location.reload(); }

function updateJenis(id) {
    const item = document.querySelector('.tt-jenis-item[data-id="' + id + '"]');
    const nama = item.querySelector('.edit-nama').value.trim();
    const desc = item.querySelector('.edit-desc').value.trim();
    if (!nama) { showToast('Nama tidak boleh kosong!', 'error'); return; }

    const saveBtn = item.querySelector('.tt-edit-save');
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; saveBtn.disabled = true;

    fetch('{{ route("admin.tugas-tambahan.jenis.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ id: id, nama_tugas: nama, deskripsi: desc })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { showToast(data.message, 'success'); renderJenisList(data.data); updateJenisSelect(data.data); }
        else { showToast(data.message, 'error'); saveBtn.innerHTML = '<i class="fas fa-check"></i>'; saveBtn.disabled = false; }
    })
    .catch(() => { showToast('Terjadi kesalahan!', 'error'); saveBtn.innerHTML = '<i class="fas fa-check"></i>'; saveBtn.disabled = false; });
}

/* ===== RENDER JENIS LIST ===== */
function renderJenisList(items) {
    const container = document.getElementById('jenisListContainer');
    if (!items || items.length === 0) {
        container.innerHTML = '<div class="tt-jenis-empty"><i class="fas fa-inbox"></i><p>Belum ada jenis tugas tambahan.</p></div>';
        return;
    }
    let html = '';
    items.forEach(item => {
        const ns = escapeAttr(item.nama_tugas), ds = escapeAttr(item.deskripsi || '');
        html += '<div class="tt-jenis-item" data-id="' + item.id + '">';
        html += '  <div class="tt-jenis-info"><div class="tt-jenis-nama">' + escapeHtml(item.nama_tugas) + '</div>';
        if (item.deskripsi) html += '<div class="tt-jenis-desc">' + escapeHtml(item.deskripsi) + '</div>';
        html += '</div><div class="tt-jenis-actions">';
        html += '<button class="tt-jenis-edit" onclick="editJenis(' + item.id + ',\'' + ns.replace(/'/g,"\\'") + '\',\'' + ds.replace(/'/g,"\\'") + '\')" title="Edit"><i class="fas fa-pen"></i></button>';
        html += '<button class="tt-jenis-delete" onclick="hapusJenis(' + item.id + ',\'' + ns.replace(/'/g,"\\'") + '\')" title="Hapus"><i class="fas fa-trash-alt"></i></button>';
        html += '</div></div>';
    });
    container.innerHTML = html;
}

/* ===== UPDATE JENIS SELECT IN INPUT MODAL ===== */
function updateJenisSelect(items) {
    const select = document.getElementById('selectJenisTugas');
    select.innerHTML = '<option value="">-- Pilih Jenis Tugas --</option>';
    if (items) {
        items.forEach(item => {
            select.innerHTML += '<option value="' + item.id + '">' + escapeHtml(item.nama_tugas) + '</option>';
        });
    }
}

/* ===== GURU SELECTION (IN PICKER MODAL) ===== */
let selectedGuru = []; // Array of {value, nama, tipe}

function toggleGuruSelect(el) {
    const cb = el.querySelector('.guru-checkbox');
    cb.checked = !cb.checked;
    el.classList.toggle('selected', cb.checked);
    updateGuruSelectedCount();
}

function updateGuruSelectedCount() {
    const checked = document.querySelectorAll('#guruSelectList .guru-checkbox:checked');
    const preview = document.getElementById('selectedGuruPreview');
    const count = document.getElementById('selectedGuruCount');
    count.textContent = checked.length;
    preview.style.display = checked.length > 0 ? 'block' : 'none';
}

function filterGuruList() {
    const search = document.getElementById('searchGuruInput').value.toLowerCase();
    document.querySelectorAll('.tt-guru-select-item').forEach(item => {
        const nama = item.getAttribute('data-nama');
        item.style.display = nama.includes(search) ? 'flex' : 'none';
    });
}

// Sync checkboxes with click
document.querySelectorAll('#guruSelectList .guru-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('.tt-guru-select-item').classList.toggle('selected', this.checked);
        updateGuruSelectedCount();
    });
});

function confirmGuruSelection() {
    const checked = document.querySelectorAll('#guruSelectList .guru-checkbox:checked');
    selectedGuru = [];
    checked.forEach(cb => {
        const item = cb.closest('.tt-guru-select-item');
        selectedGuru.push({
            value: cb.value,
            nama: item.getAttribute('data-display'),
            tipe: item.getAttribute('data-tipe')
        });
    });
    renderGuruChips();
    closeGuruPickerModal();
}

function renderGuruChips() {
    const container = document.getElementById('guruChipsContainer');
    const empty = document.getElementById('guruChipsEmpty');

    if (selectedGuru.length === 0) {
        if (empty) empty.style.display = 'block';
        // Remove all chips
        container.querySelectorAll('.tt-guru-chip').forEach(c => c.remove());
        return;
    }

    if (empty) empty.style.display = 'none';
    // Clear existing chips
    container.querySelectorAll('.tt-guru-chip').forEach(c => c.remove());

    selectedGuru.forEach((guru, i) => {
        const chipClass = guru.tipe === 'BK' ? 'chip-bk' : 'chip-guru';
        const chip = document.createElement('div');
        chip.className = 'tt-guru-chip ' + chipClass;
        chip.innerHTML = escapeHtml(guru.nama) + ' <span style="opacity:0.6;font-size:10px;">(' + guru.tipe + ')</span>' +
            ' <button class="tt-guru-chip-remove" onclick="removeGuruChip(' + i + ')" title="Hapus">&times;</button>';
        container.appendChild(chip);
    });
}

function removeGuruChip(index) {
    const removed = selectedGuru.splice(index, 1)[0];
    // Uncheck in picker modal
    const cb = document.querySelector('#guruSelectList .guru-checkbox[value="' + removed.value + '"]');
    if (cb) {
        cb.checked = false;
        cb.closest('.tt-guru-select-item').classList.remove('selected');
    }
    updateGuruSelectedCount();
    renderGuruChips();
}

/* ===== SIMPAN TUGAS ===== */
function simpanTugas() {
    const jenisId = document.getElementById('selectJenisTugas').value;
    if (!jenisId) { showToast('Pilih jenis tugas terlebih dahulu!', 'error'); return; }

    if (selectedGuru.length === 0) { showToast('Pilih minimal 1 guru!', 'error'); return; }

    const guruIds = selectedGuru.map(g => g.value);
    const keterangan = document.getElementById('inputKeteranganTugas').value.trim();

    const btn = document.getElementById('btnSimpanTugas');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route("admin.tugas-tambahan.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ jenis_tugas_id: jenisId, guru_ids: guruIds, keterangan: keterangan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else { showToast(data.message, 'error'); }
    })
    .catch(() => showToast('Terjadi kesalahan!', 'error'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Tugas Tambahan'; });
}

/* ===== HAPUS TUGAS ===== */
function hapusTugas(id) {
    if (!confirm('Yakin ingin menghapus tugas tambahan ini?')) return;
    fetch('{{ route("admin.tugas-tambahan.delete") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const row = document.querySelector('tr[data-tugas-id="' + id + '"]');
            if (row) { row.style.transition = 'opacity 0.3s'; row.style.opacity = '0'; setTimeout(() => { row.remove(); updateTugasCount(); }, 300); }
        } else { showToast(data.message, 'error'); }
    })
    .catch(() => showToast('Terjadi kesalahan!', 'error'));
}

/* ===== EDIT TUGAS ===== */
function openEditTugas(id, jenisId, keterangan) {
    document.getElementById('editTugasId').value = id;
    document.getElementById('editSelectJenisTugas').value = jenisId;
    document.getElementById('editKeteranganTugas').value = keterangan;
    document.getElementById('editTugasModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditTugas() {
    document.getElementById('editTugasModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('editTugasModal').addEventListener('click', function(e) { if (e.target === this) closeEditTugas(); });

function updateTugas() {
    const id = document.getElementById('editTugasId').value;
    const jenisId = document.getElementById('editSelectJenisTugas').value;
    const keterangan = document.getElementById('editKeteranganTugas').value.trim();

    if (!jenisId) { showToast('Pilih jenis tugas!', 'error'); return; }

    const btn = document.getElementById('btnUpdateTugas');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route("admin.tugas-tambahan.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ id: parseInt(id), jenis_tugas_id: parseInt(jenisId), keterangan: keterangan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeEditTugas();
            setTimeout(() => location.reload(), 800);
        } else { showToast(data.message, 'error'); }
    })
    .catch(() => showToast('Terjadi kesalahan!', 'error'))
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan'; });
}

function updateTugasCount() {
    const rows = document.querySelectorAll('.tt-tugas-table tbody tr');
    const countEl = document.querySelector('.tt-section-count');
    if (countEl) countEl.textContent = rows.length + ' Data';
    if (rows.length === 0) location.reload();
}

/* ===== HELPERS ===== */
function escapeHtml(text) { const d = document.createElement('div'); d.textContent = text; return d.innerHTML; }
function escapeAttr(text) { return String(text).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

document.getElementById('inputNamaTugas').addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); simpanJenis(); } });
</script>
@endsection
