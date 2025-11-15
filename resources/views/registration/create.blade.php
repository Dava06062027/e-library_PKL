<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Member - Perpustakaan Remen Maos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }
        .registration-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .registration-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .upload-box {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-box:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .upload-box.has-file {
            border-color: #28a745;
            background-color: #f0f9f4;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="registration-card">
                <div class="registration-header">
                    <i class="bi bi-book-half" style="font-size: 3rem;"></i>
                    <h2 class="mt-3 mb-2">Daftar Sebagai Member</h2>
                    <p class="mb-0">Perpustakaan Remen Maos</p>
                </div>

                <div class="p-4 p-md-5">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('registration.store') }}" method="POST" enctype="multipart/form-data" id="registrationForm">
                        @csrf

                        <h5 class="mb-3"><i class="bi bi-person-circle me-2"></i>Data Pribadi</h5>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                <small class="text-muted">Email akan digunakan untuk login</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Upload Dokumen</h5>
                        <p class="text-muted small">Upload dokumen identitas dan bukti alamat (KTP/SIM/Kartu Pelajar/KK)</p>

                        <div class="mb-3">
                            <label class="form-label">Dokumen Identitas <span class="text-danger">*</span></label>
                            <div class="upload-box" id="id-upload-box" onclick="document.getElementById('id_document').click()">
                                <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #667eea;"></i>
                                <p class="mb-1 mt-2">Klik untuk upload</p>
                                <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                <p class="mb-0 mt-2 text-success fw-bold" id="id-filename" style="display: none;"></p>
                            </div>
                            <input type="file" class="d-none" id="id_document" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Bukti Alamat <span class="text-danger">*</span></label>
                            <div class="upload-box" id="address-upload-box" onclick="document.getElementById('address_proof').click()">
                                <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #667eea;"></i>
                                <p class="mb-1 mt-2">Klik untuk upload</p>
                                <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                <p class="mb-0 mt-2 text-success fw-bold" id="address-filename" style="display: none;"></p>
                            </div>
                            <input type="file" class="d-none" id="address_proof" name="address_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>

                        <hr class="my-4">

                        <!-- CAPTCHA -->
                        <div class="mb-4 d-flex justify-content-center">
                            <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui <a href="#" class="text-decoration-none">Syarat dan Ketentuan</a> perpustakaan
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Daftar Sekarang
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                Sudah punya akun? Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-white">
                <p class="mb-0">&copy; 2025 Perpustakaan Remen Maos. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Handle ID document upload
    document.getElementById('id_document').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('id-filename').textContent = '✓ ' + file.name;
            document.getElementById('id-filename').style.display = 'block';
            document.getElementById('id-upload-box').classList.add('has-file');
        }
    });

    // Handle address proof upload
    document.getElementById('address_proof').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('address-filename').textContent = '✓ ' + file.name;
            document.getElementById('address-filename').style.display = 'block';
            document.getElementById('address-upload-box').classList.add('has-file');
        }
    });

    // Form validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;

        if (password !== confirmation) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter!');
            return false;
        }
    });
</script>
</body>
</html>
