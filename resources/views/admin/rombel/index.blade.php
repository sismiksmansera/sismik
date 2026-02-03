@extends('layouts.app')

@section('title', 'Manajemen Rombel | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-content { display: flex; align-items: center; gap: 20px; }
    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }
    .header-text h1 { font-size: 28px; font-weight: 700; margin-bottom: 5px; }
    .header-text p { opacity: 0.9; }
    .header-actions { display: flex; gap: 10px; }
    
    .content-section {
        background: var(--white);
        border-radius: 16px;
        padding: 0;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 2px solid var(--gray-200);
    }
    .section-header h2 {
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-header h2 i { color: var(--primary); }
    
    .table-controls {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }
    .filter-group { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
    .filter-item { display: flex; align-items: center; gap: 8px; }
    .filter-item label { font-weight: 600; font-size: 13px; color: var(--gray-600); }
    .filter-item select {
        padding: 8px 12px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        min-width: 150px;
    }
    .search-box {
        position: relative;
    }
    .search-box input {
        padding: 10px 16px 10px 40px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        width: 280px;
        font-size: 14px;
    }
    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    .modern-table { width: 100%; border-collapse: collapse; }
    .modern-table th {
        background: var(--gray-100);
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .modern-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    .modern-table tbody tr:hover { background: var(--gray-50); }
    .modern-table tbody tr.rombel-aktif {
        background: rgba(16, 185, 129, 0.08);
        border-left: 3px solid var(--primary);
    }
    
    .semester-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(16, 185, 129, 0.15);
        color: var(--primary);
    }
    .tingkat-badge {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
    }
    
    .action-buttons-small { display: flex; gap: 6px; justify-content: center; }
    .btn-action {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
        text-decoration: none;
        color: white;
    }
    .btn-action:hover { transform: translateY(-2px); }
    .btn-warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .btn-success { background: linear-gradient(135deg, #10B981, #059669); }
    .btn-danger { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .btn-info { background: linear-gradient(135deg, #3B82F6, #2563EB); }
    .btn-purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: var(--gray-500);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--gray-300); }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 500px;
        max-height: 85vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { font-size: 18px; font-weight: 600; }
    .modal-close {
        width: 32px; height: 32px;
        border: none;
        background: var(--gray-100);
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
    }
    .modal-body { padding: 24px; }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .pagination-footer {
        padding: 16px 24px;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--gray-200);
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
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="header-text">
                    <h1>Manajemen Rombongan Belajar</h1>
                    <p>Kelola data kelas dan wali kelas</p>
                    @if($tahunFilter || $semesterFilter)
                        <div style="margin-top: 8px;">
                            <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 13px;">
                                <i class="fas fa-filter"></i>
                                {{ $tahunFilter ? "Tahun: $tahunFilter" : '' }}
                                {{ $semesterFilter ? "| Semester: " . ucfirst($semesterFilter) : '' }}
                                @if($tahunFilter == $tahunAktif && $semesterFilter == strtolower($semesterAktif))
                                    <span style="color: #86efac;">(Aktif)</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.rombel.create') }}" class="btn btn-light">
                    <i class="fas fa-plus"></i> Tambah Rombel
                </a>
                <button class="btn btn-light" onclick="openModal('modalSalin')">
                    <i class="fas fa-copy"></i> Salin Rombel
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Content Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list-alt"></i> Daftar Rombongan Belajar</h2>
                <span class="badge" style="background: var(--primary); color: white; padding: 6px 14px; border-radius: 20px;">
                    {{ $rombelList->count() }} Rombel
                </span>
            </div>

            <!-- Table Controls -->
            <div class="table-controls">
                <div class="filter-group">
                    <div class="filter-item">
                        <label>Tahun Pelajaran</label>
                        <select id="filterTahun">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ $tahunFilter == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }} {{ $tahun == $tahunAktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Semester</label>
                        <select id="filterSemester">
                            <option value="">Semua</option>
                            <option value="ganjil" {{ $semesterFilter == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ $semesterFilter == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Tampilkan</label>
                        <select id="rowsPerPage">
                            <option value="10" selected>10 baris</option>
                            <option value="25">25 baris</option>
                            <option value="50">50 baris</option>
                            <option value="100">100 baris</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    @if($tahunFilter != $tahunAktif || $semesterFilter != strtolower($semesterAktif))
                        <a href="{{ route('admin.rombel.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    @endif
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari rombel, wali kelas...">
                </div>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="modern-table" id="rombelTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th class="text-center">Tahun Pelajaran</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Rombel</th>
                            <th class="text-center">Tingkat</th>
                            <th>Wali Kelas</th>
                            <th class="text-center" width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rombelList as $index => $rombel)
                            @php
                                $isAktif = ($rombel->tahun_pelajaran == $tahunAktif && strtolower($rombel->semester) == strtolower($semesterAktif));
                            @endphp
                            <tr class="{{ $isAktif ? 'rombel-aktif' : '' }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <i class="fas fa-calendar-alt" style="color: var(--gray-400);"></i>
                                    {{ $rombel->tahun_pelajaran }}
                                </td>
                                <td class="text-center">
                                    <span class="semester-badge">{{ $rombel->semester }}</span>
                                </td>
                                <td class="text-center" style="font-weight: 700; color: var(--primary);">
                                    {{ $rombel->nama_rombel }}
                                </td>
                                <td class="text-center">
                                    <span class="tingkat-badge">{{ $rombel->tingkat }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-user-tie" style="color: var(--gray-400);"></i>
                                    {{ $rombel->wali_kelas }}
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons-small">
                                        <a href="{{ route('admin.rombel.edit', $rombel->id) }}" class="btn-action btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.rombel.members', $rombel->id) }}" class="btn-action btn-info" title="Anggota">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="{{ route('admin.mapel.index', ['id' => $rombel->id, 'tahun' => $rombel->tahun_pelajaran, 'semester' => $rombel->semester]) }}" class="btn-action btn-success" title="Mata Pelajaran & Jadwal">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="{{ route('admin.prestasi.lihat', ['type' => 'rombel', 'id' => $rombel->id]) }}" class="btn-action" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;" title="Lihat Prestasi">
                                            <i class="fas fa-trophy"></i>
                                        </a>
                                        <button class="btn-action btn-danger" onclick="confirmDelete({{ $rombel->id }})" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <h3>Tidak Ada Data Rombel</h3>
                                        <p>Belum ada rombongan belajar untuk periode ini</p>
                                        <a href="{{ route('admin.rombel.create') }}" class="btn btn-primary" style="margin-top: 16px;">
                                            <i class="fas fa-plus"></i> Tambah Rombel Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            <div class="pagination-controls" style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <div class="pagination-info" style="color: #6b7280; font-size: 14px;">
                    Menampilkan <strong id="showingStart">1</strong> - <strong id="showingEnd">10</strong> dari <strong id="totalRows">{{ $rombelList->count() }}</strong> data
                </div>
                <div class="pagination-buttons" style="display: flex; gap: 8px; align-items: center;">
                    <button class="btn btn-sm btn-secondary" id="btnFirst" onclick="goToPage(1)" title="Halaman Pertama">
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" id="btnPrev" onclick="prevPage()" title="Sebelumnya">
                        <i class="fas fa-angle-left"></i>
                    </button>
                    <span style="padding: 6px 16px; background: var(--primary); color: white; border-radius: 6px; font-weight: 600; font-size: 14px;">
                        Hal <span id="currentPage">1</span>/<span id="totalPages">1</span>
                    </span>
                    <button class="btn btn-sm btn-secondary" id="btnNext" onclick="nextPage()" title="Selanjutnya">
                        <i class="fas fa-angle-right"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" id="btnLast" onclick="goToPage(totalPages)" title="Halaman Terakhir">
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div class="modal-body" style="padding: 40px;">
            <div style="font-size: 48px; color: #F59E0B; margin-bottom: 16px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="margin-bottom: 8px;">Konfirmasi Hapus</h3>
            <p style="color: var(--gray-500); margin-bottom: 24px;">Apakah Anda yakin ingin menghapus data rombel ini?</p>
            <form id="deleteForm" method="POST" style="display: flex; gap: 10px; justify-content: center;">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalDelete')">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Salin Rombel -->
<div class="modal-overlay" id="modalSalin">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-copy" style="color: var(--primary); margin-right: 10px;"></i>Salin Data Rombel</h3>
            <button class="modal-close" onclick="closeModal('modalSalin')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Salin Dari:</label>
                <div style="display: flex; gap: 10px;">
                    <select id="salinTahunAsal" class="form-select" style="flex: 1;">
                        <option value="">Pilih Tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <select id="salinSemesterAsal" class="form-select" style="flex: 1;">
                        <option value="">Pilih Semester</option>
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Salin Ke:</label>
                <div style="display: flex; gap: 10px;">
                    <select id="salinTahunTujuan" class="form-select" style="flex: 1;">
                        <option value="">Pilih Tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $tahunAktif ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <select id="salinSemesterTujuan" class="form-select" style="flex: 1;">
                        <option value="">Pilih Semester</option>
                        <option value="ganjil" {{ strtolower($semesterAktif ?? '') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ strtolower($semesterAktif ?? '') == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="background: var(--gray-100); padding: 12px; border-radius: 8px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" id="salinAnggota" checked>
                    <span>Salin juga data anggota rombel (siswa)</span>
                </label>
            </div>
            <div class="alert alert-info" style="padding: 10px 14px; font-size: 13px;">
                <i class="fas fa-info-circle"></i>
                <span id="infoSalin">Data rombel akan disalin ke periode baru</span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalSalin')">Batal</button>
            <button type="button" class="btn btn-primary" onclick="prosesСalin()">
                <i class="fas fa-copy"></i> Proses Salin
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal Functions
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    // Delete Confirmation
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '/admin/rombel/' + id;
        openModal('modalDelete');
    }

    // Filter Change
    function applyFilter() {
        const tahun = document.getElementById('filterTahun').value;
        const semester = document.getElementById('filterSemester').value;
        let url = new URL(window.location.href);
        if (tahun) url.searchParams.set('tahun', tahun);
        else url.searchParams.delete('tahun');
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        window.location.href = url.toString();
    }

    document.getElementById('filterTahun').addEventListener('change', applyFilter);
    document.getElementById('filterSemester').addEventListener('change', applyFilter);

    // Search
    document.getElementById('searchInput').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        allRows.forEach(row => {
            // Mark rows that match search
            const matches = row.textContent.toLowerCase().includes(term);
            row.setAttribute('data-searchable', matches ? 'true' : 'false');
        });
        // Reset pagination after search
        currentPage = 1;
        applyPagination();
    });

    // Pagination Variables
    let currentPage = 1;
    let rowsPerPage = 10;
    let totalPages = 1;
    const allRows = Array.from(document.querySelectorAll('#rombelTable tbody tr'));
    
    // Initialize all rows as searchable
    allRows.forEach(row => row.setAttribute('data-searchable', 'true'));

    // Get rows that match current search
    function getSearchableRows() {
        return allRows.filter(row => row.getAttribute('data-searchable') === 'true');
    }

    // Apply Pagination
    function applyPagination() {
        const searchableRows = getSearchableRows();
        const total = searchableRows.length;
        
        // Hide all rows first
        allRows.forEach(row => row.style.display = 'none');
        
        if (rowsPerPage === 'all') {
            totalPages = 1;
            searchableRows.forEach(row => row.style.display = '');
        } else {
            totalPages = Math.ceil(total / rowsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            
            searchableRows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });
        }
        
        updatePaginationInfo(total);
    }

    // Update Pagination Info Display
    function updatePaginationInfo(total) {
        const start = total === 0 ? 0 : ((currentPage - 1) * (rowsPerPage === 'all' ? total : rowsPerPage)) + 1;
        const end = rowsPerPage === 'all' ? total : Math.min(currentPage * rowsPerPage, total);
        
        document.getElementById('showingStart').textContent = start;
        document.getElementById('showingEnd').textContent = end;
        document.getElementById('totalRows').textContent = total;
        document.getElementById('currentPage').textContent = currentPage;
        document.getElementById('totalPages').textContent = totalPages;
        
        // Enable/disable buttons
        document.getElementById('btnFirst').disabled = currentPage <= 1;
        document.getElementById('btnPrev').disabled = currentPage <= 1;
        document.getElementById('btnNext').disabled = currentPage >= totalPages;
        document.getElementById('btnLast').disabled = currentPage >= totalPages;
    }

    // Navigation Functions
    function goToPage(page) {
        currentPage = Math.max(1, Math.min(page, totalPages));
        applyPagination();
    }
    function prevPage() { goToPage(currentPage - 1); }
    function nextPage() { goToPage(currentPage + 1); }

    // Rows Per Page Change
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        rowsPerPage = this.value === 'all' ? 'all' : parseInt(this.value);
        currentPage = 1;
        applyPagination();
    });

    // Initialize pagination on page load
    applyPagination();

    // Copy Rombel
    function prosesСalin() {
        const tAsal = document.getElementById('salinTahunAsal').value;
        const sAsal = document.getElementById('salinSemesterAsal').value;
        const tTujuan = document.getElementById('salinTahunTujuan').value;
        const sTujuan = document.getElementById('salinSemesterTujuan').value;
        const salinAnggota = document.getElementById('salinAnggota').checked;

        if (!tAsal || !sAsal || !tTujuan || !sTujuan) {
            alert('Harap lengkapi semua field!');
            return;
        }
        if (tAsal === tTujuan && sAsal === sTujuan) {
            alert('Periode asal dan tujuan tidak boleh sama!');
            return;
        }

        fetch('{{ route("admin.rombel.copy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tahun_asal: tAsal,
                semester_asal: sAsal,
                tahun_tujuan: tTujuan,
                semester_tujuan: sTujuan,
                salin_anggota: salinAnggota ? '1' : '0'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeModal('modalSalin');
                location.reload();
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }

    // Update info salin
    ['salinTahunAsal', 'salinSemesterAsal', 'salinTahunTujuan', 'salinSemesterTujuan'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => {
            const tA = document.getElementById('salinTahunAsal').value;
            const sA = document.getElementById('salinSemesterAsal').value;
            const tT = document.getElementById('salinTahunTujuan').value;
            const sT = document.getElementById('salinSemesterTujuan').value;
            if (tA && sA && tT && sT) {
                document.getElementById('infoSalin').textContent = 
                    `Menyalin dari ${tA} ${sA} ke ${tT} ${sT}`;
            }
        });
    });

    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
    });
</script>
@endpush
