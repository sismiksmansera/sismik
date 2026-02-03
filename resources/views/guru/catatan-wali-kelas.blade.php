@extends('layouts.app')

@section('title', 'Catatan Wali Kelas - ' . $siswa->nama)

@push('styles')
<style>
/* HEADER SECTION */
.catatan-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.catatan-header .header-icon-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto 20px;
}

.catatan-header .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-transform: uppercase;
}

.catatan-header .header-subtitle {
    font-size: 14px;
    opacity: 0.9;
}

/* STUDENT INFO CARD */
.student-info-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
}

.student-photo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #f59e0b;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    font-weight: 700;
}

.student-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-details { flex: 1; }
.student-details h3 {
    margin: 0 0 8px 0;
    font-size: 1.3rem;
    color: #1f2937;
}

.student-details .info-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    color: #6b7280;
    font-size: 14px;
}

.student-details .info-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.student-details .info-item i { color: #f59e0b; }

/* ACTION BUTTONS */
.action-buttons-header {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 25px;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-back {
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
}

.btn-back:hover {
    background: #f3f4f6;
    transform: translateY(-2px);
    color: #1f2937;
}

/* FORM SECTION */
.form-section {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.form-section h4 {
    margin: 0 0 20px 0;
    font-size: 1.1rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section h4 i { color: #f59e0b; }

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

.btn-simpan {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-simpan:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
}

.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    align-items: center;
}

/* CATATAN SECTION */
.catatan-section {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.catatan-section .section-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.catatan-section .section-header h4 {
    margin: 0;
    font-size: 1.1rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
}

.catatan-section .section-header h4 i { color: #f59e0b; }

.catatan-count-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.catatan-list {
    padding: 20px;
}

.catatan-item {
    background: #fefce8;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid #f59e0b;
    transition: all 0.2s ease;
}

.catatan-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.catatan-item:last-child {
    margin-bottom: 0;
}

.catatan-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.catatan-tanggal {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #92400e;
    font-size: 14px;
}

.catatan-tanggal i { color: #f59e0b; }

.btn-hapus {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn-hapus:hover {
    background: #fecaca;
}

.catatan-content {
    color: #374151;
    font-size: 14px;
    line-height: 1.6;
    white-space: pre-wrap;
}

.empty-state {
    padding: 60px 30px;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #fef3c7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 32px;
    color: #f59e0b;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 1.1rem;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .catatan-header { padding: 20px 15px; }
    .catatan-header .header-icon-large { width: 60px; height: 60px; font-size: 28px; }
    .catatan-header .page-title { font-size: 22px; }
    
    .student-info-card {
        flex-direction: column;
        text-align: center;
    }
    
    .student-details .info-row {
        justify-content: center;
    }
    
    .catatan-header-row {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="catatan-wali-kelas-page">
            <!-- HEADER -->
            <div class="catatan-header">
                <div class="header-icon-large">
                    <i class="fas fa-sticky-note"></i>
                </div>
                <h1 class="page-title">Catatan Wali Kelas</h1>
                <p class="header-subtitle">{{ $rombel->nama_rombel }} - {{ $tahunPelajaran }} {{ ucfirst($semester) }}</p>
            </div>



            <!-- STUDENT INFO CARD -->
            <div class="student-info-card">
                <div class="student-photo">
                    @if($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto))
                        <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                    @else
                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                    @endif
                </div>
                <div class="student-details">
                    <h3>{{ $siswa->nama }}</h3>
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-id-card"></i>
                            <span>NIS: {{ $siswa->nis }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-fingerprint"></i>
                            <span>NISN: {{ $siswa->nisn }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM INPUT CATATAN -->
            <div class="form-section">
                <h4><i class="fas fa-plus-circle"></i> Tambah Catatan Baru</h4>
                <form id="formCatatan">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $siswaId }}">
                    <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                    <input type="hidden" name="tahun_pelajaran" value="{{ $tahunPelajaran }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    
                    <div class="form-group">
                        <label for="tanggal"><i class="fas fa-calendar-alt"></i> Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" 
                               value="{{ date('Y-m-d') }}" 
                               min="{{ $minDate }}" 
                               max="{{ $maxDate }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="catatan"><i class="fas fa-comment-alt"></i> Isi Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" placeholder="Tuliskan catatan untuk siswa ini..." required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <a href="{{ route('guru.anggota-rombel', ['id' => $rombelId, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" class="btn-modern btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-simpan">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

            <!-- DAFTAR CATATAN -->
            <div class="catatan-section">
                <div class="section-header">
                    <h4><i class="fas fa-list"></i> Riwayat Catatan</h4>
                    <span class="catatan-count-badge" id="catatanCount">{{ count($catatanList) }} Catatan</span>
                </div>
                
                <div class="catatan-list" id="catatanList">
                    @if(count($catatanList) > 0)
                        @foreach($catatanList as $catatan)
                        <div class="catatan-item" data-id="{{ $catatan->id }}">
                            <div class="catatan-header-row">
                                <div class="catatan-tanggal">
                                    <i class="fas fa-calendar-day"></i>
                                    {{ \Carbon\Carbon::parse($catatan->tanggal)->format('d F Y') }}
                                </div>
                                <button type="button" class="btn-hapus" onclick="hapusCatatan({{ $catatan->id }})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                            <div class="catatan-content">{{ $catatan->catatan }}</div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <h3>Belum Ada Catatan</h3>
                            <p>Belum ada catatan yang dibuat untuk siswa ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Submit form catatan
document.getElementById('formCatatan').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("guru.catatan-wali-kelas.simpan") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("guru.anggota-rombel", ["id" => $rombelId, "tahun" => $tahunPelajaran, "semester" => $semester]) }}';
            }, 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan!', 'error');
    });
});

function hapusCatatan(id) {
    if (!confirm('Hapus catatan ini?')) return;
    
    const formData = new FormData();
    formData.append('catatan_id', id);
    
    fetch('{{ route("guru.catatan-wali-kelas.hapus") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            document.querySelector(`.catatan-item[data-id="${id}"]`).remove();
            
            // Update count
            const count = document.querySelectorAll('.catatan-item').length;
            document.getElementById('catatanCount').textContent = count + ' Catatan';
            
            // Show empty state if no more items
            if (count === 0) {
                document.getElementById('catatanList').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <h3>Belum Ada Catatan</h3>
                        <p>Belum ada catatan yang dibuat untuk siswa ini.</p>
                    </div>
                `;
            }
        } else {
            showToast(data.message, 'error');
        }
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${type === 'success' ? '#10b981' : '#ef4444'}; color: white; padding: 15px 25px; border-radius: 10px; z-index: 9999; box-shadow: 0 10px 30px rgba(0,0,0,0.2);`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endsection
