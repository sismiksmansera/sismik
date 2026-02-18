@extends($layout ?? 'layouts.app')

@section('title', 'Tugas Tambahan Lainnya | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        {{-- Header --}}
        <div class="content-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="header-text">
                    <h1>Tugas Tambahan Lainnya</h1>
                    <p class="header-subtitle">Kelola tugas tambahan guru di luar KBM</p>
                </div>
            </div>
        </div>

        {{-- Placeholder Content --}}
        <div class="content-section">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-hard-hat"></i>
                </div>
                <h3>Halaman Dalam Pengembangan</h3>
                <p>Fitur pengelolaan tugas tambahan lainnya akan segera tersedia.</p>
            </div>
        </div>
    </div>
</div>

<style>
.content-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.header-text h1 {
    margin: 0 0 0.25rem 0;
    font-size: 1.75rem;
    font-weight: 700;
}

.header-subtitle {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.9;
}

.content-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
}

.empty-icon i {
    font-size: 40px;
    color: #8b5cf6;
}

.empty-state h3 {
    margin: 0 0 10px;
    color: #374151;
    font-size: 1.25rem;
}

.empty-state p {
    color: #6b7280;
    margin: 0;
}
</style>
@endsection
