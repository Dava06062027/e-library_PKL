<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi - Perpustakaan Remen Maos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .verified-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
        }
        .verified-icon {
            width: 100px;
            height: 100px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: bounceIn 0.8s ease;
        }
        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="verified-card">
        <div class="verified-icon">
            <i class="bi bi-check-circle-fill text-white" style="font-size: 4rem;"></i>
        </div>

        <h2 class="mb-3">Email Berhasil Diverifikasi!</h2>

        <p class="text-muted mb-4">
            Email Anda telah berhasil diverifikasi. Pendaftaran Anda sekarang sedang dalam proses review oleh tim kami.
        </p>

        <div class="alert alert-info text-start">
            <h6 class="alert-heading"><i class="bi bi-clock-history me-2"></i>Status Saat Ini:</h6>
            <p class="mb-2"><strong>Sedang Direview</strong></p>
            <small class="text-muted">
                Tim kami akan memeriksa dokumen yang Anda upload. Proses ini biasanya memakan waktu 1-3 hari kerja.
            </small>
        </div>

        <div class="alert alert-success">
            <i class="bi bi-envelope-check me-2"></i>
            <strong>Kami akan mengirim email</strong> setelah review selesai
        </div>

        <hr class="my-4">

        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-success btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login ke Akun
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                Kembali ke Beranda
            </a>
        </div>

        <p class="text-muted small mt-4 mb-0">
            Punya pertanyaan?
            <a href="mailto:support@perpusremenmaos.com" class="text-decoration-none">Hubungi kami</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
