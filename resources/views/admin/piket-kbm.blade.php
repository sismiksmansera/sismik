@extends('layouts.app')

@section('title', 'Piket KBM | SISMIK')

@push('styles')
<style>
    /* Page Header */
    .page-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #3b82f6;
        margin-bottom: 24px;
    }
    .page-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }
    .page-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .page-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Day Blocks Grid */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

    /* Day Block */
    .day-block {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .day-block:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .day-block-header {
        padding: 16px 20px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .day-block-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .day-block-header .count-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .day-block-body {
        padding: 16px 20px;
    }

    /* Guru Piket Item */
    .piket-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 8px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .piket-item:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }
    .piket-item:last-child { margin-bottom: 0; }

    .piket-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .piket-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        color: white;
    }
    .piket-avatar.guru { background: linear-gradient(135deg, #10b981, #059669); }
    .piket-avatar.guru_bk { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .piket-name {
        font-size: 13.5px;
        font-weight: 500;
        color: #1f2937;
    }
    .piket-nip {
        font-size: 11px;
        color: #9ca3af;
    }
    .piket-role-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .piket-role-badge.guru { background: #d1fae5; color: #065f46; }
    .piket-role-badge.guru_bk { background: #ede9fe; color: #5b21b6; }

    .piket-delete {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 14px;
        padding: 6px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .piket-delete:hover {
        background: #fef2f2;
    }

    /* Empty State */
    .empty-piket {
        text-align: center;
        padding: 20px;
        color: #9ca3af;
        font-size: 13px;
    }
    .empty-piket i { font-size: 28px; margin-bottom: 8px; display: block; color: #d1d5db; }

    /* Add Button */
    .add-piket-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 10px;
        margin-top: 12px;
        background: #eff6ff;
        border: 2px dashed #93c5fd;
        border-radius: 10px;
        color: #3b82f6;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .add-piket-btn:hover {
        background: #dbeafe;
        border-color: #3b82f6;
    }

    /* Add Form (dropdown) */
    .add-piket-form {
        display: none;
        margin-top: 12px;
        padding: 14px;
        background: #f0f9ff;
        border-radius: 10px;
        border: 1px solid #bfdbfe;
    }
    .add-piket-form.show { display: block; }

    .form-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 10px;
    }
    .form-row label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }
    .form-row select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        background: white;
        color: #1f2937;
    }
    .form-row select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .form-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }
    .btn-save-piket {
        flex: 1;
        padding: 8px 16px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }
    .btn-save-piket:hover { opacity: 0.9; }
    .btn-cancel-piket {
        padding: 8px 16px;
        background: #e5e7eb;
        color: #374151;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }
    .btn-cancel-piket:hover { background: #d1d5db; }

    @media (max-width: 768px) {
        .days-grid { grid-template-columns: 1fr; }
        .page-header-card { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="page-header-card">
            <div class="page-header-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="page-header-details">
                <h3>Manajemen Piket KBM</h3>
                <p>Kelola jadwal guru piket KBM untuk setiap hari</p>
            </div>
        </div>

        <!-- Day Blocks -->
        <div class="days-grid">
            @foreach($hariList as $hari)
            @php $piketHari = $piketData[$hari]; @endphp
            <div class="day-block" id="day-{{ $hari }}">
                <div class="day-block-header">
                    <h4><i class="fas fa-calendar-day"></i> {{ $hari }}</h4>
                    <span class="count-badge" id="count-{{ $hari }}">{{ $piketHari->count() }} guru</span>
                </div>
                <div class="day-block-body">
                    <div id="list-{{ $hari }}">
                        @forelse($piketHari as $piket)
                        <div class="piket-item" id="piket-{{ $piket->id }}">
                            <div class="piket-info">
                                <div class="piket-avatar {{ $piket->tipe_guru }}">
                                    <i class="fas {{ $piket->tipe_guru === 'guru_bk' ? 'fa-user-graduate' : 'fa-chalkboard-teacher' }}"></i>
                                </div>
                                <div>
                                    <div class="piket-name">{{ $piket->nama_guru }}</div>
                                    <div class="piket-nip">{{ $piket->nip ?? '-' }} &bull; <span class="piket-role-badge {{ $piket->tipe_guru }}">{{ $piket->tipe_guru === 'guru_bk' ? 'Guru BK' : 'Guru' }}</span></div>
                                </div>
                            </div>
                            <button class="piket-delete" onclick="deletePiket({{ $piket->id }}, '{{ $hari }}')" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        @empty
                        <div class="empty-piket" id="empty-{{ $hari }}">
                            <i class="fas fa-user-slash"></i>
                            Belum ada guru piket
                        </div>
                        @endforelse
                    </div>

                    <!-- Add Button -->
                    <button class="add-piket-btn" onclick="toggleForm('{{ $hari }}')">
                        <i class="fas fa-plus-circle"></i> Tambah Guru Piket
                    </button>

                    <!-- Add Form -->
                    <div class="add-piket-form" id="form-{{ $hari }}">
                        <div class="form-row">
                            <label>Tipe</label>
                            <select id="tipe-{{ $hari }}" onchange="updateGuruDropdown('{{ $hari }}')">
                                <option value="guru">Guru</option>
                                <option value="guru_bk">Guru BK</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Pilih Guru</label>
                            <select id="guru-{{ $hari }}">
                                @foreach($guruList as $g)
                                <option value="{{ $g->id }}" data-tipe="guru">{{ $g->nama }} {{ $g->nip ? '('.$g->nip.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-actions">
                            <button class="btn-save-piket" onclick="savePiket('{{ $hari }}')">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <button class="btn-cancel-piket" onclick="toggleForm('{{ $hari }}')">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Guru data for dropdown switching
    const guruData = @json($guruList);
    const guruBkData = @json($guruBkList);

    function toggleForm(hari) {
        const form = document.getElementById('form-' + hari);
        form.classList.toggle('show');
    }

    function updateGuruDropdown(hari) {
        const tipe = document.getElementById('tipe-' + hari).value;
        const select = document.getElementById('guru-' + hari);
        select.innerHTML = '';

        const data = tipe === 'guru_bk' ? guruBkData : guruData;
        data.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.nama + (g.nip ? ' (' + g.nip + ')' : '');
            select.appendChild(opt);
        });
    }

    function savePiket(hari) {
        const tipe = document.getElementById('tipe-' + hari).value;
        const guruId = document.getElementById('guru-' + hari).value;

        if (!guruId) {
            alert('Pilih guru terlebih dahulu!');
            return;
        }

        fetch('{{ route("admin.piket-kbm.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                hari: hari,
                guru_id: guruId,
                tipe_guru: tipe
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }

    function deletePiket(id, hari) {
        if (!confirm('Yakin hapus guru piket ini?')) return;

        fetch('{{ url("admin/piket-kbm") }}/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }
</script>
@endpush
