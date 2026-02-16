@extends('layouts.app')

@section('title', 'Leger Nilai Katrol')

@push('styles')
<style>
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}
.btn-show-leger {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-show-leger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
    color: white;
}
.leger-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}
.leger-table th, .leger-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}
.leger-table th {
    background: #4f46e5;
    color: white;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}
.leger-table tbody tr:nth-child(even) {
    background: #f9fafb;
}
.leger-table tbody tr:hover {
    background: #e0e7ff;
}
#legerContainer {
    max-height: 600px;
    overflow: auto;
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div class="container-fluid px-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-table text-primary"></i> Leger Nilai Katrol
                    </h1>
                    <p class="text-muted mb-0">Tampilkan leger nilai dari tabel Katrol Nilai Leger</p>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Data</h5>
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <select class="form-select" id="tahun_pelajaran" name="tahun_pelajaran" required>
                                <option value="">Pilih Tahun Pelajaran</option>
                                @foreach($tahunList as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Semester</label>
                            <select class="form-select" id="semester" name="semester" required disabled>
                                <option value="">Pilih Tahun Dulu</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rombel</label>
                            <select class="form-select" id="rombel_id" name="rombel_id" required disabled>
                                <option value="">Pilih Semester Dulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-show-leger" id="btnShowLeger" disabled>
                            <i class="fas fa-eye"></i> Tampilkan Leger
                        </button>
                    </div>
                </form>
            </div>

            <!-- Leger Display -->
            <div id="legerContainer" style="display: none;">
                <div class="filter-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Leger Nilai Katrol</h5>
                        <button class="btn btn-sm btn-success" id="btnPrintLeger">
                            <i class="fas fa-print"></i> Cetak Leger
                        </button>
                    </div>
                    <div id="legerTableWrapper" style="overflow-x: auto;">
                        <!-- Table will be inserted here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tahunInput = document.getElementById('tahun_pelajaran');
    const semesterInput = document.getElementById('semester');
    const rombelInput = document.getElementById('rombel_id');
    const btnShowLeger = document.getElementById('btnShowLeger');
    const btnPrintLeger = document.getElementById('btnPrintLeger');
    const legerContainer = document.getElementById('legerContainer');
    
    // Load semesters when tahun changes
    tahunInput.addEventListener('change', function() {
        const tahun = this.value;
        
        semesterInput.innerHTML = '<option value="">Loading...</option>';
        semesterInput.disabled = true;
        rombelInput.innerHTML = '<option value="">Pilih Semester Dulu</option>';
        rombelInput.disabled = true;
        btnShowLeger.disabled = true;
        legerContainer.style.display = 'none';
        
        if (!tahun) {
            semesterInput.innerHTML = '<option value="">Pilih Tahun Dulu</option>';
            return;
        }
        
        fetch('{{ route("admin.leger.get-semesters") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tahun_pelajaran: tahun })
        })
        .then(response => response.json())
        .then(semesters => {
            semesterInput.innerHTML = '<option value="">Pilih Semester</option>';
            semesters.forEach(semester => {
                semesterInput.innerHTML += `<option value="${semester}">${semester}</option>`;
            });
            semesterInput.disabled = false;
        });
    });
    
    // Load rombels when semester changes
    semesterInput.addEventListener('change', function() {
        const tahun = tahunInput.value;
        const semester = this.value;
        
        rombelInput.innerHTML = '<option value="">Loading...</option>';
        rombelInput.disabled = true;
        btnShowLeger.disabled = true;
        legerContainer.style.display = 'none';
        
        if (!semester) {
            rombelInput.innerHTML = '<option value="">Pilih Semester Dulu</option>';
            return;
        }
        
        fetch('{{ route("admin.leger.get-rombels") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                tahun_pelajaran: tahun,
                semester: semester 
            })
        })
        .then(response => response.json())
        .then(rombels => {
            rombelInput.innerHTML = '<option value="">Pilih Rombel</option>';
            rombels.forEach(rombel => {
                rombelInput.innerHTML += `<option value="${rombel.id}">${rombel.nama_rombel}</option>`;
            });
            rombelInput.disabled = false;
        });
    });
    
    // Enable buttons when rombel selected
    rombelInput.addEventListener('change', function() {
        btnShowLeger.disabled = !this.value;
        legerContainer.style.display = 'none';
    });
    
    // Show leger when button clicked
    btnShowLeger.addEventListener('click', function() {
        const rombelId = rombelInput.value;
        const tahun = tahunInput.value;
        const semester = semesterInput.value;
        
        console.log('=== LEGER DEBUG ===');
        console.log('Rombel ID:', rombelId);
        console.log('Tahun:', tahun);
        console.log('Semester:', semester);
        
        if (!rombelId) return;
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        const url = `/admin/leger/data?rombel_id=${rombelId}&tahun=${tahun}&semester=${semester}`;
        console.log('Fetching from:', url);
        
        fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            console.log('Students count:', data.students ? data.students.length : 0);
            console.log('Mapels count:', data.mapels ? data.mapels.length : 0);
            displayLeger(data);
            btnShowLeger.disabled = false;
            btnShowLeger.innerHTML = '<i class="fas fa-eye"></i> Tampilkan Leger';
            legerContainer.style.display = 'block';
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Gagal memuat data leger: ' + error.message);
            btnShowLeger.disabled = false;
            btnShowLeger.innerHTML = '<i class="fas fa-eye"></i> Tampilkan Leger';
        });
    });
    
    // Print leger when button clicked
    btnPrintLeger.addEventListener('click', function() {
        const rombelId = rombelInput.value;
        const tahun = tahunInput.value;
        const semester = semesterInput.value;
        
        if (!rombelId) return;
        
        const url = `/admin/leger/print?rombel_id=${rombelId}&tahun=${tahun}&semester=${semester}`;
        window.open(url, '_blank', 'width=1200,height=800');
    });
    
    function displayLeger(data) {
        console.log('displayLeger called with:', data);
        const wrapper = document.getElementById('legerTableWrapper');
        
        if (!data.students || data.students.length === 0) {
            console.log('No students data');
            wrapper.innerHTML = '<p class="text-center text-muted">Tidak ada data leger untuk rombel ini</p>';
            return;
        }
        
        console.log('Generating table for', data.students.length, 'students');
        
        let html = '<table class="leger-table"><thead><tr>';
        html += '<th rowspan="2">No</th>';
        html += '<th rowspan="2">NISN</th>';
        html += '<th rowspan="2">Nama Siswa</th>';
        
        // Mapel headers
        data.mapels.forEach(mapel => {
            html += `<th rowspan="2">${mapel}</th>`;
        });
        
        html += '<th rowspan="2">Jumlah</th>';
        html += '<th rowspan="2">Rata-rata</th>';
        html += '<th rowspan="2">Ranking</th>';
        html += '</tr></thead><tbody>';
        
        // Student rows
        data.students.forEach((student, index) => {
            html += '<tr>';
            html += `<td>${index + 1}</td>`;
            html += `<td>${student.nisn}</td>`;
            html += `<td style="text-align: left;">${student.nama_siswa}</td>`;
            
            data.mapels.forEach(mapel => {
                const nilai = student.nilai[mapel] || '-';
                html += `<td>${nilai}</td>`;
            });
            
            html += `<td style="font-weight: bold; background: #fef3c7;">${student.jumlah}</td>`;
            html += `<td style="font-weight: bold; background: #fef2f2;">${student.rata_rata_display}</td>`;
            html += `<td style="font-weight: bold; background: #f3e8ff;">${student.ranking}</td>`;
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        wrapper.innerHTML = html;
    }
});
</script>
@endpush
