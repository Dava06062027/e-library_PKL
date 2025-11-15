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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
        .reason-box {
            background: #f8d7da;
            border: 2px solid #dc3545;
            padding: 20px;
            border-radius: 8px;
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
    <h1>📋 Perpustakaan Remen Maos</h1>
    <p>Pemberitahuan Pendaftaran</p>
</div>

<div class="content">
    <h2>Halo, {{ $name }}</h2>

    <p>Terima kasih atas minat Anda untuk bergabung sebagai member Perpustakaan Remen Maos.</p>

    <p>Setelah melakukan review, kami mohon maaf untuk menginformasikan bahwa <strong>pendaftaran Anda belum dapat disetujui</strong> pada saat ini.</p>

    <h3>📌 Alasan:</h3>

    <div class="reason-box">
        <p style="margin: 0; white-space: pre-line;">{{ $reason }}</p>
    </div>

    <div style="background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0;">
        <strong>💡 Info:</strong> Anda dapat mendaftar kembali setelah memenuhi persyaratan yang diperlukan.
    </div>

    <h3>🔄 Langkah Selanjutnya:</h3>
    <ul>
        <li>Perbaiki dokumen sesuai alasan penolakan di atas</li>
        <li>Pastikan semua persyaratan terpenuhi</li>
        <li>Daftar kembali melalui website kami</li>
    </ul>

    <center>
        <a href="{{ url('/register') }}" class="btn">Daftar Ulang</a>
    </center>

    <h3>📞 Butuh Bantuan?</h3>
    <p>Jika Anda memiliki pertanyaan atau memerlukan klarifikasi lebih lanjut, jangan ragu untuk menghubungi kami:</p>
    <ul>
        <li><strong>Email:</strong> support@perpusremenmaos.com</li>
        <li><strong>Telepon:</strong> (0271) 123-4567</li>
        <li><strong>Jam Operasional:</strong> Senin-Jumat, 08:00-16:00 WIB</li>
    </ul>

    <p style="margin-top: 30px;">Terima kasih atas pengertian Anda, dan kami berharap dapat melayani Anda di masa mendatang.</p>

    <p><strong>Salam,</strong><br>Tim Perpustakaan Remen Maos</p>
</div>

<div class="footer">
    <p>&copy; 2025 Perpustakaan Remen Maos. All rights reserved.</p>
    <p>Jl. Perpustakaan No. 123, Surakarta, Central Java, Indonesia</p>
</div>
</body>
</html>
