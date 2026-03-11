@extends('layouts.app')

@section('title', 'Ekstrakurikuler | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .ekskul-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #10b981;
        margin-bottom: 25px;
    }
    .ekskul-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .ekskul-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .ekskul-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Cards Grid */
    .ekskul-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    /* Ekskul Card */
    .ekskul-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .ekskul-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .ekskul-card-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 18px 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .ekskul-icon {
        width: 45px;
        height: 45px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .ekskul-info h4 { margin: 0; font-size: 1rem; font-weight: 600; }
    .ekskul-info p { margin: 3px 0 0 0; font-size: 0.75rem; opacity: 0.9; }

    .ekskul-card-body { padding: 20px; }

    /* Pembina Section */
    .ekskul-section { margin-bottom: 15px; }
    .ekskul-section-label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .ekskul-section-label i { font-size: 10px; }

    .pembina-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .pembina-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        background: #f0fdf4;
        color: #059669;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .pembina-tag i { font-size: 10px; }

    .ekskul-desc {
        color: #4b5563;
        font-size: 0.85rem;
        line-height: 1.5;
        margin: 0;
    }

    /* Nilai Section */
    .ekskul-nilai {
        border-top: 1px solid #e5e7eb;
        padding-top: 15px;
        margin-top: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .nilai-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
    }
    .nilai-label { font-size: 0.75rem; color: #6b7280; }

    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 16px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .empty-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }
    .empty-icon i { font-size: 40px; color: white; }
    .empty-state h3 { margin: 0 0 12px 0; color: #1f2937; font-size: 1.25rem; font-weight: 600; }
    .empty-state p { margin: 0; color: #6b7280; font-size: 0.95rem; max-width: 400px; margin: 0 auto; }

    @media (max-width: 768px) {
        .ekskul-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="ekskul-header">
            <div class="ekskul-header-icon">
                <i class="fas fa-futbol"></i>
            </div>
            <div class="ekskul-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Ekstrakurikuler Saya</p>
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $tahunAktif }} - {{ $semesterAktif }}
                </p>
            </div>
        </div>

        @if($ekskulList->count() > 0)
        <div class="ekskul-grid">
            @foreach($ekskulList as $ekskul)
            <div class="ekskul-card">
                <div class="ekskul-card-header">
                    <div class="ekskul-icon">
                        <i class="fas {{ $ekskul->icon }}"></i>
                    </div>
                    <div class="ekskul-info">
                        <h4>{{ $ekskul->nama_ekstrakurikuler }}</h4>
                        <p>{{ $ekskul->tahun_pelajaran }} - {{ $ekskul->semester }}</p>
                    </div>
                </div>
                <div class="ekskul-card-body">
                    @if(count($ekskul->pembina_list) > 0)
                    <div class="ekskul-section">
                        <div class="ekskul-section-label">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Pembina</span>
                        </div>
                        <div class="pembina-tags">
                            @foreach($ekskul->pembina_list as $pembina)
                            <span class="pembina-tag">
                                <i class="fas fa-user-tie"></i>
                                {{ $pembina }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($ekskul->deskripsi)
                    <div class="ekskul-section">
                        <div class="ekskul-section-label">
                            <i class="fas fa-info-circle"></i>
                            <span>Deskripsi</span>
                        </div>
                        <p class="ekskul-desc">{{ $ekskul->deskripsi }}</p>
                    </div>
                    @endif

                    <div class="ekskul-nilai">
                        <div class="ekskul-section-label" style="margin: 0;">
                            <i class="fas fa-star"></i>
                            <span>Nilai</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="nilai-badge" style="background: {{ $ekskul->nilai_info['bg'] }}; color: {{ $ekskul->nilai_info['text'] }};">
                                {{ $ekskul->nilai ?: '-' }}
                            </span>
                            <span class="nilai-label">{{ $ekskul->nilai_info['label'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-futbol"></i></div>
            <h3>Belum Mengikuti Ekstrakurikuler</h3>
            <p>Kamu belum terdaftar di ekstrakurikuler manapun untuk periode {{ $tahunAktif }} semester {{ $semesterAktif }}.</p>
        </div>
        @endif
    </div>
</div>
@endsection
