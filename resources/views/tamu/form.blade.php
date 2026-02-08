<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Tamu | SISMIK</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a5d1a 0%, #2e8b2e 50%, #3cb371 100%);
            padding: 30px 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #1a5d1a 0%, #2e8b2e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .form-header .icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 30px;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .form-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .form-body {
            padding: 30px;
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item i {
            color: #16a34a;
        }

        .info-item span {
            color: #166534;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #dc2626;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #16a34a;
            background: white;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkbox-group:hover {
            background: #f0fdf4;
            border-color: #86efac;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #16a34a;
        }

        .checkbox-group span {
            font-weight: 500;
            color: #374151;
        }

        .conditional-fields {
            display: none;
            margin-top: 15px;
            padding: 20px;
            background: #fefce8;
            border-radius: 10px;
            border: 1px solid #fde047;
        }

        .conditional-fields.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .section-divider span {
            padding: 0 15px;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.3);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: white;
            color: #374151;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-back:hover {
            background: #f3f4f6;
            transform: translateY(-1px);
        }

        .form-actions {
            text-align: center;
        }

        .error-msg {
            background: #fee2e2;
            color: #b91c1c;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }

        .error-msg ul {
            margin: 10px 0 0 20px;
        }

        @media (max-width: 600px) {
            .info-bar {
                flex-direction: column;
                gap: 10px;
            }
            .form-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Form Kunjungan Tamu</h1>
                <p>Silakan isi data kunjungan Anda</p>
            </div>

            <div class="form-body">
                <!-- Info Bar -->
                <div class="info-bar">
                    <div class="info-item">
                        <i class="fas fa-calendar-day"></i>
                        <span>{{ $hari }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $tanggal }}</span>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="error-msg">
                        <strong><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tamu.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat <span class="required">*</span></label>
                        <textarea name="alamat" placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Nomor HP <span class="required">*</span></label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required>
                    </div>

                    <div class="form-group">
                        <label>Datang Sebagai <span class="required">*</span></label>
                        <select name="datang_sebagai" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriOptions as $option)
                                <option value="{{ $option }}" {{ old('datang_sebagai') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bertemu Dengan <span class="required">*</span></label>
                        <input type="text" name="bertemu_dengan" value="{{ old('bertemu_dengan') }}" placeholder="Nama orang yang ingin ditemui" required>
                    </div>

                    <div class="form-group">
                        <label>Keperluan <span class="required">*</span></label>
                        <textarea name="keperluan" placeholder="Jelaskan keperluan kunjungan Anda" required>{{ old('keperluan') }}</textarea>
                    </div>

                    <div class="section-divider">
                        <span>Informasi Dokumen</span>
                    </div>

                    <!-- Memberikan Dokumen -->
                    <div class="form-group">
                        <label class="checkbox-group" for="memberikan_dokumen">
                            <input type="checkbox" name="memberikan_dokumen" id="memberikan_dokumen" value="1" 
                                {{ old('memberikan_dokumen') ? 'checked' : '' }}
                                onchange="toggleFields('memberikan')">
                            <span>Saya akan memberikan barang/dokumen</span>
                        </label>

                        <div id="fields_memberikan" class="conditional-fields {{ old('memberikan_dokumen') ? 'active' : '' }}">
                            <div class="form-group">
                                <label>Jenis Dokumen/Barang</label>
                                <select name="jenis_dokumen_diberikan">
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($dokumenOptions as $option)
                                        <option value="{{ $option }}" {{ old('jenis_dokumen_diberikan') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Deskripsi Dokumen/Barang</label>
                                <textarea name="deskripsi_dokumen_diberikan" placeholder="Jelaskan dokumen/barang yang akan diberikan">{{ old('deskripsi_dokumen_diberikan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Meminta Dokumen -->
                    <div class="form-group">
                        <label class="checkbox-group" for="meminta_dokumen">
                            <input type="checkbox" name="meminta_dokumen" id="meminta_dokumen" value="1"
                                {{ old('meminta_dokumen') ? 'checked' : '' }}
                                onchange="toggleFields('meminta')">
                            <span>Saya akan meminta dokumen</span>
                        </label>

                        <div id="fields_meminta" class="conditional-fields {{ old('meminta_dokumen') ? 'active' : '' }}">
                            <div class="form-group">
                                <label>Jenis Dokumen yang Diminta</label>
                                <select name="jenis_dokumen_diminta">
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($dokumenOptions as $option)
                                        <option value="{{ $option }}" {{ old('jenis_dokumen_diminta') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Deskripsi Permintaan</label>
                                <textarea name="deskripsi_dokumen_diminta" placeholder="Jelaskan dokumen yang ingin diminta">{{ old('deskripsi_dokumen_diminta') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Kirim Data Kunjungan
                        </button>
                        <a href="{{ route('login') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFields(type) {
            const checkbox = document.getElementById(type === 'memberikan' ? 'memberikan_dokumen' : 'meminta_dokumen');
            const fields = document.getElementById('fields_' + type);
            
            if (checkbox.checked) {
                fields.classList.add('active');
            } else {
                fields.classList.remove('active');
            }
        }
    </script>
</body>
</html>
