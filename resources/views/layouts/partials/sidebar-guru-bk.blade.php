<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        @php
            $logoImage = isset($loginSettings) ? ($loginSettings->logo_image ?? null) : null;
        @endphp
        @if($logoImage)
            <img src="{{ asset('storage/' . $logoImage) }}" alt="Logo Sekolah" class="sidebar-logo">
        @else
            <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo Sekolah" class="sidebar-logo" onerror="this.style.display='none'">
        @endif
        <div class="brand-name">SISMIK</div>
        <p>Panel Guru BK</p>
        <div class="toggle-indicator">‹ Klik untuk collapse</div>
    </div>


    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="{{ route('guru_bk.dashboard') }}" class="menu-item {{ request()->routeIs('guru_bk.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-label">Bimbingan</div>
        <a href="{{ route('guru_bk.semua-catatan') }}" class="menu-item {{ request()->routeIs('guru_bk.semua-catatan*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Catatan Bimbingan</span>
        </a>
        <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="menu-item {{ request()->routeIs('guru_bk.siswa-bimbingan*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Siswa Bimbingan</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-phone"></i>
            <span>Panggilan Ortu</span>
        </a>
        <a href="{{ route('guru_bk.pelanggaran') }}" class="menu-item {{ request()->routeIs('guru_bk.pelanggaran*') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Pelanggaran Siswa</span>
        </a>
        <a href="{{ route('guru_bk.tugas-tambahan') }}" class="menu-item {{ request()->routeIs('guru_bk.tugas-tambahan') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Tugas Tambahan</span>
        </a>

        <div class="menu-label">Pengaduan</div>
        <a href="{{ route('guru_bk.pengaduan') }}" class="menu-item {{ request()->routeIs('guru_bk.pengaduan*') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Pengaduan Siswa</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-chart-pie"></i>
            <span>Rekap Status</span>
        </a>

        <div class="menu-label">Akun</div>
        <a href="{{ route('guru_bk.profil') }}" class="menu-item {{ request()->routeIs('guru_bk.profil*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profil Saya</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left; font-family: inherit; font-size: inherit;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</div>
