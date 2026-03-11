@extends('layouts.app')

@section('title', 'Pengaturan Lainnya | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .header-icon {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
    }
    .header-text h1 { font-size: 1.5rem; font-weight: 700; }
    .header-text p { opacity: 0.85; margin-top: 4px; font-size: 14px; }
    .content-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px; padding-bottom: 15px;
        border-bottom: 2px solid var(--gray-100);
    }
    .section-header h2 {
        font-size: 1.1rem; font-weight: 700;
        color: var(--dark);
        display: flex; align-items: center; gap: 10px;
    }
    .section-header h2 i { color: var(--primary); }
    .header-actions { display: flex; gap: 10px; align-items: center; }
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block; font-weight: 600;
        margin-bottom: 8px; color: var(--gray-700);
    }
    .form-control {
        width: 100%; padding: 12px 16px;
        border: 2px solid var(--gray-200);
        border-radius: 10px; font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        outline: none; border-color: var(--primary);
    }
    .modern-switch {
        position: relative; display: inline-block;
        width: 48px; height: 24px;
    }
    .modern-switch input { opacity: 0; width: 0; height: 0; }
    .modern-switch .slider {
        position: absolute; cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc; border-radius: 24px;
        transition: 0.3s;
    }
    .modern-switch .slider:before {
        content: "";
        position: absolute; height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background-color: white; border-radius: 50%;
        transition: 0.3s;
    }
    .modern-switch input:checked + .slider { background-color: var(--primary); }
    .modern-switch input:checked + .slider:before { transform: translateX(24px); }

    /* Modal Styles */
    .modal-overlay {
        display: none; position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1050;
        align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white; border-radius: 16px;
        width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto;
    }
    .modal-header {
        padding: 20px 24px; border-bottom: 1px solid var(--gray-200);
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h3 { font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .modal-close {
        background: none; border: none;
        font-size: 24px; cursor: pointer; color: var(--gray-500);
    }
    .modal-body { padding: 24px; }
    .modal-footer {
        padding: 16px 24px; border-top: 1px solid var(--gray-200);
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .alert-success {
        background: #d1fae5; color: #065f46;
        padding: 14px 20px; border-radius: 12px;
        margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
    }
    .alert-danger {
        background: #fee2e2; color: #991b1b;
        padding: 14px 20px; border-radius: 12px;
        margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="header-text">
                <h1>Pengaturan Lainnya</h1>
                <p>Kelola pengaturan halaman login, testing date, dan maintenance mode</p>
            </div>
        </div>

        <!-- Maintenance Mode Quick Card -->
        <div onclick="openMaintenanceModal()" style="background: white; border-radius: 16px; padding: 18px 24px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: space-between; cursor: pointer; border: 1px solid #e5e7eb; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)'">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-tools" style="color: white; font-size: 18px;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: #1f2937; font-size: 15px;">Maintenance Mode</div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 2px;" id="maintenanceQuickStatus">Memuat status...</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span id="maintenanceQuickBadge" style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f3f4f6; color: #6b7280;">...</span>
                <i class="fas fa-chevron-right" style="color: #9ca3af; font-size: 12px;"></i>
            </div>
        </div>

        <!-- Login Settings Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-sign-in-alt"></i> Pengaturan Halaman Login</h2>
                <div class="header-actions">
                    <a href="{{ url('/login') }}" target="_blank" class="btn" style="background: var(--primary); color: white;">
                        <i class="fas fa-external-link-alt"></i> Preview Login
                    </a>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- Background Upload -->
                <div class="settings-card" style="background: #f8fafc; border-radius: 12px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-image" style="color: var(--primary);"></i> Background Halaman Login
                    </h4>
                    
                    <div style="background: #e2e8f0; border-radius: 10px; height: 180px; overflow: hidden; position: relative; margin-bottom: 15px;" id="bgPreviewContainer">
                        @if($loginSettings && $loginSettings->background_image)
                            <img src="{{ asset('storage/' . $loginSettings->background_image) }}" alt="Background" 
                                style="width: 100%; height: 100%; object-fit: cover;" id="bgPreviewImg">
                            <div style="position: absolute; top: 10px; right: 10px;">
                                <button type="button" onclick="deleteBackground()" class="btn" style="background: #ef4444; color: white; padding: 8px 12px; border-radius: 6px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #94a3b8;">
                                <i class="fas fa-image" style="font-size: 40px; margin-bottom: 10px;"></i>
                                <p style="margin: 0;">Belum ada background</p>
                            </div>
                        @endif
                    </div>
                    
                    <form id="formUploadBg" enctype="multipart/form-data">
                        <input type="file" name="background_image" id="bgInput" accept="image/*" style="display: none;">
                        <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer;" 
                            onclick="document.getElementById('bgInput').click()">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #64748b;"></i>
                            <p style="margin: 10px 0 0; color: #64748b;">Klik atau drag gambar (maks 5MB)</p>
                        </div>
                        <button type="button" onclick="uploadBackground()" class="btn" style="width: 100%; margin-top: 12px; background: var(--primary); color: white; padding: 12px;">
                            <i class="fas fa-upload"></i> Upload Background
                        </button>
                    </form>
                </div>

                <!-- Overlay Settings -->
                <div class="settings-card" style="background: #f8fafc; border-radius: 12px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-palette" style="color: var(--primary);"></i> Pengaturan Warna Overlay
                    </h4>
                    
                    <div id="overlayPreviewBox" style="background: linear-gradient(135deg, rgba(0,100,0,0.7), rgba(0,150,0,0.5)); border-radius: 10px; height: 100px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-weight: 600;">Preview Overlay</span>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label class="form-label">Warna Overlay Awal (Kiri Atas)</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="color" id="overlayColorPicker" value="#006400" style="width: 50px; height: 40px; border: none; cursor: pointer;">
                            <input type="range" id="overlayOpacity" min="0" max="100" value="70" style="flex: 1;">
                            <span id="opacityValue">70%</span>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label class="form-label">Warna Overlay Akhir (Kanan Bawah)</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="color" id="overlayColorEndPicker" value="#228b22" style="width: 50px; height: 40px; border: none; cursor: pointer;">
                            <input type="range" id="overlayOpacityEnd" min="0" max="100" value="50" style="flex: 1;">
                            <span id="opacityEndValue">50%</span>
                        </div>
                    </div>
                    
                    <button type="button" onclick="saveOverlaySettings()" class="btn" style="width: 100%; background: #10b981; color: white; padding: 12px;">
                        <i class="fas fa-save"></i> Simpan Pengaturan Overlay
                    </button>
                </div>

                <!-- Logo Upload -->
                <div class="settings-card" style="background: #f8fafc; border-radius: 12px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-school" style="color: var(--primary);"></i> Logo Sekolah
                    </h4>
                    
                    <div style="text-align: center; margin-bottom: 15px;">
                        <div style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 3px solid #e2e8f0; background: white;">
                            @if($loginSettings && $loginSettings->logo_image)
                                <img src="{{ asset('storage/' . $loginSettings->logo_image) }}" alt="Logo" 
                                    style="width: 100%; height: 100%; object-fit: contain;" id="logoPreviewImg">
                            @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                    <i class="fas fa-school" style="font-size: 40px;"></i>
                                </div>
                            @endif
                        </div>
                        @if($loginSettings && $loginSettings->logo_image)
                            <button type="button" onclick="deleteLogo()" style="margin-top: 10px; background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;">
                                <i class="fas fa-trash"></i> Hapus Logo
                            </button>
                        @endif
                    </div>
                    
                    <form id="formUploadLogo" enctype="multipart/form-data">
                        <input type="file" name="logo_image" id="logoInput" accept="image/*" style="display: none;">
                        <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer;" 
                            onclick="document.getElementById('logoInput').click()">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 20px; color: #64748b;"></i>
                            <p style="margin: 8px 0 0; color: #64748b; font-size: 13px;">Klik atau drag logo (maks 2MB)</p>
                        </div>
                        <button type="button" onclick="uploadLogo()" class="btn" style="width: 100%; margin-top: 12px; background: var(--primary); color: white; padding: 12px;">
                            <i class="fas fa-upload"></i> Upload Logo
                        </button>
                    </form>
                </div>

                <!-- Testing Date Settings -->
                <div class="settings-card" style="background: #f8fafc; border-radius: 12px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calendar-check" style="color: var(--primary);"></i> Pengaturan Testing Date
                    </h4>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label class="form-label">Tanggal Testing</label>
                        <input type="date" id="testingDate" class="form-control" value="{{ $loginSettings && $loginSettings->testing_date ? (is_string($loginSettings->testing_date) ? $loginSettings->testing_date : $loginSettings->testing_date->format('Y-m-d')) : '' }}">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px; display: flex; align-items: center; gap: 12px;">
                        <label class="modern-switch">
                            <input type="checkbox" id="testingActive" {{ ($loginSettings->testing_active ?? 'Tidak') === 'Ya' ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span>Aktifkan Testing Mode</span>
                    </div>
                    
                    <div style="background: #fef3c7; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 15px;">
                        <p style="margin: 0; font-size: 13px; color: #92400e;">
                            <i class="fas fa-info-circle"></i> Jika diaktifkan, sistem akan menggunakan tanggal testing sebagai tanggal hari ini.
                        </p>
                    </div>
                    
                    <button type="button" onclick="saveTestingDate()" class="btn" style="width: 100%; background: #f59e0b; color: white; padding: 12px;">
                        <i class="fas fa-save"></i> Simpan Testing Date
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Maintenance Mode -->
<div class="modal-overlay" id="modalMaintenance">
    <div class="modal-content" style="max-width: 550px;">
        <div class="modal-header">
            <h3><i class="fas fa-tools" style="color: #f59e0b;"></i> Pengaturan Maintenance Mode</h3>
            <button class="modal-close" onclick="closeModal('modalMaintenance')">&times;</button>
        </div>
        <div class="modal-body">
            <div style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 18px;"></i>
                    <strong style="color: #92400e;">Perhatian</strong>
                </div>
                <p style="color: #78350f; font-size: 13px; margin: 0;">Saat maintenance mode aktif, semua pengguna (guru, guru BK, siswa) tidak dapat mengakses sistem. Hanya admin yang tetap bisa login.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Status Maintenance</label>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <label class="toggle-switch" style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" id="maintenanceToggle" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #d1d5db; border-radius: 28px; transition: 0.3s;"></span>
                    </label>
                    <span id="maintenanceStatusLabel" style="font-weight: 600; font-size: 14px; color: #6b7280;">Non-Aktif</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Pesan Maintenance</label>
                <textarea id="maintenanceMessage" class="form-control" rows="3" placeholder="Tulis pesan yang akan ditampilkan kepada pengguna..." style="resize: vertical;">Sistem sedang dalam pemeliharaan. Silakan kembali beberapa saat lagi.</textarea>
            </div>

            <div id="maintenancePreviewLink" style="display: none; margin-top: 10px;">
                <a href="/" target="_blank" style="color: #6366f1; font-size: 13px; text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> Lihat halaman maintenance
                </a>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalMaintenance')">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="saveMaintenanceSettings()">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal Functions
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // Close modal when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ========== LOGIN SETTINGS FUNCTIONS ==========
    const csrfToken = '{{ csrf_token() }}';

    // Upload Background
    function uploadBackground() {
        const fileInput = document.getElementById('bgInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Pilih file gambar terlebih dahulu!');
            return;
        }
        
        const formData = new FormData();
        formData.append('background_image', fileInput.files[0]);
        
        fetch('{{ route("admin.login.upload-bg") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }

    // Delete Background
    function deleteBackground() {
        if (!confirm('Yakin hapus background?')) return;
        
        fetch('{{ route("admin.login.delete-bg") }}', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        });
    }

    // Upload Logo
    function uploadLogo() {
        const fileInput = document.getElementById('logoInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Pilih file logo terlebih dahulu!');
            return;
        }
        
        const formData = new FormData();
        formData.append('logo_image', fileInput.files[0]);
        
        fetch('{{ route("admin.login.upload-logo") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }

    // Delete Logo
    function deleteLogo() {
        if (!confirm('Yakin hapus logo?')) return;
        
        fetch('{{ route("admin.login.delete-logo") }}', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        });
    }

    // Overlay Preview Update
    function updateOverlayPreview() {
        const color1 = document.getElementById('overlayColorPicker').value;
        const opacity1 = document.getElementById('overlayOpacity').value / 100;
        const color2 = document.getElementById('overlayColorEndPicker').value;
        const opacity2 = document.getElementById('overlayOpacityEnd').value / 100;
        
        const rgba1 = hexToRgba(color1, opacity1);
        const rgba2 = hexToRgba(color2, opacity2);
        
        document.getElementById('overlayPreviewBox').style.background = 
            `linear-gradient(135deg, ${rgba1}, ${rgba2})`;
        
        document.getElementById('opacityValue').textContent = Math.round(opacity1 * 100) + '%';
        document.getElementById('opacityEndValue').textContent = Math.round(opacity2 * 100) + '%';
    }

    function hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Add event listeners for overlay preview
    document.getElementById('overlayColorPicker')?.addEventListener('input', updateOverlayPreview);
    document.getElementById('overlayOpacity')?.addEventListener('input', updateOverlayPreview);
    document.getElementById('overlayColorEndPicker')?.addEventListener('input', updateOverlayPreview);
    document.getElementById('overlayOpacityEnd')?.addEventListener('input', updateOverlayPreview);

    // Save Overlay Settings
    function saveOverlaySettings() {
        const color1 = document.getElementById('overlayColorPicker').value;
        const opacity1 = document.getElementById('overlayOpacity').value / 100;
        const color2 = document.getElementById('overlayColorEndPicker').value;
        const opacity2 = document.getElementById('overlayOpacityEnd').value / 100;
        
        const data = {
            overlay_color: hexToRgba(color1, opacity1),
            overlay_color_end: hexToRgba(color2, opacity2)
        };
        
        fetch('{{ route("admin.login.save-overlay") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
            } else {
                alert('Error: ' + result.message);
            }
        });
    }

    // Save Testing Date
    function saveTestingDate() {
        const data = {
            testing_date: document.getElementById('testingDate').value,
            testing_active: document.getElementById('testingActive').checked ? 'Ya' : 'Tidak'
        };
        
        fetch('{{ route("admin.testing-date.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
            } else {
                alert('Error: ' + result.message);
            }
        });
    }

    // ============ MAINTENANCE MODE ============
    function updateMaintenanceUI(isActive) {
        const toggle = document.getElementById('maintenanceToggle');
        const label = document.getElementById('maintenanceStatusLabel');
        const quickStatus = document.getElementById('maintenanceQuickStatus');
        const quickBadge = document.getElementById('maintenanceQuickBadge');
        const previewLink = document.getElementById('maintenancePreviewLink');
        const slider = toggle.nextElementSibling;

        toggle.checked = isActive;
        slider.style.background = isActive ? '#ef4444' : '#d1d5db';

        // Update slider circle
        if (!slider.querySelector('.slider-dot')) {
            const dot = document.createElement('span');
            dot.className = 'slider-dot';
            dot.style.cssText = 'position:absolute;width:22px;height:22px;background:white;border-radius:50%;top:3px;transition:0.3s;box-shadow:0 1px 3px rgba(0,0,0,0.2);';
            slider.appendChild(dot);
        }
        const dot = slider.querySelector('.slider-dot');
        dot.style.left = isActive ? '27px' : '3px';

        label.textContent = isActive ? 'Aktif' : 'Non-Aktif';
        label.style.color = isActive ? '#ef4444' : '#6b7280';

        if (quickStatus) {
            quickStatus.textContent = isActive ? 'Pengguna tidak dapat mengakses sistem' : 'Sistem berjalan normal';
        }
        if (quickBadge) {
            quickBadge.textContent = isActive ? 'AKTIF' : 'NON-AKTIF';
            quickBadge.style.background = isActive ? '#fef2f2' : '#f0fdf4';
            quickBadge.style.color = isActive ? '#ef4444' : '#10b981';
        }
        if (previewLink) {
            previewLink.style.display = isActive ? 'block' : 'none';
        }
    }

    function loadMaintenanceSettings() {
        fetch('{{ route("admin.maintenance.get") }}')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateMaintenanceUI(data.maintenance_mode == 1);
                    document.getElementById('maintenanceMessage').value = data.maintenance_message;
                }
            })
            .catch(() => {
                document.getElementById('maintenanceQuickStatus').textContent = 'Gagal memuat status';
            });
    }

    function openMaintenanceModal() {
        loadMaintenanceSettings();
        openModal('modalMaintenance');
    }

    function saveMaintenanceSettings() {
        const toggle = document.getElementById('maintenanceToggle');
        const message = document.getElementById('maintenanceMessage').value;

        fetch('{{ route("admin.maintenance.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                maintenance_mode: toggle.checked,
                maintenance_message: message
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateMaintenanceUI(data.maintenance_mode == 1);
                alert(data.message);
                closeModal('modalMaintenance');
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(() => alert('Gagal menyimpan pengaturan.'));
    }

    // Init on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadMaintenanceSettings();

        const toggle = document.getElementById('maintenanceToggle');
        if (toggle) {
            toggle.addEventListener('change', function() {
                updateMaintenanceUI(this.checked);
            });
        }
    });
</script>
@endpush
