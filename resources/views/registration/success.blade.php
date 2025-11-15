<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - Perpustakaan Remen Maos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .success-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        .temp-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg text-white" style="font-size: 3rem;"></i>
        </div>

        <h2 class="mb-3">Pendaftaran Berhasil!</h2>
        <p class="text-muted mb-4">
            Terima kasih telah mendaftar sebagai member Perpustakaan Remen Maos
        </p>

        @if(session('temp_card_number'))
            <div class="temp-card">
                <h5 class="mb-2">Nomor Kartu Temporary Anda</h5>
                <h3 class="mb-0 fw-bold">{{ session('temp_card_number') }}</h3>
                <small class="d-block mt-2 opacity-75">Simpan nomor ini untuk akses sementara</small>
            </div>
        @endif

        <div class="alert alert-info text-start">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Langkah Selanjutnya:</h6>
            <ol class="mb-0">
                <li class="mb-2">
                    <strong>Cek Email Anda</strong><br>
                    <small>Kami telah mengirim link verifikasi ke <strong>{{ session('email') }}</strong></small>
                </li>
                <li class="mb-2">
                    <strong>Klik Link Verifikasi</strong><br>
                    <small>Verifikasi email Anda dalam 24 jam</small>
                </li>
                <li class="mb-2">
                    <strong>Menunggu Review</strong><br>
                    <small>Tim kami akan mereview dokumen Anda (1-3 hari kerja)</small>
                </li>
                <li>
                    <strong>Approval Akhir</strong><br>
                    <small>Setelah disetujui, Anda akan menerima email konfirmasi</small>
                </li>
            </ol>
        </div>

        <div class="alert alert-success text-start">
            <strong><i class="bi bi-gift me-2"></i>Akses Sementara:</strong><br>
            <small>Dengan nomor kartu temporary, Anda sudah bisa mengakses koleksi digital kami!</small>
        </div>

        <hr class="my-4">

        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login Sekarang
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                Kembali ke Beranda
            </a>
        </div>

        <p class="text-muted small mt-4 mb-0">
            Tidak menerima email? Cek folder spam atau
            <a href="#" class="text-decoration-none">kirim ulang email verifikasi</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
