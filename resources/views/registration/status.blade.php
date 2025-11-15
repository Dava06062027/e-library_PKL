<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran - Perpustakaan Remen Maos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }
        .status-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .status-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .timeline {
            position: relative;
            padding: 30px 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 60px;
            padding-bottom: 30px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -10px;
            width: 2px;
            background: #e0e0e0;
        }
        .timeline-item:last-child::before {
            display: none;
        }
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0e0e0;
            color: #999;
        }
        .timeline-icon.active {
            background: #667eea;
            color: white;
        }
        .timeline-icon.completed {
            background: #28a745;
            color: white;
        }
        .timeline-icon.rejected {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="status-card">
                <div class="status-header">
                    <i class="bi bi-clipboard-check" style="font-size: 3rem;"></i>
                    <h2 class="mt-3 mb-2">Status Pendaftaran</h2>
                    <p class="mb-0">Perpustakaan Remen Maos</p>
                </div>

                <div class="p-4 p-md-5">
                    <!-- Info Member -->
                    <div class="mb-4">
                        <h5 class="mb-3">Informasi Pendaftaran</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama:</strong>
                                <p class="mb-0">{{ $registration->name }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Email:</strong>
                                <p class="mb-0">{{ $registration->email }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>No. Kartu Temporary:</strong>
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ $registration->temp_card_number }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Tanggal Daftar:</strong>
                                <p class="mb-0">{{ $registration->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Current Status -->
                    <div class="text-center mb-4">
                        <h5 class="mb-3">Status Saat Ini</h5>
                        <span class="badge {{ $registration->getStatusBadgeClass() }} fs-5 px-4 py-2">
                                {{ $registration->getStatusLabel() }}
                            </span>
                    </div>

                    <!-- Timeline Progress -->
                    <div class="timeline">
                        <!-- Step 1: Pendaftaran -->
                        <div class="timeline-item">
                            <div class="timeline-icon completed">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Pendaftaran Diterima</h6>
                                <small class="text-muted">{{ $registration->created_at->format('d M Y, H:i') }}</small>
                                <p class="mb-0 mt-2 small">Form pendaftaran berhasil dikirim</p>
                            </div>
                        </div>

                        <!-- Step 2: Email Verification -->
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $registration->email_verified_at ? 'completed' : ($registration->isPendingVerification() ? 'active' : '') }}">
                                <i class="bi {{ $registration->email_verified_at ? 'bi-check-lg' : 'bi-envelope' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Verifikasi Email</h6>
                                @if($registration->email_verified_at)
                                    <small class="text-muted">{{ $registration->email_verified_at->format('d M Y, H:i') }}</small>
                                    <p class="mb-0 mt-2 small text-success">✓ Email terverifikasi</p>
                                @else
                                    <p class="mb-0 mt-2 small text-warning">⏳ Menunggu verifikasi email</p>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Review Dokumen -->
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $registration->reviewed_at ? 'completed' : ($registration->isUnderReview() ? 'active' : '') }}">
                                <i class="bi {{ $registration->reviewed_at ? 'bi-check-lg' : 'bi-file-earmark-text' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Review Dokumen</h6>
                                @if($registration->reviewed_at)
                                    <small class="text-muted">{{ $registration->reviewed_at->format('d M Y, H:i') }}</small>
                                    <p class="mb-0 mt-2 small text-success">✓ Dokumen direview</p>
                                    @if($registration->review_notes)
                                        <div class="alert alert-info mt-2 mb-0">
                                            <small><strong>Catatan:</strong> {{ $registration->review_notes }}</small>
                                        </div>
                                    @endif
                                @elseif($registration->status === 'document_requested')
                                    <p class="mb-0 mt-2 small text-warning">⚠️ Dokumen tambahan diperlukan</p>
                                    @if($registration->review_notes)
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <small><strong>Catatan:</strong> {{ $registration->review_notes }}</small>
                                        </div>
                                    @endif
                                @else
                                    <p class="mb-0 mt-2 small text-muted">⏳ Menunggu review</p>
                                @endif
                            </div>
                        </div>

                        <!-- Step 4: Approval -->
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $registration->isApproved() ? 'completed' : ($registration->isRejected() ? 'rejected' : ($registration->isPendingApproval() ? 'active' : '')) }}">
                                <i class="bi {{ $registration->isApproved() ? 'bi-check-circle-fill' : ($registration->isRejected() ? 'bi-x-circle-fill' : 'bi-clock-history') }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Approval Akhir</h6>
                                @if($registration->isApproved())
                                    <small class="text-muted">{{ $registration->approved_at->format('d M Y, H:i') }}</small>
                                    <p class="mb-0 mt-2 small text-success">✓ Pendaftaran disetujui!</p>
                                    <div class="alert alert-success mt-2 mb-0">
                                        <small>Akun Anda sudah aktif. Silakan login untuk menggunakan layanan perpustakaan.</small>
                                    </div>
                                @elseif($registration->isRejected())
                                    <small class="text-muted">{{ $registration->reviewed_at->format('d M Y, H:i') }}</small>
                                    <p class="mb-0 mt-2 small text-danger">✗ Pendaftaran ditolak</p>
                                    @if($registration->rejection_reason)
                                        <div class="alert alert-danger mt-2 mb-0">
                                            <small><strong>Alasan:</strong> {{ $registration->rejection_reason }}</small>
                                        </div>
                                    @endif
                                @else
                                    <p class="mb-0 mt-2 small text-muted">⏳ Menunggu approval dari Officer/Admin</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        @if($registration->isApproved())
                            <a href="{{ route('login') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login Sekarang
                            </a>
                        @elseif($registration->isRejected())
                            <a href="{{ route('registration.create') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-arrow-repeat me-2"></i>Daftar Ulang
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-hourglass-split me-2"></i>Menunggu Proses
                            </button>
                        @endif

                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            Kembali ke Beranda
                        </a>
                    </div>

                    <!-- Contact Info -->
                    <div class="alert alert-info mt-4">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Butuh Bantuan?</h6>
                        <p class="mb-0 small">
                            Hubungi kami di <strong>support@perpusremenmaos.com</strong> atau
                            <strong>(0271) 123-4567</strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 text-white">
                <p class="mb-0">&copy; 2025 Perpustakaan Remen Maos. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
