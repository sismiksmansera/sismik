<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        @php
            $logoImage = $loginSettings->logo_image ?? null;
        @endphp
        @if($logoImage)
            <img src="{{ asset('storage/' . $logoImage) }}" alt="Logo Sekolah" class="sidebar-logo">
        @else
            <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo Sekolah" class="sidebar-logo" onerror="this.style.display='none'">
        @endif
        <div class="brand-name">SISMIK</div>
        <p>Panel Siswa</p>
        <div class="toggle-indicator">‹ Klik untuk collapse</div>
    </div>


    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="{{ route('siswa.dashboard') }}" class="menu-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-label">Akademik</div>
        <a href="{{ route('siswa.nilai') }}" class="menu-item {{ request()->routeIs('siswa.nilai*') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Lihat Nilai</span>
        </a>
        <a href="{{ route('siswa.presensi') }}" class="menu-item {{ request()->routeIs('siswa.presensi*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-check"></i>
            <span>Lihat Presensi</span>
        </a>
        <a href="{{ route('siswa.jadwal') }}" class="menu-item {{ request()->routeIs('siswa.jadwal') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Jadwal Pelajaran</span>
        </a>
        <a href="{{ route('siswa.mapel') }}" class="menu-item {{ request()->routeIs('siswa.mapel') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Mata Pelajaran</span>
        </a>

        <div class="menu-label">Kegiatan</div>
        <a href="{{ route('siswa.ekstrakurikuler') }}" class="menu-item {{ request()->routeIs('siswa.ekstrakurikuler') ? 'active' : '' }}">
            <i class="fas fa-futbol"></i>
            <span>Ekstrakurikuler</span>
        </a>
        <a href="{{ route('siswa.prestasi') }}" class="menu-item {{ request()->routeIs('siswa.prestasi') ? 'active' : '' }}">
            <i class="fas fa-trophy"></i>
            <span>Prestasi</span>
        </a>

        <div class="menu-label">Lainnya</div>
        <a href="{{ route('siswa.catatan-bk') }}" class="menu-item {{ request()->routeIs('siswa.catatan-bk') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Catatan BK</span>
        </a>
        <a href="{{ route('siswa.catatan-guru-wali') }}" class="menu-item {{ request()->routeIs('siswa.catatan-guru-wali') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Catatan Guru Wali</span>
        </a>
        <a href="{{ route('siswa.pengaduan.index') }}" class="menu-item {{ request()->routeIs('siswa.pengaduan*') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i>
            <span>Pengaduan</span>
        </a>
        <a href="{{ route('siswa.riwayat-akademik') }}" class="menu-item {{ request()->routeIs('siswa.riwayat-akademik') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Riwayat Akademik</span>
        </a>

        <div class="menu-label">Akun</div>
        <a href="{{ route('siswa.profil') }}" class="menu-item {{ request()->routeIs('siswa.profil*') ? 'active' : '' }}">
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
