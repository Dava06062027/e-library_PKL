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
            max-width: 700px;
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
        .step-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg text-white" style="font-size: 3rem;"></i>
        </div>

        <h2 class="mb-3">Akun Berhasil Dibuat!</h2>
        <p class="text-muted mb-4">
            Halo <strong>{{ session('name') }}</strong>, pendaftaran Anda telah berhasil!
        </p>

        @if(session('temp_card_number'))
            <div class="temp-card">
                <h5 class="mb-2">Nomor Kartu Temporary Anda</h5>
                <h3 class="mb-0 fw-bold">{{ session('temp_card_number') }}</h3>
                <small class="d-block mt-2 opacity-75">Simpan nomor ini untuk referensi</small>
            </div>
        @endif

        <div class="alert alert-warning text-start mt-4">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Penting!</h5>
            <p class="mb-0">
                <strong>Untuk mem-verifikasi akun, Anda harus datang ke Perpustakaan Remen Maos membawa kartu identitas (KTP atau Kartu Pelajar)</strong>
            </p>
        </div>

        <div class="text-start mt-4">
            <h6 class="mb-3"><i class="bi bi-list-check me-2"></i>Langkah Selanjutnya:</h6>

            <div class="step-card">
                <h6 class="mb-2"><i class="bi bi-1-circle-fill text-primary me-2"></i>Datang ke Perpustakaan</h6>
                <p class="mb-0 small text-muted">
                    Kunjungi Perpustakaan Remen Maos dengan membawa <strong>KTP atau Kartu Pelajar asli</strong>
                </p>
            </div>

            <div class="step-card">
                <h6 class="mb-2"><i class="bi bi-2-circle-fill text-primary me-2"></i>Verifikasi Data</h6>
                <p class="mb-0 small text-muted">
                    Petugas kami akan memverifikasi data Anda dengan KTP/Kartu Pelajar yang Anda bawa
                </p>
            </div>

            <div class="step-card">
                <h6 class="mb-2"><i class="bi bi-3-circle-fill text-primary me-2"></i>Aktivasi Akun</h6>
                <p class="mb-0 small text-muted">
                    Setelah verifikasi berhasil, akun Anda akan diaktifkan dan Anda bisa login
                </p>
            </div>

            <div class="step-card">
                <h6 class="mb-2"><i class="bi bi-4-circle-fill text-primary me-2"></i>Mulai Meminjam Buku</h6>
                <p class="mb-0 small text-muted">
                    Akun yang sudah terverifikasi dapat langsung meminjam buku di perpustakaan
                </p>
            </div>
        </div>

        <div class="alert alert-info mt-4 text-start">
            <h6 class="alert-heading"><i class="bi bi-geo-alt me-2"></i>Alamat Perpustakaan</h6>
            <p class="mb-1 small">
                <strong>Perpustakaan Remen Maos</strong><br>
                Jl. Perpustakaan No. 123, Surakarta, Jawa Tengah<br>
                <i class="bi bi-telephone me-1"></i>(0271) 123-4567<br>
                <i class="bi bi-clock me-1"></i>Senin - Jumat: 08.00 - 16.00 WIB
            </p>
        </div>

        <hr class="my-4">

        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Login
            </a>
        </div>

        <p class="text-muted small mt-4 mb-0">
            Butuh bantuan? Hubungi kami di <strong>support@perpusremenmaos.com</strong>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
