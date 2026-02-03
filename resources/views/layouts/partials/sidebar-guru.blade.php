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
        <p>Panel Guru</p>
        <div class="toggle-indicator">‹ Klik untuk collapse</div>
    </div>


    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="{{ route('guru.dashboard') }}" class="menu-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-label">Akademik</div>
        <a href="{{ route('guru.presensi-selector') }}" class="menu-item {{ request()->routeIs('guru.presensi-selector') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Presensi Siswa</span>
        </a>
        <a href="{{ route('guru.input-penilaian') }}" class="menu-item {{ request()->routeIs('guru.input-penilaian') ? 'active' : '' }}">
            <i class="fas fa-edit"></i>
            <span>Input Penilaian</span>
        </a>
        <a href="{{ route('guru.lihat-nilai-selector') }}" class="menu-item {{ request()->routeIs('guru.lihat-nilai-selector') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Lihat Nilai</span>
        </a>

        <div class="menu-label">Kelas</div>
        <a href="#" class="menu-item">
            <i class="fas fa-users"></i>
            <span>Anggota Rombel</span>
        </a>
        <a href="{{ route('guru.jadwal') }}" class="menu-item {{ request()->routeIs('guru.jadwal') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Jadwal Pelajaran</span>
        </a>
        <a href="{{ route('guru.tugas-mengajar') }}" class="menu-item {{ request()->routeIs('guru.tugas-mengajar') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Tugas Mengajar</span>
        </a>
        <a href="{{ route('guru.tugas-tambahan') }}" class="menu-item {{ request()->routeIs('guru.tugas-tambahan') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Tugas Tambahan</span>
        </a>

        <div class="menu-label">Laporan</div>
        <a href="#" class="menu-item">
            <i class="fas fa-print"></i>
            <span>Cetak Raport</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-trophy"></i>
            <span>Prestasi</span>
        </a>

        <div class="menu-label">Akun</div>
        <a href="{{ route('guru.profil') }}" class="menu-item {{ request()->routeIs('guru.profil*') ? 'active' : '' }}">
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
