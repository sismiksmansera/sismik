@extends($layout ?? 'layouts.app')

@section('title', 'Manajemen Talenta | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        {{-- Header --}}
        <div class="mt-header">
            <div class="mt-header-content">
                <div class="mt-header-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="mt-header-text">
                    <h1>Manajemen Talenta</h1>
                    <p>Kelola ajang talenta dan prestasi siswa</p>
                </div>
            </div>
            <div class="mt-header-stats">
                <div class="mt-stat-badge">
                    <i class="fas fa-flag"></i>
                    <span>{{ count($ajangList) }} Ajang</span>
                </div>
            </div>
        </div>

        {{-- Ajang Talenta Section --}}
        <div class="mt-content-section" style="margin-bottom: 24px;">
            <div class="mt-section-header">
                <div class="mt-section-title">
                    <i class="fas fa-flag"></i>
                    <h2>Ajang Talenta</h2>
                </div>
                <button class="mt-btn-add" onclick="openAjangModal()">
                    <i class="fas fa-plus-circle"></i> Tambah Ajang Talenta
                </button>
            </div>

            @if(count($ajangList) == 0)
            <div class="mt-empty-state" style="padding: 30px 20px;">
                <div class="mt-empty-icon" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="fas fa-flag"></i>
                </div>
                <h3>Belum Ada Ajang Talenta</h3>
                <p>Klik "Tambah Ajang Talenta" untuk menambahkan.</p>
            </div>
            @else
            <div class="ajang-grid">
                @foreach($ajangList as $ajang)
                <div class="ajang-card" id="ajang-{{ $ajang->id }}">
                    <div class="ajang-card-header">
                        <div class="ajang-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <button class="ajang-delete" onclick="deleteAjang({{ $ajang->id }}, '{{ addslashes($ajang->nama_ajang) }}')" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <h3 class="ajang-name">{{ $ajang->nama_ajang }}</h3>
                    <div class="ajang-details">
                        @if($ajang->tahun)
                        <div class="ajang-detail-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ $ajang->tahun }}</span>
                        </div>
                        @endif
                        @if($ajang->penyelenggara)
                        <div class="ajang-detail-item">
                            <i class="fas fa-building"></i>
                            <span>{{ $ajang->penyelenggara }}</span>
                        </div>
                        @endif
                        @if($ajang->pembina)
                        <div class="ajang-detail-item">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $ajang->pembina }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Modal Tambah Ajang Talenta --}}
<div class="mt-modal-overlay" id="ajangModalOverlay" onclick="closeAjangModal(event)">
    <div class="mt-modal" onclick="event.stopPropagation()">
        <div class="mt-modal-header">
            <div class="mt-modal-title">
                <i class="fas fa-plus-circle"></i>
                <h3>Tambah Ajang Talenta</h3>
            </div>
            <button class="mt-modal-close" onclick="closeAjangModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="ajangForm" onsubmit="submitAjang(event)">
            <div class="mt-modal-body">
                <div class="mt-form-group">
                    <label>Nama Ajang Talenta <span class="required">*</span></label>
                    <input type="text" id="namaAjang" name="nama_ajang" placeholder="Contoh: Olimpiade Sains Nasional" required>
                </div>
                <div class="mt-form-group">
                    <label>Tahun</label>
                    <input type="text" id="tahunAjang" name="tahun" placeholder="Contoh: 2026" maxlength="10">
                </div>
                <div class="mt-form-group">
                    <label>Penyelenggara</label>
                    <input type="text" id="penyelenggaraAjang" name="penyelenggara" placeholder="Contoh: Kemendikbudristek">
                </div>
                <div class="mt-form-group">
                    <label>Pembina</label>
                    <input type="text" id="pembinaAjang" name="pembina" placeholder="Nama pembina/pelatih">
                </div>
            </div>
            <div class="mt-modal-footer">
                <button type="button" class="mt-btn-cancel" onclick="closeAjangModal()">Batal</button>
                <button type="submit" class="mt-btn-save" id="btnSaveAjang">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* HEADER */
    .mt-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 16px;
        color: white;
    }
    .mt-header-content { display: flex; align-items: center; gap: 16px; }
    .mt-header-icon {
        width: 52px; height: 52px; border-radius: 14px;
        background: rgba(255, 255, 255, 0.2);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 22px;
    }
    .mt-header-text h1 { font-size: 22px; font-weight: 800; color: white; margin: 0; }
    .mt-header-text p { font-size: 13px; color: rgba(255,255,255,0.8); margin: 4px 0 0; }
    .mt-header-stats { display: flex; gap: 12px; }
    .mt-stat-badge {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: white; font-size: 13px; font-weight: 600;
    }
    .mt-stat-badge.gold {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fef3c7;
    }

    /* BTN ADD */
    .mt-btn-add {
        padding: 10px 20px; border-radius: 10px;
        background: white; color: #3b82f6;
        border: none;
        font-size: 13px; font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .mt-btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.12);
        background: #f0f9ff;
    }

    /* AJANG GRID */
    .ajang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }
    .ajang-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        transition: all 0.3s;
        position: relative;
    }
    .ajang-card:hover {
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.08);
    }
    .ajang-card-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 12px;
    }
    .ajang-icon {
        width: 38px; height: 38px; border-radius: 10px;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        display: flex; align-items: center; justify-content: center;
        color: #f59e0b; font-size: 16px;
    }
    .ajang-delete {
        width: 26px; height: 26px; border-radius: 6px;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #ef4444; font-size: 11px;
        cursor: pointer; display: flex;
        align-items: center; justify-content: center;
        transition: all 0.2s; opacity: 0;
    }
    .ajang-card:hover .ajang-delete { opacity: 1; }
    .ajang-delete:hover {
        background: #fecaca;
        transform: scale(1.1);
    }
    .ajang-name {
        font-size: 15px; font-weight: 700; color: #1e293b;
        margin-bottom: 12px; line-height: 1.3;
    }
    .ajang-details { display: flex; flex-direction: column; gap: 6px; }
    .ajang-detail-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #64748b;
    }
    .ajang-detail-item i { width: 14px; text-align: center; font-size: 11px; color: #94a3b8; }

    /* CONTENT */
    .mt-content-section {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .mt-section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px; padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .mt-section-title { display: flex; align-items: center; gap: 10px; }
    .mt-section-title i { color: #3b82f6; font-size: 18px; }
    .mt-section-title h2 { font-size: 17px; font-weight: 700; color: #1e293b; margin: 0; }
    .mt-section-count {
        padding: 6px 14px; border-radius: 8px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #2563eb; font-size: 12px; font-weight: 600;
    }

    /* EMPTY STATE */
    .mt-empty-state { text-align: center; padding: 50px 20px; }
    .mt-empty-icon {
        width: 70px; height: 70px; border-radius: 50%;
        background: #eff6ff;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px; font-size: 28px; color: #3b82f6;
    }
    .mt-empty-state h3 { font-size: 16px; color: #334155; margin-bottom: 8px; }
    .mt-empty-state p { color: #94a3b8; font-size: 13px; }

    /* MODAL */
    .mt-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);
        z-index: 9999;
        display: none; align-items: center; justify-content: center;
        padding: 20px;
    }
    .mt-modal-overlay.active { display: flex; }
    .mt-modal {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        width: 100%; max-width: 500px;
        animation: modalSlideIn 0.3s ease;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .mt-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .mt-modal-title { display: flex; align-items: center; gap: 10px; }
    .mt-modal-title i { color: #3b82f6; font-size: 18px; }
    .mt-modal-title h3 { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; }
    .mt-modal-close {
        width: 32px; height: 32px; border-radius: 8px;
        background: #f1f5f9; border: none; color: #64748b;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .mt-modal-close:hover { background: #fee2e2; color: #ef4444; }
    .mt-modal-body { padding: 24px; }
    .mt-form-group { margin-bottom: 18px; }
    .mt-form-group label {
        display: block; font-size: 12px; font-weight: 600;
        color: #64748b; text-transform: uppercase;
        letter-spacing: 0.5px; margin-bottom: 8px;
    }
    .mt-form-group label .required { color: #ef4444; }
    .mt-form-group input {
        width: 100%; padding: 12px 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px; color: #1e293b;
        font-size: 14px; font-family: 'Poppins', sans-serif;
        transition: all 0.2s;
    }
    .mt-form-group input::placeholder { color: #94a3b8; }
    .mt-form-group input:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    .mt-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .mt-btn-cancel {
        padding: 10px 20px; border-radius: 10px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #64748b; font-size: 13px; font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer; transition: all 0.2s;
    }
    .mt-btn-cancel:hover { background: #e2e8f0; color: #475569; }
    .mt-btn-save {
        padding: 10px 24px; border-radius: 10px;
        background: #3b82f6;
        border: none; color: white;
        font-size: 13px; font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        transition: all 0.3s;
    }
    .mt-btn-save:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.25);
    }
    .mt-btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .mt-header { flex-direction: column; align-items: flex-start; }
        .ajang-grid { grid-template-columns: 1fr; }
    }
</style>

<script>
function openAjangModal() {
    document.getElementById('ajangForm').reset();
    document.getElementById('ajangModalOverlay').classList.add('active');
}

function closeAjangModal(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('ajangModalOverlay').classList.remove('active');
}

function submitAjang(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveAjang');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = {
        nama_ajang: document.getElementById('namaAjang').value,
        tahun: document.getElementById('tahunAjang').value,
        penyelenggara: document.getElementById('penyelenggaraAjang').value,
        pembina: document.getElementById('pembinaAjang').value
    };

    fetch('{{ route("admin.manajemen-talenta.ajang.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        if (data.success) {
            closeAjangModal();
            location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        alert('Gagal menghubungi server');
    });
}

function deleteAjang(ajangId, nama) {
    if (!confirm(`Yakin ingin menghapus ajang "${nama}"?`)) return;

    fetch('{{ route("admin.manajemen-talenta.ajang.delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ajang_id: ajangId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById('ajang-' + ajangId);
            if (card) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 300);
            }
        } else {
            alert(data.message || 'Gagal menghapus');
        }
    })
    .catch(() => alert('Gagal menghubungi server'));
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAjangModal();
});
</script>
@endsection
