<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | SISMIK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: #f1f5f9;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        .bg-pattern {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .bg-pattern::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
            top: -200px; right: -200px;
            animation: float 8s ease-in-out infinite;
        }
        .bg-pattern::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%);
            bottom: -150px; left: -150px;
            animation: float 10s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }

        .maintenance-container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 600px;
            padding: 40px 30px;
        }

        /* Gear animation */
        .gear-container {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 40px;
        }
        .gear {
            position: absolute;
            color: #6366f1;
            animation: spin 6s linear infinite;
        }
        .gear-1 {
            font-size: 80px;
            top: 0; left: 15px;
        }
        .gear-2 {
            font-size: 55px;
            bottom: 5px; right: 10px;
            animation-direction: reverse;
            animation-duration: 4s;
            color: #8b5cf6;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #818cf8, #a78bfa, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .message-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 40px;
        }
        .message-card p {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        .message-card i {
            color: #f59e0b;
            margin-right: 8px;
        }

        .status-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            background: #f59e0b;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }
        .status-text {
            color: #f59e0b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .secret-input-container {
            display: none;
            margin-top: 30px;
            animation: fadeIn 0.5s ease;
        }
        .secret-input-container.show { display: block; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .secret-input {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 12px 18px;
            color: #f1f5f9;
            font-size: 0.9rem;
            width: 100%;
            max-width: 300px;
            text-align: center;
            outline: none;
            transition: border-color 0.3s;
        }
        .secret-input:focus { border-color: rgba(129, 140, 248, 0.6); }
        .secret-input::placeholder { color: #64748b; }
        .secret-btn {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .secret-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(99,102,241,0.4); }

        .footer {
            margin-top: 40px;
            color: #475569;
            font-size: 0.8rem;
        }

        @media (max-width: 640px) {
            .maintenance-container { padding: 30px 20px; }
            h1 { font-size: 1.8rem; }
            .subtitle { font-size: 0.95rem; }
            .gear-container { width: 100px; height: 100px; margin-bottom: 30px; }
            .gear-1 { font-size: 60px; }
            .gear-2 { font-size: 40px; }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>

    <div class="maintenance-container">
        <div class="gear-container" id="gearTrigger" style="cursor: default;" title="">
            <i class="fas fa-cog gear gear-1"></i>
            <i class="fas fa-cog gear gear-2"></i>
        </div>

        <div class="status-bar">
            <div class="status-dot"></div>
            <span class="status-text">Maintenance Mode</span>
        </div>

        <h1>Sedang Dalam Pemeliharaan</h1>

        <p class="subtitle">
            Kami sedang melakukan peningkatan sistem untuk memberikan<br>
            pengalaman yang lebih baik bagi Anda.
        </p>

        <div class="message-card">
            <p><i class="fas fa-info-circle"></i> {{ $message }}</p>
        </div>

        <!-- Hidden admin access - triggered by clicking gear 5 times -->
        <div class="secret-input-container" id="secretContainer">
            <p style="color: #64748b; font-size: 0.75rem; margin-bottom: 12px;">
                <i class="fas fa-lock" style="margin-right: 5px;"></i> Masukkan kunci akses admin
            </p>
            <input type="password" class="secret-input" id="secretKey" placeholder="Kunci akses..." onkeydown="if(event.key==='Enter')adminAccess()">
            <br>
            <button class="secret-btn" onclick="adminAccess()">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} SISMIK - Sistem Informasi Manajemen Akademik</p>
        </div>
    </div>

    <script>
        // Hidden admin access: click gear 5 times
        let clickCount = 0;
        let clickTimer = null;
        document.getElementById('gearTrigger').addEventListener('click', function() {
            clickCount++;
            clearTimeout(clickTimer);
            clickTimer = setTimeout(() => { clickCount = 0; }, 3000);
            if (clickCount >= 5) {
                document.getElementById('secretContainer').classList.add('show');
                document.getElementById('secretKey').focus();
                clickCount = 0;
            }
        });

        function adminAccess() {
            const key = document.getElementById('secretKey').value.trim();
            if (key) {
                window.location.href = '/admin-access/' + encodeURIComponent(key);
            }
        }
    </script>
</body>
</html>
