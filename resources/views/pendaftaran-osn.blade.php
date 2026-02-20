<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran OSN 2026 | SISMIK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e2e8f0;
        }

        /* ANIMATED BACKGROUND */
        .bg-pattern {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 40% 80%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            z-index: 0;
        }

        .page-wrapper {
            position: relative; z-index: 1;
            max-width: 800px; margin: 0 auto;
            padding: 30px 20px 60px;
        }

        /* HEADER */
        .osn-header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeInDown 0.6s ease;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .osn-logo {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 42px; color: white;
            margin: 0 auto 20px;
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.3);
            position: relative;
        }
        .osn-logo::after {
            content: '';
            position: absolute; inset: -3px;
            border-radius: 27px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
            z-index: -1; opacity: 0.5;
            filter: blur(10px);
        }

        .osn-header h1 {
            font-size: 32px; font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .osn-header p {
            color: #94a3b8; font-size: 15px;
        }
        .osn-header .school-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 8px 20px; border-radius: 25px;
            font-size: 13px; color: #60a5fa; font-weight: 600;
            margin-top: 15px;
        }

        /* CARD */
        .glass-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            animation: fadeInUp 0.5s ease;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-title {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 25px;
        }
        .card-title .icon-box {
            width: 45px; height: 45px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: white;
        }
        .card-title .icon-box.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .card-title .icon-box.green { background: linear-gradient(135deg, #10b981, #059669); }
        .card-title .icon-box.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .card-title .icon-box.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .card-title h2 { font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .card-title p { font-size: 13px; color: #94a3b8; margin-top: 2px; }

        /* SEARCH */
        .search-group {
            display: flex; gap: 12px;
        }
        .search-input {
            flex: 1; padding: 14px 18px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 12px; color: #f1f5f9;
            font-size: 15px; font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .search-input::placeholder { color: #64748b; }
        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .btn-search {
            padding: 14px 28px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; border: none; border-radius: 12px;
            font-weight: 600; font-size: 15px; cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.3s; font-family: 'Inter', sans-serif;
            white-space: nowrap;
        }
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
        }
        .btn-search:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* NOTIFICATION */
        .notif-box {
            padding: 16px 20px; border-radius: 14px;
            margin-top: 20px;
            display: flex; align-items: center; gap: 12px;
            animation: fadeInUp 0.4s ease;
        }
        .notif-box.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #34d399;
        }
        .notif-box.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
        }
        .notif-box i { font-size: 22px; }
        .notif-box .notif-text { flex: 1; }
        .notif-box .notif-text strong { display: block; font-size: 15px; }
        .notif-box .notif-text span { font-size: 13px; opacity: 0.8; }

        .btn-daftar {
            padding: 14px 30px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white; border: none; border-radius: 12px;
            font-weight: 700; font-size: 15px; cursor: pointer;
            display: inline-flex; align-items: center; gap: 10px;
            transition: all 0.3s; font-family: 'Inter', sans-serif;
            margin-top: 20px; width: 100%; justify-content: center;
        }
        .btn-daftar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
        }

        /* PROFILE HEADER */
        .profile-top {
            display: flex; align-items: center; gap: 20px;
            padding: 20px; border-radius: 16px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(148, 163, 184, 0.08);
            margin-bottom: 25px;
        }
        .profile-avatar-wrap {
            position: relative; flex-shrink: 0;
        }
        .profile-avatar {
            width: 80px; height: 80px;
            border-radius: 50%; overflow: hidden;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 28px;
            cursor: pointer;
            border: 3px solid rgba(59, 130, 246, 0.3);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .btn-edit-avatar {
            position: absolute; bottom: -4px; right: -4px;
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: 2px solid #1e293b;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: white; font-size: 11px;
            transition: transform 0.2s;
        }
        .btn-edit-avatar:hover { transform: scale(1.15); }
        .profile-info h3 { font-size: 20px; font-weight: 700; color: #f1f5f9; }
        .profile-info .meta-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
        .profile-info .meta-badge {
            padding: 4px 12px; border-radius: 8px;
            font-size: 11px; font-weight: 600;
        }
        .meta-badge.blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .meta-badge.green { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .meta-badge.purple { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }

        /* FORM */
        .form-section { margin-bottom: 30px; }
        .form-section-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 15px; font-weight: 700; color: #e2e8f0;
            margin-bottom: 18px; padding-bottom: 10px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        .form-section-title i { color: #60a5fa; font-size: 16px; }

        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: span 2; }

        .form-label {
            font-size: 12px; font-weight: 600;
            color: #94a3b8; text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 8px;
        }
        .form-label .required { color: #f87171; }

        .form-input, .form-select {
            padding: 12px 15px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 10px; color: #f1f5f9;
            font-size: 14px; font-family: 'Inter', sans-serif;
            transition: all 0.3s; width: 100%;
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .form-input:disabled, .form-input[readonly] {
            opacity: 0.6; cursor: not-allowed;
            background: rgba(15, 23, 42, 0.3);
        }
        .form-select {
            cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M6 8L1 3h10z' fill='%2394a3b8'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 35px;
        }
        .form-select option { background: #1e293b; color: #f1f5f9; }
        .form-hint { font-size: 11px; color: #64748b; margin-top: 5px; }
        .validation-error { color: #f87171; font-size: 12px; margin-top: 5px; display: none; }

        /* SUBMIT */
        .btn-submit {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white; border: none; border-radius: 14px;
            font-weight: 700; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all 0.3s; font-family: 'Inter', sans-serif;
            margin-top: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.35);
        }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* MODAL */
        .modal-overlay {
            display: none; position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 9999;
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: #1e293b; border-radius: 20px;
            width: 90%; max-width: 450px;
            border: 1px solid rgba(148, 163, 184, 0.15);
            overflow: hidden;
            animation: fadeInUp 0.3s ease;
        }
        .modal-header {
            padding: 20px 24px; display: flex;
            align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        .modal-header h3 { font-size: 16px; font-weight: 700; color: #f1f5f9; }
        .modal-close {
            background: none; border: none; color: #94a3b8;
            font-size: 20px; cursor: pointer; padding: 4px;
            transition: color 0.2s;
        }
        .modal-close:hover { color: #f87171; }
        .modal-body { padding: 24px; text-align: center; }

        /* Photo view modal */
        .photo-full {
            max-width: 100%; max-height: 60vh;
            border-radius: 12px; margin-bottom: 20px;
        }
        .photo-actions { display: flex; gap: 12px; justify-content: center; }
        .btn-modal {
            padding: 10px 24px; border-radius: 10px;
            font-weight: 600; font-size: 14px; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.2s; border: none;
            font-family: 'Inter', sans-serif;
        }
        .btn-modal.primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }
        .btn-modal.primary:hover { box-shadow: 0 4px 15px rgba(59,130,246,0.4); }
        .btn-modal.secondary {
            background: rgba(148, 163, 184, 0.15);
            color: #94a3b8;
        }
        .btn-modal.secondary:hover { background: rgba(148, 163, 184, 0.25); }

        /* Upload area */
        .upload-zone {
            border: 2px dashed rgba(148, 163, 184, 0.2);
            border-radius: 14px; padding: 30px;
            cursor: pointer; transition: all 0.3s;
            margin-bottom: 20px;
        }
        .upload-zone:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }
        .upload-zone i { font-size: 36px; color: #64748b; margin-bottom: 10px; }
        .upload-zone p { color: #94a3b8; font-size: 13px; }
        .upload-zone small { color: #64748b; font-size: 11px; }

        .upload-preview {
            display: none; margin-bottom: 20px;
        }
        .upload-preview img {
            width: 120px; height: 120px;
            border-radius: 50%; object-fit: cover;
            border: 3px solid rgba(59, 130, 246, 0.3);
            margin-bottom: 10px;
        }

        /* SUCCESS OVERLAY */
        .success-overlay {
            display: none; position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            z-index: 9999;
            justify-content: center; align-items: center;
        }
        .success-box {
            text-align: center; padding: 50px 40px;
            animation: fadeInUp 0.5s ease;
        }
        .success-icon {
            width: 100px; height: 100px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 48px; color: white;
            margin: 0 auto 25px;
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.3);
        }
        .success-box h2 { font-size: 28px; color: #f1f5f9; margin-bottom: 10px; }
        .success-box p { color: #94a3b8; font-size: 15px; max-width: 400px; margin: 0 auto; }

        .btn-back-login {
            display: inline-flex; align-items: center; gap: 8px;
            margin-top: 30px; padding: 14px 30px;
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #60a5fa; border-radius: 12px;
            text-decoration: none; font-weight: 600;
            transition: all 0.3s;
        }
        .btn-back-login:hover {
            background: rgba(59, 130, 246, 0.25);
            transform: translateY(-2px);
        }

        /* TOAST */
        .toast {
            position: fixed; top: 20px; right: 20px;
            padding: 16px 24px; border-radius: 12px;
            color: white; font-weight: 600;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            z-index: 10001; display: none;
            animation: slideInRight 0.4s ease;
        }
        .toast.error { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .toast.success-toast { background: linear-gradient(135deg, #10b981, #059669); }
        @keyframes slideInRight {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .page-wrapper { padding: 20px 15px 40px; }
            .osn-header h1 { font-size: 24px; }
            .osn-logo { width: 70px; height: 70px; font-size: 32px; border-radius: 18px; }
            .glass-card { padding: 20px; border-radius: 16px; }
            .search-group { flex-direction: column; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: span 1; }
            .profile-top { flex-direction: column; text-align: center; }
            .profile-avatar { width: 70px; height: 70px; font-size: 24px; }
            .profile-info .meta-badges { justify-content: center; }
            .success-box { padding: 40px 25px; }
            .success-box h2 { font-size: 22px; }
        }

        @media (max-width: 480px) {
            .osn-header h1 { font-size: 20px; }
            .glass-card { padding: 16px; }
            .btn-daftar, .btn-submit { font-size: 14px; padding: 13px 20px; }
        }

        /* SPINNER */
        .spinner {
            display: inline-block; width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white; border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .hidden { display: none !important; }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>

    <div class="page-wrapper">
        <!-- HEADER -->
        <div class="osn-header">
            <div class="osn-logo">
                <i class="fas fa-trophy"></i>
            </div>
            <h1>PENDAFTARAN OSN 2026</h1>
            <p>Olimpiade Sains Nasional Tingkat Sekolah</p>
            <div class="school-badge">
                <i class="fas fa-school"></i> SMAN 1 Seputih Raman
            </div>
        </div>

        <!-- STEP 1: SEARCH -->
        <div class="glass-card" id="searchCard">
            <div class="card-title">
                <div class="icon-box blue"><i class="fas fa-search"></i></div>
                <div>
                    <h2>Cari Data Siswa</h2>
                    <p>Masukkan NISN untuk mencari data Anda</p>
                </div>
            </div>

            <div class="search-group">
                <input type="text" class="search-input" id="nisnInput"
                       placeholder="Masukkan NISN Anda..." maxlength="20"
                       onkeypress="if(event.key==='Enter'){searchNISN();}">
                <button class="btn-search" id="btnSearch" onclick="searchNISN()">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>

            <div id="notifBox" class="notif-box hidden"></div>

            <button class="btn-daftar hidden" id="btnDaftar" onclick="showForm()">
                <i class="fas fa-clipboard-check"></i> DAFTAR OSN 2026
            </button>
        </div>

        <!-- STEP 2: FORM -->
        <div class="hidden" id="formSection">
            <div class="glass-card">
                <div class="card-title">
                    <div class="icon-box green"><i class="fas fa-user-edit"></i></div>
                    <div>
                        <h2>Form Pendaftaran OSN 2026</h2>
                        <p>Lengkapi data berikut untuk mendaftar</p>
                    </div>
                </div>

                <!-- Profile Header -->
                <div class="profile-top" id="profileTop"></div>

                <form id="osnForm" onsubmit="submitForm(event)">
                    <input type="hidden" name="siswa_id" id="siswaId">

                    <!-- Identitas -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-id-card"></i> Identitas Siswa
                        </div>
                        <div class="form-grid">
                            <div class="form-group full">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-input" id="fNama" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">NISN</label>
                                <input type="text" class="form-input" id="fNisn" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">NIS</label>
                                <input type="text" class="form-input" id="fNis" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-input" id="fJk" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Agama</label>
                                <input type="text" class="form-input" id="fAgama" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-input" name="tempat_lahir" id="fTempatLahir" placeholder="Masukkan tempat lahir">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-input" name="tgl_lahir" id="fTglLahir">
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-map-marker-alt"></i> Alamat
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Provinsi</label>
                                <input type="text" class="form-input" name="provinsi" id="fProvinsi" placeholder="Masukkan provinsi">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kota / Kabupaten</label>
                                <input type="text" class="form-input" name="kota" id="fKota" placeholder="Masukkan kota/kabupaten">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-input" name="kecamatan" id="fKecamatan" placeholder="Masukkan kecamatan">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kampung</label>
                                <input type="text" class="form-input" name="kelurahan" id="fKelurahan" placeholder="Masukkan kampung">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Dusun</label>
                                <input type="text" class="form-input" name="dusun" id="fDusun" placeholder="Masukkan nama dusun">
                            </div>
                            <div class="form-group">
                                <label class="form-label">RT / RW</label>
                                <input type="text" class="form-input" name="rt_rw" id="fRtRw" placeholder="Contoh: 001/002">
                            </div>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-address-book"></i> Kontak
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" class="form-input" name="email" id="fEmail"
                                       placeholder="email@contoh.com" required>
                                <span class="validation-error" id="errEmail"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label">No. HP <span class="required">*</span></label>
                                <input type="tel" class="form-input" name="nohp_siswa" id="fNohp"
                                       placeholder="08xxxxxxxxxx" required>
                                <span class="validation-error" id="errNohp"></span>
                            </div>
                        </div>
                    </div>

                    <!-- OSN -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-trophy"></i> Data OSN
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Mapel OSN 2026 <span class="required">*</span></label>
                                <select class="form-select" name="mapel_osn_2026" id="fMapelOsn" required>
                                    <option value="">-- Pilih Mapel --</option>
                                    <option value="Matematika">Matematika</option>
                                    <option value="Fisika">Fisika</option>
                                    <option value="Kimia">Kimia</option>
                                    <option value="Biologi">Biologi</option>
                                    <option value="Geografi">Geografi</option>
                                    <option value="Astronomi">Astronomi</option>
                                    <option value="Informatika">Informatika</option>
                                    <option value="Ekonomi">Ekonomi</option>
                                    <option value="Kebumian">Kebumian</option>
                                </select>
                                <span class="validation-error" id="errMapel"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Apakah Ikut OSN 2025? <span class="required">*</span></label>
                                <select class="form-select" name="ikut_osn_2025" id="fIkutOsn" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="validation-error" id="errIkutOsn"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmit">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- PHOTO VIEW MODAL -->
    <div class="modal-overlay" id="photoViewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-image"></i> Foto Profil</h3>
                <button class="modal-close" onclick="closeModal('photoViewModal')">&times;</button>
            </div>
            <div class="modal-body">
                <img id="photoViewImg" src="" alt="Foto Profil" class="photo-full">
                <div id="photoViewPlaceholder" style="width:150px;height:150px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-size:48px;font-weight:800;margin:0 auto 20px;"></div>
                <div class="photo-actions">
                    <button class="btn-modal primary" onclick="closeModal('photoViewModal');openModal('photoUploadModal')">
                        <i class="fas fa-camera"></i> Ganti Foto
                    </button>
                    <button class="btn-modal secondary" onclick="closeModal('photoViewModal')">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PHOTO UPLOAD MODAL -->
    <div class="modal-overlay" id="photoUploadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-camera"></i> Upload Foto Profil</h3>
                <button class="modal-close" onclick="closeModal('photoUploadModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-zone" id="uploadZone" onclick="document.getElementById('fotoFileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk pilih foto</p>
                    <small>Format: JPG, PNG, GIF — Maks. 2MB</small>
                </div>
                <input type="file" id="fotoFileInput" accept="image/*" style="display:none" onchange="previewUpload(this)">

                <div class="upload-preview" id="uploadPreview">
                    <img id="uploadPreviewImg" src="" alt="Preview">
                    <p style="color:#94a3b8;font-size:13px;margin-bottom:15px;" id="uploadFileName"></p>
                </div>

                <div class="photo-actions">
                    <button class="btn-modal primary" id="btnUploadFoto" onclick="uploadPhoto()" disabled>
                        <i class="fas fa-upload"></i> Upload
                    </button>
                    <button class="btn-modal secondary" onclick="resetUpload();closeModal('photoUploadModal')">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUCCESS OVERLAY -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-box">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Pendaftaran Berhasil!</h2>
            <p>Data OSN 2026 Anda telah berhasil disimpan. Terima kasih telah mendaftar.</p>
            <a href="{{ route('pendaftaran-osn') }}" class="btn-back-login">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast error" id="toastError"></div>
    <div class="toast success-toast" id="toastSuccess"></div>

    <script>
    let siswaData = null;

    function searchNISN() {
        const nisn = document.getElementById('nisnInput').value.trim();
        if (!nisn || nisn.length < 4) {
            showNotif('error', 'NISN Tidak Valid', 'Masukkan NISN minimal 4 digit');
            return;
        }

        const btn = document.getElementById('btnSearch');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Mencari...';

        fetch('{{ route("pendaftaran-osn.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nisn: nisn })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search"></i> Cari';

            if (data.found) {
                siswaData = data.siswa;
                showNotif('success', 'Data Ditemukan!', `${data.siswa.nama} - ${data.siswa.rombel_aktif || 'Belum ada rombel'}`);
                document.getElementById('btnDaftar').classList.remove('hidden');
            } else {
                siswaData = null;
                showNotif('error', 'Tidak Ditemukan', data.message);
                document.getElementById('btnDaftar').classList.add('hidden');
                document.getElementById('formSection').classList.add('hidden');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search"></i> Cari';
            showNotif('error', 'Error', 'Gagal menghubungi server');
        });
    }

    function showNotif(type, title, msg) {
        const box = document.getElementById('notifBox');
        box.className = `notif-box ${type}`;
        box.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <div class="notif-text">
                <strong>${title}</strong>
                <span>${msg}</span>
            </div>
        `;
        box.classList.remove('hidden');
    }

    function showForm() {
        if (!siswaData) return;
        const d = siswaData;
        document.getElementById('formSection').classList.remove('hidden');

        // Profile top with photo
        const hasPhoto = !!d.foto;
        const avatarContent = hasPhoto
            ? `<img src="${d.foto}" alt="${d.nama}" id="avatarImg">`
            : (d.initials || 'S');

        document.getElementById('profileTop').innerHTML = `
            <div class="profile-avatar-wrap">
                <div class="profile-avatar" onclick="openPhotoView()">
                    ${avatarContent}
                </div>
                <div class="btn-edit-avatar" onclick="openModal('photoUploadModal')" title="Ganti Foto">
                    <i class="fas fa-pen"></i>
                </div>
            </div>
            <div class="profile-info">
                <h3>${d.nama}</h3>
                <div class="meta-badges">
                    <span class="meta-badge blue"><i class="fas fa-id-badge"></i> ${d.nisn}</span>
                    <span class="meta-badge green"><i class="fas fa-graduation-cap"></i> ${d.rombel_aktif || '-'}</span>
                    <span class="meta-badge purple"><i class="fas fa-calendar"></i> Angkatan ${d.angkatan || '-'}</span>
                </div>
            </div>
        `;

        // Fill fields
        document.getElementById('siswaId').value = d.id;
        document.getElementById('fNama').value = d.nama || '';
        document.getElementById('fNisn').value = d.nisn || '';
        document.getElementById('fNis').value = d.nis || '';
        document.getElementById('fJk').value = d.jk || '';
        document.getElementById('fAgama').value = d.agama || '';
        document.getElementById('fTempatLahir').value = d.tempat_lahir || '';
        document.getElementById('fTglLahir').value = d.tgl_lahir_raw || '';
        document.getElementById('fProvinsi').value = d.provinsi || '';
        document.getElementById('fKota').value = d.kota || '';
        document.getElementById('fKecamatan').value = d.kecamatan || '';
        document.getElementById('fKelurahan').value = d.kelurahan || '';
        document.getElementById('fDusun').value = d.dusun || '';
        document.getElementById('fRtRw').value = d.rt_rw || '';
        document.getElementById('fEmail').value = d.email || '';
        document.getElementById('fNohp').value = d.nohp_siswa || '';

        if (d.mapel_osn_2026) document.getElementById('fMapelOsn').value = d.mapel_osn_2026;
        if (d.ikut_osn_2025) document.getElementById('fIkutOsn').value = d.ikut_osn_2025;

        setTimeout(() => {
            document.getElementById('formSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 200);
    }

    // Photo modals
    function openPhotoView() {
        if (!siswaData) return;
        const img = document.getElementById('photoViewImg');
        const placeholder = document.getElementById('photoViewPlaceholder');

        if (siswaData.foto) {
            img.src = siswaData.foto;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            img.style.display = 'none';
            placeholder.style.display = 'flex';
            placeholder.textContent = siswaData.initials || 'S';
        }
        openModal('photoViewModal');
    }

    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function previewUpload(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 2 * 1024 * 1024) {
                showToast('Ukuran file maksimal 2MB', 'error');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadPreviewImg').src = e.target.result;
                document.getElementById('uploadFileName').textContent = file.name;
                document.getElementById('uploadPreview').style.display = 'block';
                document.getElementById('uploadZone').style.display = 'none';
                document.getElementById('btnUploadFoto').disabled = false;
            };
            reader.readAsDataURL(file);
        }
    }

    function resetUpload() {
        document.getElementById('fotoFileInput').value = '';
        document.getElementById('uploadPreview').style.display = 'none';
        document.getElementById('uploadZone').style.display = 'block';
        document.getElementById('btnUploadFoto').disabled = true;
    }

    function uploadPhoto() {
        const fileInput = document.getElementById('fotoFileInput');
        if (!fileInput.files[0] || !siswaData) return;

        const formData = new FormData();
        formData.append('foto', fileInput.files[0]);
        formData.append('siswa_id', siswaData.id);

        const btn = document.getElementById('btnUploadFoto');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Uploading...';

        fetch('{{ route("pendaftaran-osn.upload-foto") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(r => r.json())
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-upload"></i> Upload';

            if (result.success) {
                siswaData.foto = result.foto_url;
                // Update avatar in profile
                const avatarDiv = document.querySelector('.profile-avatar');
                if (avatarDiv) {
                    avatarDiv.innerHTML = `<img src="${result.foto_url}" alt="${siswaData.nama}" id="avatarImg">`;
                }
                resetUpload();
                closeModal('photoUploadModal');
                showToast('Foto profil berhasil diperbarui!', 'success');
            } else {
                showToast(result.message || 'Gagal upload foto', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-upload"></i> Upload';
            showToast('Gagal menghubungi server', 'error');
        });
    }

    function submitForm(e) {
        e.preventDefault();
        clearErrors();

        const form = document.getElementById('osnForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        let hasError = false;
        if (!data.email) { showFieldError('errEmail', 'Email wajib diisi'); hasError = true; }
        if (!data.nohp_siswa) { showFieldError('errNohp', 'Nomor HP wajib diisi'); hasError = true; }
        if (!data.mapel_osn_2026) { showFieldError('errMapel', 'Pilih Mapel OSN'); hasError = true; }
        if (!data.ikut_osn_2025) { showFieldError('errIkutOsn', 'Pilih Ya/Tidak'); hasError = true; }
        if (hasError) return;

        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Menyimpan...';

        fetch('{{ route("pendaftaran-osn.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';

            if (result.success) {
                document.getElementById('successOverlay').style.display = 'flex';
            } else {
                if (result.errors) {
                    Object.entries(result.errors).forEach(([key, msgs]) => {
                        const errEl = document.getElementById('err' + key.charAt(0).toUpperCase() + key.slice(1));
                        if (errEl) showFieldError(errEl.id, msgs[0]);
                    });
                }
                showToast(result.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
            showToast('Gagal menghubungi server', 'error');
        });
    }

    function showFieldError(id, msg) {
        const el = document.getElementById(id);
        if (el) { el.textContent = msg; el.style.display = 'block'; }
    }

    function clearErrors() {
        document.querySelectorAll('.validation-error').forEach(el => {
            el.style.display = 'none'; el.textContent = '';
        });
    }

    function showToast(msg, type = 'error') {
        const toastId = type === 'success' ? 'toastSuccess' : 'toastError';
        const toast = document.getElementById(toastId);
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${msg}`;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    // Close modals on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        }
    });
    </script>
</body>
</html>
