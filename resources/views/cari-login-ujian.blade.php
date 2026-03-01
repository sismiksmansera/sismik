<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cari Login Ujian | SISMIK</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            color: #e2e8f0;
        }

        /* Animated background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }
        .bg-animation .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.08);
            animation: float 20s infinite ease-in-out;
        }
        .bg-animation .circle:nth-child(1) { width: 400px; height: 400px; top: -100px; left: -100px; animation-delay: 0s; }
        .bg-animation .circle:nth-child(2) { width: 300px; height: 300px; bottom: -50px; right: -50px; animation-delay: 5s; background: rgba(59, 130, 246, 0.08); }
        .bg-animation .circle:nth-child(3) { width: 200px; height: 200px; top: 50%; left: 50%; animation-delay: 10s; background: rgba(16, 185, 129, 0.08); }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .page-header .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 20px;
        }
        .page-header .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #7c3aed, #3b82f6);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 8px 32px rgba(124, 58, 237, 0.4);
        }
        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #a78bfa, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-header p {
            color: #94a3b8;
            font-size: 15px;
            margin-top: 8px;
        }

        /* Search Box */
        .search-box {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s;
        }
        .search-box:focus-within {
            border-color: rgba(124, 58, 237, 0.5);
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.15);
        }
        .search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-input-wrapper i {
            position: absolute;
            left: 20px;
            color: #7c3aed;
            font-size: 20px;
        }
        .search-input {
            width: 100%;
            padding: 18px 20px 18px 56px;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: #f1f5f9;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        .search-input::placeholder { color: #64748b; }
        .search-input:focus {
            outline: none;
            border-color: #7c3aed;
            background: rgba(255, 255, 255, 0.12);
        }
        .search-hint {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            margin-top: 12px;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 40px;
            display: none;
        }
        .loading.show { display: block; }
        .loading .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(124, 58, 237, 0.2);
            border-top-color: #7c3aed;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 12px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Results */
        .results-count {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 16px;
            display: none;
        }
        .results-count.show { display: block; }
        .results-count strong { color: #a78bfa; }

        .result-cards {
            display: grid;
            gap: 16px;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s;
            animation: fadeInUp 0.4s ease-out;
        }
        .result-card:hover {
            transform: translateY(-2px);
            border-color: rgba(124, 58, 237, 0.3);
            box-shadow: 0 8px 32px rgba(124, 58, 237, 0.15);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-card .student-info {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .student-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #7c3aed, #3b82f6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .student-name {
            font-size: 18px;
            font-weight: 700;
            color: #f1f5f9;
        }
        .student-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .student-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .login-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .login-item {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }
        .login-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .login-item .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .login-item .value {
            font-family: 'Courier New', monospace;
            font-size: 15px;
            color: #e2e8f0;
            font-weight: 600;
            letter-spacing: 0.5px;
            word-break: break-all;
        }
        .login-item .download-hint {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .login-item:hover .download-hint {
            opacity: 1;
        }
        .login-item.dsmart { border-left: 3px solid #7c3aed; }
        .login-item.bimasoft { border-left: 3px solid #3b82f6; }
        .login-item.jihan { border-left: 3px solid #10b981; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
            display: none;
        }
        .empty-state.show { display: block; }
        .empty-state i {
            font-size: 56px;
            margin-bottom: 16px;
            color: #475569;
        }
        .empty-state h3 {
            font-size: 18px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .empty-state p {
            font-size: 14px;
        }

        /* Initial state */
        .initial-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        .initial-state i {
            font-size: 64px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #7c3aed, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .initial-state h3 {
            font-size: 18px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .initial-state p {
            font-size: 14px;
        }

        /* Back link */
        .back-link {
            text-align: center;
            margin-top: 40px;
        }
        .back-link a {
            color: #7c3aed;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .back-link a:hover { color: #a78bfa; }

        @media (max-width: 640px) {
            .container { padding: 20px 14px; }
            .page-header h1 { font-size: 24px; }
            .search-box { padding: 20px; }
            .search-input { padding: 15px 15px 15px 48px; font-size: 15px; }
            .result-card { padding: 18px; }
            .login-grid { grid-template-columns: 1fr; }
            .student-meta { flex-direction: column; gap: 4px; }
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
            <h1>Cari Kartu Login Ujian</h1>
            <p>Cari data login ujian D-Smart, Bimasoft, dan Aksi Jihan berdasarkan nama siswa</p>
        </div>

        <!-- Search Box -->
        <div class="search-box">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="Ketik nama siswa di sini..." autocomplete="off" autofocus>
            </div>
            <p class="search-hint">Masukkan minimal 2 karakter untuk mulai mencari</p>
        </div>

        <!-- Loading indicator -->
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Mencari data...</p>
        </div>

        <!-- Results count -->
        <div class="results-count" id="resultsCount"></div>

        <!-- Initial state -->
        <div class="initial-state" id="initialState">
            <i class="fas fa-search"></i>
            <h3>Mulai Pencarian</h3>
            <p>Ketik nama siswa pada kolom pencarian di atas untuk menemukan data login ujian</p>
        </div>

        <!-- Empty state -->
        <div class="empty-state" id="emptyState">
            <i class="fas fa-user-slash"></i>
            <h3>Data Tidak Ditemukan</h3>
            <p>Tidak ada siswa dengan nama yang sesuai. Coba gunakan nama lain.</p>
        </div>

        <!-- Result cards -->
        <div class="result-cards" id="resultCards"></div>

        <!-- Back to login -->
        <div class="back-link">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke halaman login</a>
        </div>
    </div>

    <script>
        let searchTimeout = null;
        const searchInput = document.getElementById('searchInput');
        const loading = document.getElementById('loading');
        const resultsCount = document.getElementById('resultsCount');
        const initialState = document.getElementById('initialState');
        const emptyState = document.getElementById('emptyState');
        const resultCards = document.getElementById('resultCards');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                resetState();
                return;
            }

            searchTimeout = setTimeout(() => doSearch(query), 400);
        });

        function resetState() {
            loading.classList.remove('show');
            resultsCount.classList.remove('show');
            emptyState.classList.remove('show');
            resultCards.innerHTML = '';
            initialState.style.display = 'block';
        }

        function doSearch(query) {
            initialState.style.display = 'none';
            emptyState.classList.remove('show');
            resultsCount.classList.remove('show');
            resultCards.innerHTML = '';
            loading.classList.add('show');

            fetch('{{ route("cari-login-ujian.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ query: query }),
            })
            .then(res => res.json())
            .then(result => {
                loading.classList.remove('show');

                if (!result.data || result.data.length === 0) {
                    emptyState.classList.add('show');
                    return;
                }

                resultsCount.innerHTML = `Ditemukan <strong>${result.count}</strong> hasil untuk "<strong>${escapeHtml(query)}</strong>"`;
                resultsCount.classList.add('show');

                result.data.forEach((item, i) => {
                    const card = document.createElement('div');
                    card.className = 'result-card';
                    card.style.animationDelay = (i * 0.05) + 's';

                    const initial = item.nama_siswa.charAt(0).toUpperCase();

                    card.innerHTML = `
                        <div class="student-info">
                            <div class="student-avatar">${initial}</div>
                            <div>
                                <div class="student-name">${escapeHtml(item.nama_siswa)}</div>
                                <div class="student-meta">
                                    <span><i class="fas fa-school"></i> ${escapeHtml(item.kelas)}</span>
                                    <span><i class="fas fa-id-badge"></i> NISN: ${escapeHtml(item.nisn)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="login-grid">
                            <div class="login-item dsmart" onclick="downloadCard('KARTU LOGIN UJIAN D-SMART', '${escapeAttr(item.nama_siswa)}', '${escapeAttr(item.nisn)}', '${escapeAttr(item.password_dsmart || '-')}', '#7c3aed', '#a78bfa')">
                                <div class="label"><i class="fas fa-key"></i> Password D-Smart</div>
                                <div class="value">${escapeHtml(item.password_dsmart || '-')}</div>
                                <div class="download-hint"><i class="fas fa-download"></i> Klik untuk download kartu</div>
                            </div>
                            <div class="login-item bimasoft" onclick="downloadCard('KARTU LOGIN UJIAN ONLINE BIMASOFT', '${escapeAttr(item.nama_siswa)}', '${escapeAttr(item.nisn)}', '${escapeAttr(item.password_bimasoft || '-')}', '#3b82f6', '#60a5fa')">
                                <div class="label"><i class="fas fa-key"></i> Password Bimasoft</div>
                                <div class="value">${escapeHtml(item.password_bimasoft || '-')}</div>
                                <div class="download-hint"><i class="fas fa-download"></i> Klik untuk download kartu</div>
                            </div>
                            <div class="login-item jihan" onclick="downloadCard('KARTU LOGIN AKSI JIHAN', '${escapeAttr(item.nama_siswa)}', '${escapeAttr(item.nisn)}', '${escapeAttr(item.password_aksi_jihan || '-')}', '#10b981', '#34d399')">
                                <div class="label"><i class="fas fa-key"></i> Password Aksi Jihan</div>
                                <div class="value">${escapeHtml(item.password_aksi_jihan || '-')}</div>
                                <div class="download-hint"><i class="fas fa-download"></i> Klik untuk download kartu</div>
                            </div>
                        </div>
                    `;
                    resultCards.appendChild(card);
                });
            })
            .catch(err => {
                loading.classList.remove('show');
                console.error(err);
                emptyState.classList.add('show');
                emptyState.querySelector('h3').textContent = 'Terjadi Kesalahan';
                emptyState.querySelector('p').textContent = 'Gagal memuat data. Silakan coba lagi.';
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function escapeAttr(text) {
            return String(text).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        function downloadCard(judul, nama, nisn, password, color1, color2) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const w = 800;
            const h = 450;
            canvas.width = w;
            canvas.height = h;

            // Background gradient
            const bgGrad = ctx.createLinearGradient(0, 0, w, h);
            bgGrad.addColorStop(0, '#1e1b4b');
            bgGrad.addColorStop(1, '#0f172a');
            ctx.fillStyle = bgGrad;
            ctx.fillRect(0, 0, w, h);

            // Accent bar top
            const barGrad = ctx.createLinearGradient(0, 0, w, 0);
            barGrad.addColorStop(0, color1);
            barGrad.addColorStop(1, color2);
            ctx.fillStyle = barGrad;
            ctx.fillRect(0, 0, w, 8);

            // Decorative circle
            ctx.beginPath();
            ctx.arc(w - 80, 80, 120, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255,255,255,0.03)';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(60, h - 60, 80, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255,255,255,0.02)';
            ctx.fill();

            // Title
            ctx.fillStyle = color2;
            ctx.font = 'bold 28px Poppins, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(judul, w / 2, 60);

            // Divider line
            ctx.strokeStyle = 'rgba(255,255,255,0.1)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(100, 85);
            ctx.lineTo(w - 100, 85);
            ctx.stroke();

            // Content area
            const startY = 140;
            const leftX = 80;
            const valueX = 300;

            // Labels
            ctx.textAlign = 'left';
            ctx.font = '600 18px Poppins, Arial, sans-serif';
            ctx.fillStyle = '#94a3b8';
            ctx.fillText('Nama', leftX, startY);
            ctx.fillText('Username (NISN)', leftX, startY + 70);
            ctx.fillText('Password', leftX, startY + 140);

            // Colon
            ctx.fillText(':', valueX - 20, startY);
            ctx.fillText(':', valueX - 20, startY + 70);
            ctx.fillText(':', valueX - 20, startY + 140);

            // Values
            ctx.font = 'bold 22px Poppins, Arial, sans-serif';
            ctx.fillStyle = '#f1f5f9';
            ctx.fillText(nama, valueX, startY);
            ctx.fillText(nisn, valueX, startY + 70);

            // Password with highlight box
            const pwText = password;
            const pwMetrics = ctx.measureText(pwText);
            const pwBoxPad = 16;
            ctx.fillStyle = color1 + '33';
            roundRect(ctx, valueX - pwBoxPad/2, startY + 140 - 24, pwMetrics.width + pwBoxPad, 36, 8);
            ctx.fill();
            ctx.strokeStyle = color1;
            ctx.lineWidth = 1.5;
            roundRect(ctx, valueX - pwBoxPad/2, startY + 140 - 24, pwMetrics.width + pwBoxPad, 36, 8);
            ctx.stroke();

            ctx.fillStyle = color2;
            ctx.font = 'bold 22px "Courier New", monospace';
            ctx.fillText(pwText, valueX, startY + 140);

            // Footer
            ctx.fillStyle = 'rgba(255,255,255,0.15)';
            ctx.font = '12px Poppins, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('SISMIK — Sistem Informasi Sekolah', w / 2, h - 25);

            // Download
            const link = document.createElement('a');
            link.download = `${judul.replace(/\s+/g, '_')}_${nama.replace(/\s+/g, '_')}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        }

        function roundRect(ctx, x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.quadraticCurveTo(x + w, y, x + w, y + r);
            ctx.lineTo(x + w, y + h - r);
            ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
            ctx.lineTo(x + r, y + h);
            ctx.quadraticCurveTo(x, y + h, x, y + h - r);
            ctx.lineTo(x, y + r);
            ctx.quadraticCurveTo(x, y, x + r, y);
            ctx.closePath();
        }
    </script>
</body>
</html>
