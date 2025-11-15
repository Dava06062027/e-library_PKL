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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success-box {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .credentials {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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
    <h1>🎉 Selamat!</h1>
    <p>Pendaftaran Anda Disetujui</p>
</div>

<div class="content">
    <h2>Halo, {{ $name }}!</h2>

    <div class="success-box">
        <h3 style="color: #28a745; margin: 10px 0;">✅ Pendaftaran Disetujui!</h3>
        <p style="margin: 5px 0;">Selamat datang sebagai member resmi Perpustakaan Remen Maos</p>
    </div>

    <p>Kami dengan senang hati menginformasikan bahwa pendaftaran Anda telah <strong>disetujui</strong> oleh tim kami. Akun Anda sekarang sudah aktif dan siap digunakan!</p>

    <h3>📋 Informasi Akun Anda</h3>

    <div class="credentials">
        <p style="margin: 5px 0;"><strong>Email:</strong> {{ $email }}</p>
        <p style="margin: 5px 0;"><strong>Password:</strong> (password yang Anda daftarkan)</p>
        <p style="margin: 5px 0;"><strong>No. Kartu Temporary:</strong> {{ $tempCardNumber }}</p>
    </div>

    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
        <strong>⚠️ Penting:</strong> Silakan login menggunakan email dan password yang Anda daftarkan. Untuk keamanan, kami tidak menyimpan atau mengirim password Anda.
    </div>

    <center>
        <a href="{{ url('/login') }}" class="btn">Login Sekarang</a>
    </center>

    <h3>🎁 Keuntungan Member Aktif:</h3>
    <ul>
        <li>Akses penuh ke koleksi fisik dan digital</li>
        <li>Peminjaman buku hingga 5 eksemplar</li>
        <li>Durasi peminjaman 14 hari (dapat diperpanjang)</li>
        <li>Akses ke ruang baca dan fasilitas perpustakaan</li>
        <li>Notifikasi koleksi baru</li>
    </ul>

    <h3>📚 Langkah Selanjutnya:</h3>
    <ol>
        <li>Login ke akun Anda</li>
        <li>Lengkapi profil (opsional)</li>
        <li>Mulai jelajahi koleksi kami</li>
        <li>Kunjungi perpustakaan untuk kartu member fisik</li>
    </ol>

    <p style="margin-top: 30px;">Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.</p>

    <p><strong>Selamat membaca! 📖</strong></p>
</div>

<div class="footer">
    <p>&copy; 2025 Perpustakaan Remen Maos. All rights reserved.</p>
    <p>Jl. Perpustakaan No. 123, Surakarta, Central Java, Indonesia</p>
    <p>Email: support@perpusremenmaos.com | Phone: (0271) 123-4567</p>
</div>
</body>
</html>
