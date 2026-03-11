@extends('layouts.app')

@section('title', 'Nilai Asesmen Sekolah')

@push('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: #1e293b;
}
.page-header .btn-group-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-action {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    text-decoration: none;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
}
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.btn-download-format {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
}
.btn-import-data {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white !important;
}
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.table-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}
.table thead th {
    background: #2E86AB;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    border: none;
    padding: 12px 10px;
    white-space: nowrap;
    text-align: center;
}
.table tbody td {
    padding: 10px;
    font-size: 0.85rem;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}
.table tbody tr:hover {
    background-color: #f8fafc;
}
.badge-asesmen {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-sumatif {
    background: #dbeafe;
    color: #1e40af;
}
.badge-formatif {
    background: #fce7f3;
    color: #9d174d;
}
.badge-default {
    background: #f3f4f6;
    color: #374151;
}
.nilai-cell {
    font-weight: 700;
    text-align: center;
}
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    display: block;
}
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.pagination-info {
    font-size: 0.85rem;
    color: #64748b;
}
.pagination-info strong {
    color: #1e293b;
}
.custom-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.custom-pagination .page-item .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #475569;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}
.custom-pagination .page-item .page-link:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #1e293b;
}
.custom-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #2E86AB, #1b6a8a);
    border-color: #2E86AB;
    color: white;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(46, 134, 171, 0.3);
}
.custom-pagination .page-item.disabled .page-link {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #cbd5e1;
    cursor: not-allowed;
}
.custom-pagination .page-item .page-link i {
    font-size: 0.75rem;
}
.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    color: #475569;
    font-weight: 500;
}
.btn-delete {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s;
}
.btn-delete:hover {
    background: #fef2f2;
}
.cb-cell {
    width: 36px;
    text-align: center;
}
.cb-cell input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #2E86AB;
}
.bulk-actions {
    display: none;
    align-items: center;
    gap: 10px;
}
.bulk-actions.show {
    display: flex;
}
.btn-delete-selected {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-delete-selected:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(239, 68, 68, 0.3);
}
.selected-count {
    background: white;
    color: #ef4444;
    border-radius: 50%;
    min-width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div class="container-fluid px-4">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-clipboard-list text-primary"></i> Nilai Asesmen Sekolah</h1>
                    <p class="text-muted mb-0">Data nilai asesmen sekolah seluruh siswa</p>
                </div>
                <div class="btn-group-actions">
                    <a href="{{ route('admin.nilai-asesmen.download') }}" class="btn-action btn-download-format">
                        <i class="fas fa-file-excel"></i> Download Format
                    </a>
                    <a href="{{ route('admin.nilai-asesmen.import') }}" class="btn-action btn-import-data">
                        <i class="fas fa-file-upload"></i> Import Data
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="filter-card">
                <form method="GET" action="{{ route('admin.nilai-asesmen.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Tahun Pelajaran</label>
                            <select class="form-select form-select-sm" name="tahun_pelajaran">
                                <option value="">Semua</option>
                                @foreach($tahunList as $tahun)
                                    <option value="{{ $tahun }}" {{ request('tahun_pelajaran') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Semester</label>
                            <select class="form-select form-select-sm" name="semester">
                                <option value="">Semua</option>
                                @foreach($semesterList as $sem)
                                    <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>{{ $sem }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Rombel</label>
                            <select class="form-select form-select-sm" name="nama_rombel">
                                <option value="">Semua</option>
                                @foreach($rombelList as $rombel)
                                    <option value="{{ $rombel }}" {{ request('nama_rombel') == $rombel ? 'selected' : '' }}>{{ $rombel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Mata Pelajaran</label>
                            <select class="form-select form-select-sm" name="mata_pelajaran">
                                <option value="">Semua</option>
                                @foreach($mapelList as $mapel)
                                    <option value="{{ $mapel }}" {{ request('mata_pelajaran') == $mapel ? 'selected' : '' }}>{{ $mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Jenis Asesmen</label>
                            <select class="form-select form-select-sm" name="jenis_asesmen">
                                <option value="">Semua</option>
                                @foreach($jenisAsesmenList as $jenis)
                                    <option value="{{ $jenis }}" {{ request('jenis_asesmen') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Cari Siswa</label>
                            <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Nama / NISN...">
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.nilai-asesmen.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-undo"></i> Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="stat-badge">
                        <i class="fas fa-database"></i> Total: {{ $data->total() }} data
                    </span>
                    <div class="bulk-actions" id="bulkActions">
                        <button type="button" class="btn-delete-selected" onclick="hapusTerpilih()">
                            <i class="fas fa-trash-alt"></i> Hapus Terpilih
                            <span class="selected-count" id="selectedCount">0</span>
                        </button>
                    </div>
                </div>

                @if($data->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="cb-cell"><input type="checkbox" id="selectAll" title="Pilih Semua"></th>
                                <th style="width:40px">No</th>
                                <th>Jenis Asesmen</th>
                                <th>Semester</th>
                                <th>Tahun Pelajaran</th>
                                <th>Rombel</th>
                                <th>Mata Pelajaran</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th style="width:70px">Nilai</th>
                                <th style="width:50px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $item)
                            <tr id="row-{{ $item->id }}">
                                <td class="cb-cell"><input type="checkbox" class="row-checkbox" value="{{ $item->id }}"></td>
                                <td class="text-center">{{ $data->firstItem() + $index }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-default';
                                        $lower = strtolower($item->jenis_asesmen);
                                        if (str_contains($lower, 'sumatif')) $badgeClass = 'badge-sumatif';
                                        elseif (str_contains($lower, 'formatif')) $badgeClass = 'badge-formatif';
                                    @endphp
                                    <span class="badge-asesmen {{ $badgeClass }}">{{ $item->jenis_asesmen }}</span>
                                </td>
                                <td>{{ $item->semester }}</td>
                                <td>{{ $item->tahun_pelajaran }}</td>
                                <td><strong>{{ $item->nama_rombel }}</strong></td>
                                <td>{{ $item->mata_pelajaran }}</td>
                                <td>{{ $item->nama_siswa }}</td>
                                <td><code>{{ $item->nisn }}</code></td>
                                <td class="nilai-cell">{{ $item->nilai !== null ? number_format($item->nilai, 2) : '-' }}</td>
                                <td class="text-center">
                                    <button class="btn-delete" onclick="hapusData({{ $item->id }})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($data->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Menampilkan <strong>{{ $data->firstItem() }}–{{ $data->lastItem() }}</strong> dari <strong>{{ $data->total() }}</strong> data
                    </div>
                    <ul class="custom-pagination">
                        {{-- Previous --}}
                        <li class="page-item {{ $data->onFirstPage() ? 'disabled' : '' }}">
                            @if($data->onFirstPage())
                                <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a class="page-link" href="{{ $data->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
                            @endif
                        </li>

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $data->currentPage();
                            $lastPage = $data->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            if ($currentPage <= 3) $end = min($lastPage, 5);
                            if ($currentPage >= $lastPage - 2) $start = max(1, $lastPage - 4);
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $data->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                @if($i == $currentPage)
                                    <span class="page-link">{{ $i }}</span>
                                @else
                                    <a class="page-link" href="{{ $data->url($i) }}">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $data->url($lastPage) }}">{{ $lastPage }}</a>
                            </li>
                        @endif

                        {{-- Next --}}
                        <li class="page-item {{ $data->hasMorePages() ? '' : 'disabled' }}">
                            @if($data->hasMorePages())
                                <a class="page-link" href="{{ $data->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </li>
                    </ul>
                </div>
                @endif
                @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>Belum ada data</h5>
                    <p>Data nilai asesmen sekolah belum tersedia. Gunakan fitur <strong>Import Data</strong> untuk menambahkan data.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    let debounceTimer;

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(function(select) {
        select.addEventListener('change', function() {
            form.submit();
        });
    });

    // Auto-submit on search input with debounce
    const searchInput = form.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                form.submit();
            }, 500);
        });
    }

    // Select All checkbox
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox change
    document.querySelectorAll('.row-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            const allCbs = document.querySelectorAll('.row-checkbox');
            const checkedCbs = document.querySelectorAll('.row-checkbox:checked');
            if (selectAll) selectAll.checked = allCbs.length === checkedCbs.length;
            updateBulkActions();
        });
    });
});

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    if (checkedCount > 0) {
        bulkActions.classList.add('show');
        selectedCount.textContent = checkedCount;
    } else {
        bulkActions.classList.remove('show');
    }
}

function hapusTerpilih() {
    const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) return;
    if (!confirm('Yakin ingin menghapus ' + ids.length + ' data terpilih?')) return;

    fetch('{{ route("admin.nilai-asesmen.destroy-bulk") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            ids.forEach(id => {
                const row = document.getElementById('row-' + id);
                if (row) row.remove();
            });
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        } else {
            alert(data.message || 'Gagal menghapus data');
        }
    })
    .catch(err => alert('Error: ' + err.message));
}

function hapusData(id) {
    if (!confirm('Yakin ingin menghapus data ini?')) return;

    fetch(`{{ url('admin/nilai-asesmen') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('row-' + id).remove();
            updateBulkActions();
        } else {
            alert(data.message || 'Gagal menghapus data');
        }
    })
    .catch(err => alert('Error: ' + err.message));
}
</script>
@endpush
