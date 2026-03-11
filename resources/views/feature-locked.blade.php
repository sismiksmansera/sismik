@extends('layouts.app')
@section('title', 'Fitur Terkunci | SISMIK')

@section('content')
<div class="layout">
    @if(isset($role) && $role === 'guru')
        @include('layouts.partials.sidebar-guru')
    @elseif(isset($role) && $role === 'siswa')
        @include('layouts.partials.sidebar-siswa')
    @endif

    <div class="main-content">
        <div style="display:flex;align-items:center;justify-content:center;min-height:60vh;">
            <div style="text-align:center;max-width:450px;padding:40px;">
                <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
                    <i class="fas fa-lock" style="font-size:36px;color:white;"></i>
                </div>
                <h2 style="font-size:22px;font-weight:700;color:#1f2937;margin-bottom:12px;">Fitur Terkunci</h2>
                <p style="font-size:15px;color:#6b7280;line-height:1.7;margin-bottom:24px;">
                    Mohon maaf, untuk sementara fitur ini belum bisa ditampilkan.
                </p>
                <a href="javascript:history.back()" style="display:inline-block;padding:12px 28px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;transition:all .2s;">
                    <i class="fas fa-arrow-left" style="margin-right:8px;"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
