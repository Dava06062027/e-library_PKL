<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .temp-card {
            background: #fff;
            border: 2px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>📚 Perpustakaan Remen Maos</h1>
    <p>Verifikasi Email Anda</p>
</div>

<div class="content">
    <h2>Halo, {{ $name }}!</h2>

    <p>Terima kasih telah mendaftar sebagai member Perpustakaan Remen Maos. Untuk melanjutkan proses pendaftaran, silakan verifikasi email Anda dengan mengklik tombol di bawah ini:</p>

    <center>
        <a href="{{ $verificationUrl }}" class="btn">Verifikasi Email Saya</a>
    </center>

    <p style="font-size: 12px; color: #666;">
        Atau copy link berikut ke browser Anda:<br>
        <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
    </p>

    <hr style="border: 1px solid #ddd; margin: 30px 0;">

    <h3>Nomor Kartu Temporary Anda</h3>

    <div class="temp-card">
        <h2 style="color: #667eea; margin: 10px 0;">{{ $tempCardNumber }}</h2>
        <p style="margin: 5px 0; font-size: 14px;">Simpan nomor ini untuk akses sementara ke koleksi digital</p>
    </div>

    <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0;">
        <strong>💡 Info:</strong> Dengan nomor kartu temporary, Anda sudah bisa mengakses e-resources kami sambil menunggu approval!
    </div>

    <p><strong>Langkah selanjutnya:</strong></p>
    <ol>
        <li>Verifikasi email Anda (klik tombol di atas)</li>
        <li>Tim kami akan mereview dokumen Anda (1-3 hari kerja)</li>
        <li>Anda akan menerima email konfirmasi setelah disetujui</li>
    </ol>

    <p style="color: #999; font-size: 12px; margin-top: 30px;">
        Link verifikasi ini berlaku selama 24 jam. Jika Anda tidak mendaftar, abaikan email ini.
    </p>
</div>

<div class="footer">
    <p>&copy; 2025 Perpustakaan Remen Maos. All rights reserved.</p>
    <p>Jl. Perpustakaan No. 123, Surakarta, Central Java, Indonesia</p>
</div>
</body>
</html>
