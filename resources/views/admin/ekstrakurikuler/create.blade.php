@extends('layouts.app')

@section('title', 'Tambah Ekstrakurikuler | SISMIK')

@push('styles')
<style>
    .ekstra-form { max-width: 900px; margin: 0 auto; }
    
    .form-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .form-card h3 {
        margin: 0 0 20px 0;
        color: #1f2937;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; font-size: 14px; }
    
    .form-control, .form-select {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    
    .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
    .form-col { flex: 1; }
    
    .form-info {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #0369a1;
        padding: 12px;
        border-radius: 6px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }
    
    .anggota-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .anggota-controls .badge {
        background: #3b82f6;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    
    .anggota-list {
        min-height: 100px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .empty-state-small {
        text-align: center;
        padding: 30px;
        color: #9ca3af;
    }
    
    .empty-state-small i { font-size: 32px; margin-bottom: 10px; opacity: 0.5; }
    
    .anggota-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid #3b82f6;
    }
    
    .anggota-info h4 { margin: 0; font-size: 14px; color: #1f2937; font-weight: 600; }
    .anggota-info p { margin: 5px 0 0 0; font-size: 12px; color: #6b7280; }
    
    .anggota-remove {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 16px;
        padding: 5px 8px;
        border-radius: 4px;
    }
    
    .form-actions {
        display: flex;
        justify-content: space-between;
        padding: 20px 0;
        border-top: 1px solid #e5e7eb;
        margin-top: 20px;
    }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .modal-content-wide {
        background: white;
        width: 90%;
        max-width: 900px;
        max-height: 85vh;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    
    .modal-header h3 {
        margin: 0; color: #1f2937; font-size: 18px;
        display: flex; align-items: center; gap: 10px;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
    }
    
    .modal-body { padding: 20px; max-height: 60vh; overflow-y: auto; }
    
    .search-box { position: relative; margin-bottom: 15px; }
    .search-input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }
    
    .filter-options { display: flex; gap: 10px; margin-bottom: 15px; }
    .filter-select {
        flex: 1;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }
    
    .siswa-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
        max-height: 300px;
        overflow-y: auto;
        padding: 10px 0;
    }
    
    .siswa-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .siswa-item:hover { border-color: #3b82f6; background: #f0f9ff; }
    .siswa-item.selected { border-color: #10b981; background: #f0fdf4; }
    .siswa-item h4 { margin: 0 0 5px 0; font-size: 14px; color: #1f2937; }
    .siswa-item p { margin: 0; font-size: 12px; color: #6b7280; }
    
    .selected-preview {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        padding: 12px 16px;
        border-radius: 6px;
        margin-top: 15px;
        font-size: 14px;
        color: #0369a1;
    }
    
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    
    .loading { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; }
    .spinner {
        width: 40px; height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 10px;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    .content-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header-content { display: flex; align-items: center; gap: 1.5rem; }
    .header-icon {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .header-text h1 { margin: 0 0 0.5rem 0; font-size: 1.75rem; font-weight: 700; }
    .header-subtitle { font-size: 0.875rem; opacity: 0.9; }
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
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="header-text">
                    <h1>Tambah Ekstrakurikuler Baru</h1>
                    <div class="header-subtitle">
                        <i class="fas fa-calendar-check"></i> Periode Aktif: <strong>{{ $tahunAktif }} - {{ $semesterAktif }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.ekstrakurikuler.store') }}" method="POST" class="ekstra-form" id="ekstraForm">
            @csrf
            
            <div class="form-card">
                <h3><i class="fas fa-info-circle"></i> Informasi Ekstrakurikuler</h3>
                
                <div class="form-group">
                    <label for="nama_ekstrakurikuler">Nama Ekstrakurikuler *</label>
                    <input type="text" id="nama_ekstrakurikuler" name="nama_ekstrakurikuler" 
                           class="form-control" required placeholder="Contoh: Pramuka, Basket, OSIS"
                           value="{{ old('nama_ekstrakurikuler') }}">
                    @error('nama_ekstrakurikuler')
                        <small style="color: #dc2626;">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Pembina</label>
                    <div class="form-row">
                        <div class="form-col">
                            <label for="pembina_1">Pembina 1</label>
                            <select id="pembina_1" name="pembina_1" class="form-select">
                                <option value="">- Pilih Pembina -</option>
                                @foreach($pembinaList as $pembina)
                                    <option value="{{ $pembina }}" {{ old('pembina_1') == $pembina ? 'selected' : '' }}>
                                        {{ $pembina }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-col">
                            <label for="pembina_2">Pembina 2</label>
                            <select id="pembina_2" name="pembina_2" class="form-select">
                                <option value="">- Pilih Pembina -</option>
                                @foreach($pembinaList as $pembina)
                                    <option value="{{ $pembina }}" {{ old('pembina_2') == $pembina ? 'selected' : '' }}>
                                        {{ $pembina }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-col">
                            <label for="pembina_3">Pembina 3</label>
                            <select id="pembina_3" name="pembina_3" class="form-select">
                                <option value="">- Pilih Pembina -</option>
                                @foreach($pembinaList as $pembina)
                                    <option value="{{ $pembina }}" {{ old('pembina_3') == $pembina ? 'selected' : '' }}>
                                        {{ $pembina }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                              placeholder="Deskripsi singkat tentang ekstrakurikuler">{{ old('deskripsi') }}</textarea>
                </div>
                
                <div class="form-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Ekstrakurikuler akan otomatis terdaftar pada periode aktif saat ini</span>
                </div>
            </div>
            
            <!-- Anggota Section -->
            <div class="form-card">
                <h3><i class="fas fa-users"></i> Anggota Ekstrakurikuler</h3>
                
                <div class="anggota-controls">
                    <button type="button" id="btnTambahAnggota" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Tambah Anggota
                    </button>
                    <span id="anggotaCount" class="badge">0 Anggota</span>
                </div>
                
                <div id="anggotaList" class="anggota-list">
                    <div class="empty-state-small">
                        <i class="fas fa-users"></i>
                        <p>Belum ada anggota yang ditambahkan</p>
                        <p style="font-size: 12px; margin-top: 5px;">Klik "Tambah Anggota" untuk menambahkan anggota</p>
                    </div>
                </div>
                
                <div id="hiddenAnggotaInputs"></div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Ekstrakurikuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Anggota -->
<div class="modal-overlay" id="modalTambahAnggota">
    <div class="modal-content-wide">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Tambah Anggota</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        
        <div class="modal-body">
            <div class="search-box">
                <input type="text" id="searchSiswa" placeholder="Cari siswa berdasarkan nama, NIS, atau NISN..." class="search-input">
                <i class="fas fa-search search-icon"></i>
            </div>
            
            <div class="filter-options">
                <select id="filterRombel" class="filter-select">
                    <option value="">Semua Rombel</option>
                    @foreach($rombelList as $rombel)
                        <option value="{{ $rombel }}">{{ $rombel }}</option>
                    @endforeach
                </select>
                <select id="filterAngkatan" class="filter-select">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatanList as $angkatan)
                        <option value="{{ $angkatan }}">{{ $angkatan }}</option>
                    @endforeach
                </select>
            </div>
            
            <div id="loadingSiswa" class="loading" style="display: none;">
                <div class="spinner"></div>
                <span>Memuat data siswa...</span>
            </div>
            
            <div id="siswaList" class="siswa-list"></div>
            
            <div class="selected-preview">
                <strong>Terpilih: </strong><span id="selectedCount">0 siswa</span>
            </div>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            <button type="button" id="saveAnggota" class="btn btn-success">Tambah ke Anggota</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalTambahAnggota');
    const btnTambah = document.getElementById('btnTambahAnggota');
    const searchInput = document.getElementById('searchSiswa');
    const filterRombel = document.getElementById('filterRombel');
    const filterAngkatan = document.getElementById('filterAngkatan');
    
    let selectedSiswa = [];
    let siswaData = {};
    
    btnTambah.addEventListener('click', function() {
        modal.style.display = 'flex';
        loadSiswaData();
    });
    
    window.closeModal = function() {
        modal.style.display = 'none';
    };
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }
    
    searchInput.addEventListener('input', debounce(loadSiswaData, 300));
    filterRombel.addEventListener('change', loadSiswaData);
    filterAngkatan.addEventListener('change', loadSiswaData);
    
    function loadSiswaData() {
        const loading = document.getElementById('loadingSiswa');
        const siswaList = document.getElementById('siswaList');
        
        loading.style.display = 'flex';
        siswaList.innerHTML = '';
        
        const formData = new FormData();
        formData.append('search', searchInput.value);
        formData.append('rombel', filterRombel.value);
        formData.append('angkatan', filterAngkatan.value);
        formData.append('tahun_aktif', '{{ $tahunAktif }}');
        formData.append('semester_aktif', '{{ $semesterAktif }}');
        
        fetch('{{ route("admin.ekstrakurikuler.get-siswa") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(s => {
                    siswaData[s.id] = s;
                });
                
                siswaList.innerHTML = data.data.map(s => `
                    <div class="siswa-item ${selectedSiswa.includes(s.id.toString()) ? 'selected' : ''}" onclick="toggleSiswa('${s.id}')">
                        <h4>${s.nama}</h4>
                        <p>${s.nis} | ${s.rombel_aktif || '-'}</p>
                        ${s.angkatan ? `<p style="font-size:11px;color:#999;">Angkatan ${s.angkatan}</p>` : ''}
                    </div>
                `).join('');
            } else {
                siswaList.innerHTML = '<div style="text-align:center;padding:40px;color:#6b7280;grid-column:1/-1;"><i class="fas fa-users" style="font-size:48px;margin-bottom:15px;opacity:0.5;"></i><p>Tidak ada data siswa ditemukan</p></div>';
            }
        })
        .catch(err => {
            loading.style.display = 'none';
            console.error(err);
        });
    }
    
    window.toggleSiswa = function(id) {
        id = id.toString();
        const idx = selectedSiswa.indexOf(id);
        if (idx > -1) {
            selectedSiswa.splice(idx, 1);
        } else {
            selectedSiswa.push(id);
        }
        
        document.querySelectorAll('.siswa-item').forEach(item => {
            const itemId = item.getAttribute('onclick').match(/'(\d+)'/)[1];
            item.classList.toggle('selected', selectedSiswa.includes(itemId));
        });
        
        document.getElementById('selectedCount').textContent = selectedSiswa.length + ' siswa';
    };
    
    document.getElementById('saveAnggota').addEventListener('click', function() {
        displayAnggotaList();
        closeModal();
    });
    
    function displayAnggotaList() {
        const anggotaList = document.getElementById('anggotaList');
        const hiddenInputs = document.getElementById('hiddenAnggotaInputs');
        const anggotaCount = document.getElementById('anggotaCount');
        
        anggotaCount.textContent = selectedSiswa.length + ' Anggota';
        hiddenInputs.innerHTML = selectedSiswa.map(id => 
            `<input type="hidden" name="anggota_ids[]" value="${id}">`
        ).join('');
        
        if (selectedSiswa.length > 0) {
            anggotaList.innerHTML = selectedSiswa.map(id => {
                const s = siswaData[id];
                if (!s) return '';
                return `
                    <div class="anggota-item">
                        <div class="anggota-info">
                            <h4>${s.nama}</h4>
                            <p>${s.nis} | ${s.rombel_aktif || '-'}</p>
                        </div>
                        <button type="button" onclick="removeAnggota('${id}')" class="anggota-remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }).join('');
        } else {
            anggotaList.innerHTML = `
                <div class="empty-state-small">
                    <i class="fas fa-users"></i>
                    <p>Belum ada anggota yang ditambahkan</p>
                </div>
            `;
        }
    }
    
    window.removeAnggota = function(id) {
        selectedSiswa = selectedSiswa.filter(x => x !== id.toString());
        displayAnggotaList();
    };
});
</script>
@endpush
