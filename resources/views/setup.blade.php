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

                    <div class="upload-area" onclick="document.getElementById('sqlFileInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h4>Pilih File SQL</h4>
                        <p>Format: .sql | Maksimal: 50MB</p>
                        <div class="file-name" id="sqlFileName"></div>
                    </div>
                    <input type="file" name="sql_file" id="sqlFileInput" accept=".sql" style="display: none;" onchange="showSqlFileName(this)">

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

        function updateInstallBtn() {
            document.getElementById('btnInstall').disabled = !(connectionOk && fileSelected);

            // Update steps
            document.getElementById('step1').className = connectionOk ? 'step done' : 'step active';
            document.getElementById('step2').className = connectionOk ? (fileSelected ? 'step done' : 'step active') : 'step';
            document.getElementById('step3').className = (connectionOk && fileSelected) ? 'step active' : 'step';
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
                    showStatus('success', data.message);
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
                updateInstallBtn();
            }
        }

        // Show processing overlay on form submit
        document.getElementById('setupForm').addEventListener('submit', function(e) {
            document.getElementById('processingOverlay').classList.add('active');
        });
    </script>
</body>
</html>
