@extends('layouts.app')

@section('title', 'Kartu Login Ujian | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
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
    
    .import-card {
        background: var(--white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        max-width: 700px;
        margin-bottom: 30px;
    }
    
    .upload-zone {
        border: 3px dashed var(--gray-300);
        border-radius: 16px;
        padding: 50px 30px;
        text-align: center;
        background: var(--gray-50);
        transition: all 0.3s;
        cursor: pointer;
        margin-bottom: 30px;
    }
    .upload-zone:hover {
        border-color: #7C3AED;
        background: rgba(124, 58, 237, 0.05);
    }
    .upload-zone.dragover {
        border-color: #7C3AED;
        background: rgba(124, 58, 237, 0.1);
    }
    .upload-zone i {
        font-size: 48px;
        color: #7C3AED;
        margin-bottom: 16px;
    }
    .upload-zone h3 {
        font-size: 18px;
        margin-bottom: 8px;
        color: var(--gray-700);
    }
    .upload-zone p {
        color: var(--gray-500);
        font-size: 14px;
    }
    
    .file-info {
        background: #ede9fe;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
    }
    .file-info.show { display: flex; align-items: center; gap: 12px; }
    .file-info i { font-size: 24px; color: #7c3aed; }
    .file-info .file-name { font-weight: 600; color: #5b21b6; }
    .file-info .file-size { font-size: 12px; color: #6b7280; }
    
    .template-box {
        background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
        padding: 20px;
        border-radius: 12px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .template-box i { font-size: 32px; opacity: 0.8; }
    .template-box .template-text h4 { margin: 0 0 4px; }
    .template-box .template-text p { margin: 0; opacity: 0.9; font-size: 13px; }
    
    .btn-group-action {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
    }
    
    .info-box {
        background: var(--gray-100);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    .info-box h4 {
        font-size: 15px;
        margin-bottom: 12px;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-box h4 i { color: #7C3AED; }
    .info-box ul {
        margin: 0;
        padding-left: 20px;
        color: var(--gray-600);
        font-size: 13px;
    }
    .info-box li { margin-bottom: 6px; }

    /* Data table */
    .data-card {
        background: var(--white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .data-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .data-card-header h2 {
        font-size: 20px;
        color: var(--gray-700);
        margin: 0;
    }
    .data-card-header .badge-count {
        background: #ede9fe;
        color: #7c3aed;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .table-responsive {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid var(--gray-200);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table th {
        background: #f5f3ff;
        color: #5b21b6;
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        white-space: nowrap;
        border-bottom: 2px solid #e5e7eb;
    }
    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        color: var(--gray-700);
    }
    .data-table tbody tr:hover {
        background: #faf5ff;
    }
    .data-table .password-cell {
        font-family: 'Courier New', monospace;
        background: #f9fafb;
        letter-spacing: 0.5px;
    }
    
    .btn-delete {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background: #fef2f2;
    }
    
    .btn-danger-outline {
        background: none;
        border: 2px solid #ef4444;
        color: #ef4444;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }
    .btn-danger-outline:hover {
        background: #ef4444;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray-400);
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    .empty-state p {
        font-size: 14px;
    }

    .search-filter {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .search-filter input {
        flex: 1;
        min-width: 200px;
        padding: 10px 16px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.2s;
    }
    .search-filter input:focus {
        outline: none;
        border-color: #7c3aed;
    }

    @media (max-width: 768px) {
        .content-header {
            padding: 20px;
            gap: 15px;
        }
        .header-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }
        .header-text h1 { font-size: 20px; }
        .import-card, .data-card { padding: 20px; }
        .data-card-header { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="header-text">
                <h1>Kartu Login Ujian</h1>
                <p>Import & kelola data login ujian D-Smart, Bimasoft, dan Aksi Jihan</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Import Section -->
        <div class="import-card">
            <!-- Template Download -->
            <div class="template-box">
                <div class="template-text">
                    <h4><i class="fas fa-download"></i> Download Template Excel</h4>
                    <p>Gunakan template Excel yang sudah disediakan untuk import data</p>
                </div>
                <a href="{{ route('admin.kartu-login-ujian.template') }}" class="btn btn-light" style="color: #6D28D9;">
                    <i class="fas fa-file-excel"></i> Download
                </a>
            </div>

            <form action="{{ route('admin.kartu-login-ujian.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <!-- Upload Zone -->
                <div class="upload-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Drag & Drop file Excel di sini</h3>
                    <p>atau klik untuk memilih file</p>
                    <p style="margin-top: 10px;"><strong>Format: .xlsx</strong> | Maksimal: 10MB</p>
                </div>
                <input type="file" name="file_excel" id="fileInput" accept=".xlsx,.xls" style="display: none;">

                <!-- File Info -->
                <div class="file-info" id="fileInfo">
                    <i class="fas fa-file-excel"></i>
                    <div>
                        <div class="file-name" id="fileName">-</div>
                        <div class="file-size" id="fileSize">-</div>
                    </div>
                    <button type="button" onclick="removeFile()" style="margin-left: auto; background: none; border: none; color: #ef4444; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Format Kolom Excel</h4>
                    <ul>
                        <li><strong>Kolom 1:</strong> Nama Siswa (wajib)</li>
                        <li><strong>Kolom 2:</strong> Kelas</li>
                        <li><strong>Kolom 3:</strong> NISN / Username (wajib)</li>
                        <li><strong>Kolom 4:</strong> Password D-Smart</li>
                        <li><strong>Kolom 5:</strong> Password Bimasoft</li>
                        <li><strong>Kolom 6:</strong> Password Aksi Jihan</li>
                    </ul>
                </div>

                <div class="info-box" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                    <h4 style="color: #92400e;"><i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i> Catatan Penting</h4>
                    <ul style="color: #92400e;">
                        <li>Data dengan <strong>NISN yang sama</strong> akan di-<strong>update</strong></li>
                        <li>Baris pertama harus berupa header kolom</li>
                        <li>Pastikan NISN tidak ada yang kosong</li>
                    </ul>
                </div>

                <div class="btn-group-action">
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #7C3AED, #6D28D9); color: white;" id="submitBtn" disabled>
                        <i class="fas fa-upload"></i> Import Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table Section -->
        <div class="data-card">
            <div class="data-card-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h2><i class="fas fa-table" style="color: #7c3aed;"></i> Data Kartu Login Ujian</h2>
                    <span class="badge-count">{{ $data->count() }} data</span>
                </div>
                @if($data->count() > 0)
                <form action="{{ route('admin.kartu-login-ujian.destroy-all') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA data kartu login ujian?')">
                    @csrf
                    <button type="submit" class="btn-danger-outline">
                        <i class="fas fa-trash-alt"></i> Hapus Semua
                    </button>
                </form>
                @endif
            </div>

            @if($data->count() > 0)
            <div class="search-filter">
                <input type="text" id="tableSearch" placeholder="🔍 Cari nama siswa..." oninput="filterTable()">
            </div>
            <div class="table-responsive">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>NISN (Username)</th>
                            <th>Password D-Smart</th>
                            <th>Password Bimasoft</th>
                            <th>Password Aksi Jihan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $item)
                        <tr id="row-{{ $item->id }}">
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $item->nama_siswa }}</strong></td>
                            <td>{{ $item->kelas }}</td>
                            <td class="password-cell">{{ $item->nisn }}</td>
                            <td class="password-cell">{{ $item->password_dsmart ?: '-' }}</td>
                            <td class="password-cell">{{ $item->password_bimasoft ?: '-' }}</td>
                            <td class="password-cell">{{ $item->password_aksi_jihan ?: '-' }}</td>
                            <td>
                                <button class="btn-delete" onclick="deleteRecord({{ $item->id }})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Belum ada data kartu login ujian.<br>Import data menggunakan form di atas.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const submitBtn = document.getElementById('submitBtn');

    // Drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showFileInfo(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            showFileInfo(this.files[0]);
        }
    });

    function showFileInfo(file) {
        if (!file.name.endsWith('.xlsx') && !file.name.endsWith('.xls')) {
            alert('Hanya file Excel (.xlsx/.xls) yang diperbolehkan!');
            fileInput.value = '';
            return;
        }

        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
        fileInfo.classList.add('show');
        dropZone.style.display = 'none';
        submitBtn.disabled = false;
    }

    function removeFile() {
        fileInput.value = '';
        fileInfo.classList.remove('show');
        dropZone.style.display = 'block';
        submitBtn.disabled = true;
    }

    // Table search filter
    function filterTable() {
        const query = document.getElementById('tableSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#dataTable tbody tr');
        rows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const kelas = row.cells[2].textContent.toLowerCase();
            row.style.display = (name.includes(query) || kelas.includes(query)) ? '' : 'none';
        });
    }

    // Delete single record via AJAX
    function deleteRecord(id) {
        if (!confirm('Yakin ingin menghapus data ini?')) return;
        
        fetch(`{{ url('admin/kartu-login-ujian') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('row-' + id).remove();
                // Update count badge
                const badge = document.querySelector('.badge-count');
                const remaining = document.querySelectorAll('#dataTable tbody tr').length;
                badge.textContent = remaining + ' data';
                if (remaining === 0) location.reload();
            }
        })
        .catch(() => alert('Gagal menghapus data'));
    }
</script>
@endpush
