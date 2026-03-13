<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup SISMIK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-container {
            width: 100%;
            max-width: 560px;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .setup-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            color: white;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
        }
        .setup-header h1 { color: #f1f5f9; font-size: 28px; font-weight: 800; }
        .setup-header p { color: #94a3b8; font-size: 14px; margin-top: 6px; }

        .setup-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .card-body { padding: 32px; }

        .step-indicator {
            display: flex;
            gap: 8px;
            margin-bottom: 28px;
        }
        .step {
            flex: 1;
            height: 4px;
            border-radius: 4px;
            background: #334155;
            transition: background 0.4s;
        }
        .step.active { background: #10b981; }
        .step.done { background: #059669; }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .section-title .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            flex-shrink: 0;
        }
        .section-title h2 { color: #f1f5f9; font-size: 18px; font-weight: 700; }
        .section-title p { color: #94a3b8; font-size: 12px; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .form-row.full { grid-template-columns: 1fr; }

        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 14px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            color: #f1f5f9;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        .form-group input::placeholder { color: #475569; }

        .upload-area {
            background: #0f172a;
            border: 2px dashed #334155;
            border-radius: 14px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-area:hover { border-color: #10b981; background: rgba(16, 185, 129, 0.05); }
        .upload-area i { font-size: 32px; color: #10b981; margin-bottom: 8px; }
        .upload-area h4 { color: #e2e8f0; font-size: 14px; margin-bottom: 4px; }
        .upload-area p { color: #64748b; font-size: 12px; }
        .upload-area .file-name {
            margin-top: 10px;
            padding: 8px 14px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            color: #10b981;
            font-weight: 600;
            font-size: 13px;
            display: none;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }
        .btn-test {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            margin-bottom: 14px;
        }
        .btn-test:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); }

        .btn-install {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        .btn-install:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3); }
        .btn-install:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

        .btn-secondary {
            background: #334155;
            color: #e2e8f0;
            margin-bottom: 14px;
        }

        .btn-fix {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            margin-top: 12px;
            padding: 10px 14px;
            font-size: 13px;
        }
        .btn-fix:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3); }
        .btn-fix:disabled { opacity: 0.6; cursor: not-allowed; }

        .status-box {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 13px;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .status-box.success { display: flex; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; }
        .status-box.error { display: flex; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .status-box.loading { display: flex; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #93c5fd; }
        .status-box.warning { display: flex; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #fcd34d; }

        .error-box {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            font-size: 13px;
        }

        .divider {
            height: 1px;
            background: #334155;
            margin: 24px 0;
        }

        /* Server Health Table */
        .health-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .health-table td {
            padding: 8px 10px;
            font-size: 13px;
            border-bottom: 1px solid rgba(51,65,85,0.5);
        }
        .health-table td:first-child {
            color: #94a3b8;
            font-weight: 500;
            width: 40%;
        }
        .health-table td:nth-child(2) {
            color: #f1f5f9;
            font-family: 'Fira Code', monospace;
            font-weight: 600;
        }
        .health-table td:last-child {
            width: 32px;
            text-align: center;
        }
        .badge-ok { color: #10b981; }
        .badge-fail { color: #ef4444; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.2);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Processing overlay */
        .processing-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            flex-direction: column;
            gap: 20px;
        }
        .processing-overlay.active { display: flex; }
        .processing-overlay .spinner-lg {
            width: 48px;
            height: 48px;
            border: 3px solid rgba(16, 185, 129, 0.2);
            border-top-color: #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        .processing-overlay p { color: #94a3b8; font-size: 16px; font-weight: 600; }
        .processing-overlay small { color: #64748b; font-size: 13px; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <div class="setup-logo">
                <i class="fas fa-cogs"></i>
            </div>
            <h1>Setup SISMIK</h1>
            <p>Konfigurasi database untuk menggunakan aplikasi</p>
        </div>

        <div class="setup-card">
            <div class="card-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step" id="step0"></div>
                    <div class="step active" id="step1"></div>
                    <div class="step" id="step2"></div>
                    <div class="step" id="step3"></div>
                </div>

                <!-- Server Errors -->
                @if($errors->any())
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                @if($reason === 'empty')
                <div class="error-box">
                    <i class="fas fa-database"></i> Database ditemukan tetapi kosong. Silakan import file SQL.
                </div>
                @endif

                <!-- ===================== -->
                <!-- SERVER HEALTH CHECK   -->
                <!-- ===================== -->
                @if(isset($serverChecks))
                <div class="section-title">
                    <div class="icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <h2>Kesiapan Server</h2>
                        <p>Pengecekan batas konfigurasi PHP untuk proses import database</p>
                    </div>
                </div>

                @php $allOk = !$serverChecks['needs_fix']; @endphp

                @if($allOk)
                    <div class="status-box success">
                        <i class="fas fa-check-circle"></i>
                        <div>Server siap! Semua konfigurasi PHP memenuhi syarat.</div>
                    </div>
                @else
                    <div class="status-box warning" style="display:flex; flex-direction:column; align-items:flex-start;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Beberapa konfigurasi PHP belum optimal</strong>
                        </div>
                        <div style="font-size:12px;opacity:0.9;">
                            Batas upload/memori server terlalu rendah untuk import database besar. Klik tombol di bawah untuk memperbaiki otomatis.
                        </div>
                    </div>
                @endif

                <table class="health-table">
                    @foreach($serverChecks['checks'] as $directive => $check)
                    <tr>
                        <td>{{ $directive }}</td>
                        <td>{{ $check['current'] }} {{ $check['unit'] }} <span style="color:#64748b;font-weight:400;">/ {{ $check['required'] }} {{ $check['unit'] }}</span></td>
                        <td>
                            @if($check['ok'])
                                <i class="fas fa-check-circle badge-ok"></i>
                            @else
                                <i class="fas fa-times-circle badge-fail"></i>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>

                @if(!$allOk)
                    <button type="button" class="btn btn-fix" id="btnFixLimits" onclick="fixServerLimits()">
                        <i class="fas fa-wrench"></i> Perbaiki Otomatis
                    </button>
                    <div id="fixResult" style="margin-top:10px;"></div>
                @endif

                @if($fixApplied ?? false)
                    <div class="status-box success" style="display:flex;margin-top:12px;">
                        <i class="fas fa-info-circle"></i>
                        <div>File <code style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:4px;">.user.ini</code> telah dibuat. Jika nilai belum berubah, <strong>refresh halaman ini</strong> (tekan F5) atau tunggu beberapa saat.</div>
                    </div>
                @endif

                <div class="divider"></div>
                @endif

                <!-- Connection Status -->
                <div class="status-box" id="connectionStatus"></div>

                <form action="{{ url('/setup/install') }}" method="POST" enctype="multipart/form-data" id="setupForm">
                    @csrf

                    <!-- Section 1: Database Connection -->
                    <div class="section-title">
                        <div class="icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <h2>Koneksi MySQL</h2>
                            <p>Konfigurasi koneksi ke server database</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Host</label>
                            <input type="text" name="db_host" id="dbHost" value="{{ old('db_host', $defaults['db_host']) }}" placeholder="127.0.0.1">
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="text" name="db_port" id="dbPort" value="{{ old('db_port', $defaults['db_port']) }}" placeholder="3306">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="db_username" id="dbUsername" value="{{ old('db_username', $defaults['db_username']) }}" placeholder="root">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="db_password" id="dbPassword" value="{{ old('db_password', $defaults['db_password']) }}" placeholder="Kosongkan jika tidak ada">
                        </div>
                    </div>

                    <button type="button" class="btn btn-test" id="btnTestConnection" onclick="testConnection()">
                        <i class="fas fa-plug"></i> Test Koneksi
                    </button>

                    <div class="divider"></div>

                    <!-- Section 2: Database Name -->
                    <div class="section-title">
                        <div class="icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <h2>Nama Database</h2>
                            <p>Database akan dibuat otomatis jika belum ada</p>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label>Nama Database</label>
                            <input type="text" name="db_database" id="dbDatabase" value="{{ old('db_database', $defaults['db_database']) }}" placeholder="simas_db">
                        </div>
                    </div>

                    <div class="divider"></div>

                    <!-- Section 3: SQL Import -->
                    <div class="section-title">
                        <div class="icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <div>
                            <h2>Import Database</h2>
                            <p>Upload file SQL untuk mengisi struktur dan data</p>
                        </div>
                    </div>

                    @php
                        $uploadMaxMB = intval(min(
                            (function($s) { $l=strtolower(substr($s,-1)); $v=(int)$s; return match($l){'g'=>$v*1024,'m'=>$v,'k'=>$v/1024,default=>$v/1048576}; })(ini_get('upload_max_filesize')),
                            (function($s) { $l=strtolower(substr($s,-1)); $v=(int)$s; return match($l){'g'=>$v*1024,'m'=>$v,'k'=>$v/1024,default=>$v/1048576}; })(ini_get('post_max_size'))
                        ));
                        if ($uploadMaxMB < 1) $uploadMaxMB = 2;
                    @endphp

                    <div class="upload-area" onclick="document.getElementById('sqlFileInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h4>Pilih File SQL</h4>
                        <p>Format: .sql | Maksimal: {{ $uploadMaxMB }} MB</p>
                        <div class="file-name" id="sqlFileName"></div>
                    </div>
                    <input type="file" name="sql_file" id="sqlFileInput" accept=".sql" style="display: none;" onchange="showSqlFileName(this)">

                    <!-- File too big warning -->
                    <div id="fileSizeWarning" style="display:none;margin-top:10px;padding:12px 16px;border-radius:10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:13px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="fileSizeMsg"></span>
                    </div>

                    <div style="height: 20px;"></div>

                    <button type="submit" class="btn btn-install" id="btnInstall" disabled>
                        <i class="fas fa-rocket"></i> Install SISMIK
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-lg"></div>
        <p>Menginstall SISMIK...</p>
        <small>Membuat database dan mengimport data. Mohon tunggu...</small>
    </div>

    <script>
        let connectionOk = false;
        let fileSelected = false;
        let fileOk = false;
        const maxUploadMB = {{ $uploadMaxMB }};

        function updateInstallBtn() {
            document.getElementById('btnInstall').disabled = !(connectionOk && fileSelected && fileOk);

            // Update steps
            const serverOk = {{ ($serverChecks['needs_fix'] ?? false) ? 'false' : 'true' }};
            document.getElementById('step0').className = serverOk ? 'step done' : 'step active';
            document.getElementById('step1').className = connectionOk ? 'step done' : (serverOk ? 'step active' : 'step');
            document.getElementById('step2').className = connectionOk ? (fileSelected ? 'step done' : 'step active') : 'step';
            document.getElementById('step3').className = (connectionOk && fileSelected && fileOk) ? 'step active' : 'step';
        }

        function showStatus(type, message) {
            const box = document.getElementById('connectionStatus');
            box.className = 'status-box ' + type;
            const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'times-circle' : 'circle-notch fa-spin');
            box.innerHTML = '<i class="fas fa-' + icon + '"></i> ' + message;
        }

        async function testConnection() {
            const btn = document.getElementById('btnTestConnection');
            btn.innerHTML = '<div class="spinner"></div> Testing...';
            btn.disabled = true;

            showStatus('loading', 'Menguji koneksi...');

            try {
                const response = await fetch('{{ url("/setup/test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        db_host: document.getElementById('dbHost').value,
                        db_port: document.getElementById('dbPort').value,
                        db_username: document.getElementById('dbUsername').value,
                        db_password: document.getElementById('dbPassword').value,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    connectionOk = true;
                    let msg = data.message;
                    if (data.max_allowed_packet_mb && data.max_allowed_packet_mb < 50) {
                        msg += ' ⚠️ max_allowed_packet: ' + data.max_allowed_packet_mb + 'MB (akan dinaikkan otomatis saat install)';
                    }
                    showStatus('success', msg);
                } else {
                    connectionOk = false;
                    showStatus('error', data.message);
                }
            } catch (e) {
                connectionOk = false;
                showStatus('error', 'Error: ' + e.message);
            }

            btn.innerHTML = '<i class="fas fa-plug"></i> Test Koneksi';
            btn.disabled = false;
            updateInstallBtn();
        }

        function showSqlFileName(input) {
            if (input.files && input.files[0]) {
                const el = document.getElementById('sqlFileName');
                const file = input.files[0];
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                el.textContent = '📄 ' + file.name + ' (' + sizeMB + ' MB)';
                el.style.display = 'block';
                fileSelected = true;

                // Check file size against actual server limit
                const warningDiv = document.getElementById('fileSizeWarning');
                if (parseFloat(sizeMB) > maxUploadMB) {
                    fileOk = false;
                    document.getElementById('fileSizeMsg').innerHTML =
                        'File <strong>' + sizeMB + ' MB</strong> melebihi batas upload server <strong>' + maxUploadMB + ' MB</strong>. ' +
                        'Klik tombol <strong>"Perbaiki Otomatis"</strong> di bagian Kesiapan Server di atas, lalu <strong>refresh halaman</strong>, kemudian pilih file lagi.';
                    warningDiv.style.display = 'block';
                } else {
                    fileOk = true;
                    warningDiv.style.display = 'none';
                }
                updateInstallBtn();
            }
        }

        // Fix server limits via AJAX
        async function fixServerLimits() {
            const btn = document.getElementById('btnFixLimits');
            const resultDiv = document.getElementById('fixResult');
            btn.disabled = true;
            btn.innerHTML = '<div class="spinner" style="width:14px;height:14px;"></div> Memperbaiki...';

            try {
                const response = await fetch('{{ url("/setup/fix-limits") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await response.json();

                if (data.success) {
                    resultDiv.innerHTML =
                        '<div class="status-box success" style="display:flex;">' +
                        '<i class="fas fa-check-circle"></i>' +
                        '<div><strong>Berhasil!</strong> File .user.ini telah dibuat. Halaman akan refresh dalam 3 detik untuk menerapkan perubahan...</div>' +
                        '</div>';
                    setTimeout(() => location.reload(), 3000);
                } else {
                    resultDiv.innerHTML =
                        '<div class="status-box error" style="display:flex;flex-direction:column;align-items:flex-start;">' +
                        '<div><i class="fas fa-times-circle"></i> ' + data.message + '</div>' +
                        '<div style="font-size:12px;margin-top:8px;opacity:0.9;">Solusi manual: jalankan perintah ini di terminal server:<br>' +
                        '<code style="display:block;margin-top:6px;padding:8px;background:rgba(0,0,0,0.3);border-radius:6px;word-break:break-all;">' +
                        'echo "upload_max_filesize = 100M\\npost_max_size = 105M\\nmemory_limit = 512M\\nmax_execution_time = 600" | sudo tee /etc/php/$(php -r "echo PHP_MAJOR_VERSION.\\\".\\\".PHP_MINOR_VERSION;")/fpm/conf.d/99-sismik.ini && sudo systemctl restart php*-fpm' +
                        '</code></div>' +
                        '</div>';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-wrench"></i> Coba Lagi';
                }
            } catch (e) {
                resultDiv.innerHTML =
                    '<div class="status-box error" style="display:flex;">' +
                    '<i class="fas fa-times-circle"></i> Error: ' + e.message +
                    '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-wrench"></i> Coba Lagi';
            }
        }

        // Show processing overlay on form submit
        document.getElementById('setupForm').addEventListener('submit', function(e) {
            document.getElementById('processingOverlay').classList.add('active');
        });

        // Initial step update
        updateInstallBtn();
    </script>
</body>
</html>
