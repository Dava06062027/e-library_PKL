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
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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
            background: #ffc107;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .request-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
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
    <h1>📄 Perpustakaan Remen Maos</h1>
    <p>Permintaan Dokumen Tambahan</p>
</div>

<div class="content">
    <h2>Halo, {{ $name }}</h2>

    <p>Terima kasih atas pendaftaran Anda sebagai member Perpustakaan Remen Maos.</p>

    <p>Setelah melakukan review awal terhadap dokumen yang Anda upload, kami memerlukan <strong>dokumen tambahan atau klarifikasi</strong> untuk melanjutkan proses verifikasi.</p>

    <h3>📋 Catatan dari Tim Kami:</h3>

    <div class="request-box">
        <p style="margin: 0; white-space: pre-line;">{{ $notes }}</p>
    </div>

    <div style="background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0;">
        <strong>💡 Info:</strong> Proses pendaftaran Anda akan dilanjutkan setelah kami menerima dokumen/klarifikasi yang diminta.
    </div>

    <h3>🔄 Langkah Selanjutnya:</h3>
    <ol>
        <li>Siapkan dokumen tambahan yang diminta</li>
        <li>Kirim dokumen melalui salah satu cara berikut:
            <ul>
                <li><strong>Email:</strong> support@perpusremenmaos.com (sertakan nomor temporary card Anda)</li>
                <li><strong>Upload via Website:</strong> (fitur upload akan segera tersedia)</li>
                <li><strong>Datang Langsung:</strong> Kunjungi perpustakaan kami di jam operasional</li>
            </ul>
        </li>
        <li>Tunggu konfirmasi dari tim kami (1-2 hari kerja setelah dokumen diterima)</li>
    </ol>

    <h3>📞 Butuh Bantuan?</h3>
    <p>Jika Anda memiliki pertanyaan atau memerlukan klarifikasi lebih lanjut:</p>
    <ul>
        <li><strong>Email:</strong> support@perpusremenmaos.com</li>
        <li><strong>Telepon:</strong> (0271) 123-4567</li>
        <li><strong>WhatsApp:</strong> +62 812-3456-7890</li>
        <li><strong>Jam Operasional:</strong> Senin-Jumat, 08:00-16:00 WIB</li>
    </ul>

    <p style="margin-top: 30px;">Terima kasih atas kerja sama dan kesabaran Anda. Kami menantikan dokumen tambahan dari Anda.</p>

    <p><strong>Salam,</strong><br>Tim Perpustakaan Remen Maos</p>
</div>

<div class="footer">
    <p>&copy; 2025 Perpustakaan Remen Maos. All rights reserved.</p>
    <p>Jl. Perpustakaan No. 123, Surakarta, Central Java, Indonesia</p>
</div>
</body>
</html>
