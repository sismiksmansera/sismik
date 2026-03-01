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
    
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
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

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(8px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: linear-gradient(145deg, #1e1b4b, #0f172a);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 36px;
            max-width: 480px;
            width: 100%;
            position: relative;
            animation: modalIn 0.3s ease-out;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-close {
            position: absolute;
            top: 16px; right: 16px;
            background: rgba(255,255,255,0.1);
            border: none; color: #94a3b8;
            width: 36px; height: 36px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s;
        }
        .modal-close:hover { background: rgba(239,68,68,0.3); color: #ef4444; }
        .modal-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 24px;
            padding-right: 40px;
        }
        .modal-field {
            margin-bottom: 16px;
        }
        .modal-field .mf-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .modal-field .mf-value {
            font-size: 17px;
            font-weight: 600;
            color: #f1f5f9;
        }
        .modal-field .mf-value.mono {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }
        .modal-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 20px 0;
        }
        .modal-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 24px;
        }
        .modal-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }
        .modal-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
        .modal-btn.btn-download {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
        }
        .modal-btn.btn-exam {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        @media (max-width: 640px) {
            .container { padding: 20px 14px; }
            .page-header h1 { font-size: 24px; }
            .search-box { padding: 20px; }
            .search-input { padding: 15px 15px 15px 48px; font-size: 15px; }
            .result-card { padding: 18px; }
            .login-grid { grid-template-columns: 1fr; }
            .student-meta { flex-direction: column; gap: 4px; }
            .modal-box { padding: 24px; }
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

    <!-- Modal -->
    <div class="modal-overlay" id="cardModal" onclick="if(event.target===this)closeModal()">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            <div class="modal-title" id="modalTitle"></div>
            <div class="modal-field">
                <div class="mf-label">Nama</div>
                <div class="mf-value" id="modalNama"></div>
            </div>
            <div class="modal-field">
                <div class="mf-label">Username (NISN)</div>
                <div class="mf-value mono" id="modalNisn"></div>
            </div>
            <div class="modal-field">
                <div class="mf-label">Password</div>
                <div class="mf-value mono" id="modalPassword"></div>
            </div>
            <div id="modalLinkInfo" style="display:none;">
                <div class="modal-field">
                    <div class="mf-label">Link Ujian</div>
                    <div class="mf-value" style="color:#60a5fa; font-size:14px;">https://lamteng2.dsmartlampung.com</div>
                </div>
            </div>
            <div class="modal-divider"></div>
            <div class="modal-actions">
                <button class="modal-btn btn-download" onclick="generatePDF()"><i class="fas fa-file-pdf"></i> Download Kartu (PDF)</button>
                <a class="modal-btn btn-exam" id="modalExamLink" href="https://lamteng2.dsmartlampung.com" target="_blank" style="display:none;"><i class="fas fa-external-link-alt"></i> Masuk Ujian D-Smart</a>
            </div>
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
                            <div class="login-item dsmart" onclick="showCardModal('KARTU LOGIN UJIAN D-SMART', '${escapeAttr(item.nama_siswa)}', '${escapeAttr(item.nisn)}', '${escapeAttr(item.password_dsmart || '-')}', '#7c3aed', true)">
                                <div class="label"><i class="fas fa-key"></i> Password D-Smart</div>
                                <div class="value">${escapeHtml(item.password_dsmart || '-')}</div>
                                <div class="download-hint"><i class="fas fa-download"></i> Klik untuk download kartu</div>
                            </div>
                            <div class="login-item bimasoft" onclick="showCardModal('KARTU LOGIN UJIAN ONLINE BIMASOFT', '${escapeAttr(item.nama_siswa)}', '${escapeAttr(item.nisn)}', '${escapeAttr(item.password_bimasoft || '-')}', '#3b82f6', false)">
                                <div class="label"><i class="fas fa-key"></i> Password Bimasoft</div>
                                <div class="value">${escapeHtml(item.password_bimasoft || '-')}</div>
                                <div class="download-hint"><i class="fas fa-download"></i> Klik untuk download kartu</div>
                            </div>
                            <div class="login-item jihan" onclick="showCardModal('KARTU LOGIN AKSI JIHAN', '${escapeAttr(item.nama_siswa)}', '${escapeAttr(item.nisn)}', '${escapeAttr(item.password_aksi_jihan || '-')}', '#10b981', false)">
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

        // Modal state
        let currentCard = {};

        function showCardModal(judul, nama, nisn, password, color, isDsmart) {
            currentCard = { judul, nama, nisn, password, color, isDsmart };

            document.getElementById('modalTitle').textContent = judul;
            document.getElementById('modalTitle').style.color = color;
            document.getElementById('modalNama').textContent = nama;
            document.getElementById('modalNisn').textContent = nisn;
            document.getElementById('modalPassword').textContent = password;

            // D-Smart link & exam button
            document.getElementById('modalLinkInfo').style.display = isDsmart ? 'block' : 'none';
            document.getElementById('modalExamLink').style.display = isDsmart ? 'flex' : 'none';

            document.getElementById('cardModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('cardModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close on Escape
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: [140, 90] });

            const { judul, nama, nisn, password, color, isDsmart } = currentCard;

            // Background
            doc.setFillColor(30, 27, 75);
            doc.rect(0, 0, 140, 90, 'F');

            // Top accent bar
            const r = parseInt(color.slice(1,3),16);
            const g = parseInt(color.slice(3,5),16);
            const b = parseInt(color.slice(5,7),16);
            doc.setFillColor(r, g, b);
            doc.rect(0, 0, 140, 3, 'F');

            // Title
            doc.setTextColor(r, g, b);
            doc.setFontSize(13);
            doc.setFont('helvetica', 'bold');
            doc.text(judul, 70, 14, { align: 'center' });

            // Divider
            doc.setDrawColor(255, 255, 255, 30);
            doc.setLineWidth(0.2);
            doc.line(15, 18, 125, 18);

            // Labels
            const leftX = 15;
            const valX = 55;
            let y = 28;

            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(148, 163, 184);
            doc.text('Nama', leftX, y);
            doc.text(':', valX - 3, y);

            doc.setTextColor(241, 245, 249);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(11);
            doc.text(nama, valX, y);

            y += 12;
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(148, 163, 184);
            doc.text('Username (NISN)', leftX, y);
            doc.text(':', valX - 3, y);

            doc.setTextColor(241, 245, 249);
            doc.setFont('courier', 'bold');
            doc.setFontSize(11);
            doc.text(nisn, valX, y);

            y += 12;
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(148, 163, 184);
            doc.text('Password', leftX, y);
            doc.text(':', valX - 3, y);

            // Password with highlight
            doc.setFillColor(r, g, b, 50);
            const pwWidth = doc.getTextWidth(password) + 6;
            doc.roundedRect(valX - 2, y - 4, pwWidth + 2, 7, 1.5, 1.5, 'F');

            doc.setTextColor(r, g, b);
            doc.setFont('courier', 'bold');
            doc.setFontSize(11);
            doc.text(password, valX, y);

            // D-Smart link
            if (isDsmart) {
                y += 12;
                doc.setFontSize(8);
                doc.setFont('helvetica', 'normal');
                doc.setTextColor(148, 163, 184);
                doc.text('Link Ujian', leftX, y);
                doc.text(':', valX - 3, y);

                doc.setTextColor(96, 165, 250);
                doc.setFont('helvetica', 'bold');
                doc.textWithLink('https://lamteng2.dsmartlampung.com', valX, y, { url: 'https://lamteng2.dsmartlampung.com' });
            }

            // Footer
            doc.setFontSize(6);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(100, 116, 139);
            doc.text('SISMIK \u2014 Sistem Informasi Sekolah', 70, 86, { align: 'center' });

            // Save
            const filename = `${judul.replace(/\s+/g, '_')}_${nama.replace(/\s+/g, '_')}.pdf`;
            doc.save(filename);
        }
    </script>
</body>
</html>
