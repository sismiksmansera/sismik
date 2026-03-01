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
        <p>Panel Admin</p>
        <div class="toggle-indicator">‹ Klik untuk collapse</div>
    </div>


    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-label">Manajemen Data</div>
        <a href="{{ route('admin.siswa.index') }}" class="menu-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Manajemen Siswa</span>
        </a>
        <a href="{{ route('admin.rombel.index') }}" class="menu-item {{ request()->routeIs('admin.rombel.*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard"></i>
            <span>Manajemen Rombel</span>
        </a>
        <a href="{{ route('admin.guru.index') }}" class="menu-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Manajemen Guru</span>
        </a>
        <a href="{{ route('admin.guru-bk.index') }}" class="menu-item {{ request()->routeIs('admin.guru-bk.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span>Manajemen Guru BK</span>
        </a>
        <a href="{{ route('admin.pengaduan.index') }}" class="menu-item {{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i>
            <span>Kelola Pengaduan</span>
        </a>

        <div class="menu-label">Akademik</div>

        <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="menu-item {{ request()->routeIs('admin.jadwal-pelajaran.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Jadwal Pelajaran</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-book"></i>
            <span>Mata Pelajaran</span>
        </a>
        <a href="{{ route('admin.piket-kbm.index') }}" class="menu-item {{ request()->routeIs('admin.piket-kbm.*') ? 'active' : '' }}">
            <i class="fas fa-user-clock"></i>
            <span>Piket KBM</span>
        </a>
        <a href="{{ route('admin.ekstrakurikuler.index') }}" class="menu-item {{ request()->routeIs('admin.ekstrakurikuler.*') ? 'active' : '' }}">
            <i class="fas fa-futbol"></i>
            <span>Ekstrakurikuler</span>
        </a>
        <a href="{{ route('admin.manajemen-talenta.index') }}" class="menu-item {{ request()->routeIs('admin.manajemen-talenta.*') ? 'active' : '' }}">
            <i class="fas fa-star"></i>
            <span>Manajemen Talenta</span>
        </a>
        <a href="{{ route('admin.tugas-tambahan.index') }}" class="menu-item {{ request()->routeIs('admin.tugas-tambahan.*') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Tugas Tambahan Lainnya</span>
        </a>
        <a href="{{ route('admin.migrasi-nilai.index') }}" class="menu-item {{ request()->routeIs('admin.migrasi-nilai.*') ? 'active' : '' }}">
            <i class="fas fa-file-upload"></i>
            <span>Migrasi Nilai Manual</span>
        </a>
        <a href="{{ route('admin.kartu-login-ujian.index') }}" class="menu-item {{ request()->routeIs('admin.kartu-login-ujian.*') ? 'active' : '' }}">
            <i class="fas fa-id-card"></i>
            <span>Kartu Login Ujian</span>
        </a>
        <a href="{{ route('admin.prestasi.index') }}" class="menu-item {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}">
            <i class="fas fa-trophy"></i>
            <span>Prestasi</span>
        </a>

        <div class="menu-label">Presensi Siswa</div>
        <a href="{{ route('admin.cek-presensi.index') }}" class="menu-item {{ request()->routeIs('admin.cek-presensi.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-check"></i>
            <span>Cek Presensi</span>
        </a>
        <a href="{{ route('admin.download-presensi.index') }}" class="menu-item {{ request()->routeIs('admin.download-presensi.*') ? 'active' : '' }}">
            <i class="fas fa-download"></i>
            <span>Download Presensi</span>
        </a>

        <div class="menu-label">Laporan</div>
        <a href="{{ route('admin.leger.index') }}" class="menu-item {{ request()->routeIs('admin.leger.*') ? 'active' : '' }}">
            <i class="fas fa-table"></i>
            <span>Leger</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-chart-bar"></i>
            <span>Laporan</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-print"></i>
            <span>Cetak Raport</span>
        </a>
        <a href="{{ route('admin.tamu.index') }}" class="menu-item {{ request()->routeIs('admin.tamu.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Daftar Tamu</span>
        </a>

        <div class="menu-label">Pengaturan</div>
        <a href="{{ route('admin.manajemen-sekolah') }}" class="menu-item {{ request()->routeIs('admin.manajemen-sekolah') ? 'active' : '' }}">
            <i class="fas fa-school"></i>
            <span>Manajemen Sekolah</span>
        </a>
        <a href="{{ route('admin.pengaturan-lainnya') }}" class="menu-item {{ request()->routeIs('admin.pengaturan-lainnya') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Pengaturan Lainnya</span>
        </a>
        <a href="{{ route('admin.keamanan.index') }}" class="menu-item {{ request()->routeIs('admin.keamanan.*') ? 'active' : '' }}">
            <i class="fas fa-shield-alt"></i>
            <span>Keamanan</span>
        </a>
        <a href="{{ route('admin.backup-restore') }}" class="menu-item {{ request()->routeIs('admin.backup-restore') || request()->routeIs('admin.backup.*') ? 'active' : '' }}">
            <i class="fas fa-database"></i>
            <span>Backup & Restore</span>
        </a>

        <div class="menu-label">Akun</div>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left; font-family: inherit; font-size: inherit;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</div>
