<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Berhasil - SISMIK</title>
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
        .success-container {
            text-align: center;
            max-width: 480px;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 44px;
            color: white;
            box-shadow: 0 15px 50px rgba(16, 185, 129, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 15px 50px rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 15px 50px rgba(16, 185, 129, 0.6); }
        }
        h1 { color: #f1f5f9; font-size: 28px; font-weight: 800; margin-bottom: 10px; }
        p { color: #94a3b8; font-size: 15px; line-height: 1.6; margin-bottom: 30px; }
        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 40px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1>Instalasi Berhasil! 🎉</h1>
        <p>
            Database telah berhasil dikonfigurasi dan data telah diimport.
            <br>Aplikasi SISMIK siap digunakan.
        </p>
        <a href="{{ url('/') }}" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Masuk ke SISMIK
        </a>
    </div>
</body>
</html>
