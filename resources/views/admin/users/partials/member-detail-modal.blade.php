<div class="modal fade" id="modalMemberDetail" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Detail Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Member Data -->
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary"><i class="bi bi-person-circle me-2"></i>Informasi Pribadi</h6>

                        <div class="mb-3 text-center">
                            <img id="detail-photo" src="" alt="Photo" class="rounded-circle border border-3 border-primary" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>

                        <table class="table table-sm table-bordered">
                            <tr>
                                <th width="35%" class="bg-light">Nama Lengkap</th>
                                <td id="detail-name">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Email</th>
                                <td id="detail-email">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">NIK</th>
                                <td id="detail-nik">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">No. Telepon</th>
                                <td id="detail-phone">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tanggal Lahir</th>
                                <td id="detail-birth-date">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Alamat</th>
                                <td id="detail-address">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Role</th>
                                <td><span id="detail-role" class="badge">-</span></td>
                            </tr>
                        </table>

                        <hr>

                        <h6 class="mb-3 text-success"><i class="bi bi-check-circle me-2"></i>Informasi Keanggotaan</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th width="35%" class="bg-light">Disetujui Oleh</th>
                                <td id="detail-approved-by">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tanggal Disetujui</th>
                                <td id="detail-approved-at">-</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status Verifikasi</th>
                                <td id="detail-status">-</td>
                            </tr>
                        </table>

                        <hr>

                        <h6 class="mb-3 text-warning"><i class="bi bi-card-image me-2"></i>Dokumen KTP</h6>
                        <div id="detail-ktp-container" class="text-center">
                            <img id="detail-ktp" src="" alt="KTP" class="img-fluid border rounded" style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Right Column: Member Card -->
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary"><i class="bi bi-credit-card me-2"></i>Kartu Member</h6>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Kartu member dibuat secara otomatis berdasarkan data yang diinput</small>
                        </div>

                        <!-- Member Card Preview -->
                        <div id="member-card-container" class="border rounded shadow-sm p-3 bg-white">
                            <div id="member-card" style="width: 800px; height: 500px; transform: scale(0.62); transform-origin: top left; position: relative; background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%); border-radius: 16px; overflow: hidden;">
                                <!-- Background accents -->
                                <div style="position: absolute; bottom: 0; left: 0; width: 300px; height: 180px; background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%); clip-path: polygon(0 0, 100% 50%, 100% 100%, 0 100%);"></div>

                                <!-- Star Logo (simplified) -->
                                <div style="position: absolute; top: 40px; left: 40px;">
                                    <div style="width: 60px; height: 60px; background: white; clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);"></div>
                                </div>

                                <!-- Library Title -->
                                <div style="position: absolute; top: 120px; left: 80px;">
                                    <div style="color: white; font-size: 24px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">PERPUSTAKAAN NASIONAL</div>
                                    <div style="color: white; font-size: 18px; margin-top: 5px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">REPUBLIK INDONESIA</div>
                                </div>

                                <!-- UMUM Badge -->
                                <div style="position: absolute; top: 60px; right: 100px; background: rgba(0,0,0,0.3); padding: 8px 24px; border-radius: 8px;">
                                    <div style="color: #ffd700; font-size: 24px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">UMUM</div>
                                </div>

                                <!-- Member Number -->
                                <div style="position: absolute; top: 120px; right: 40px; color: white; font-size: 32px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);" id="card-member-id">
                                    17111300175
                                </div>

                                <!-- Member Name -->
                                <div style="position: absolute; bottom: 200px; left: 80px;">
                                    <div style="color: #1a1a1a; font-size: 42px; font-weight: bold; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);" id="card-member-name">
                                        NASRUL MAKDIS
                                    </div>
                                </div>

                                <!-- Barcode representation -->
                                <div style="position: absolute; bottom: 80px; left: 80px;">
                                    <svg width="500" height="60" id="card-barcode">
                                        <!-- Barcode will be generated here -->
                                    </svg>
                                    <div style="color: #1a1a1a; font-size: 16px; margin-top: 5px; letter-spacing: 4px;" id="card-nik-text">
                                        * 1 7 1 1 1 3 0 0 1 7 5 *
                                    </div>
                                </div>

                                <!-- Member Photo -->
                                <div style="position: absolute; top: 160px; right: 40px; width: 200px; height: 240px; border: 4px solid white; border-radius: 8px; overflow: hidden; background: #e0e0e0; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                                    <img id="card-photo" src="" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>

                                <!-- Validity -->
                                <div style="position: absolute; bottom: 60px; right: 40px; color: #1a1a1a; font-size: 14px;">
                                    <div style="font-weight: normal;">Masa Berlaku Kartu</div>
                                    <div style="font-weight: bold; font-size: 16px;">Seumur Hidup</div>
                                </div>

                                <!-- Pusnas Logo Placeholders -->
                                <div style="position: absolute; bottom: 200px; right: 40px; display: flex; flex-direction: column; gap: 8px;">
                                    <div style="width: 60px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 4px;"></div>
                                    <div style="width: 60px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 4px;"></div>
                                    <div style="width: 60px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 4px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-grid gap-2">
                            <button type="button" class="btn btn-primary" id="btn-download-card">
                                <i class="bi bi-download me-2"></i>Download Kartu Member
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btn-print-card">
                                <i class="bi bi-printer me-2"></i>Print Kartu Member
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- CDN Libraries untuk Member Card -->
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Download card as image
            document.getElementById('btn-download-card')?.addEventListener('click', function() {
                const card = document.getElementById('member-card');

                html2canvas(card, {
                    scale: 2,
                    backgroundColor: null,
                    logging: false
                }).then(canvas => {
                    const link = document.createElement('a');
                    const nik = document.getElementById('detail-nik').textContent;
                    link.download = 'kartu-member-' + (nik !== '-' ? nik : 'unknown') + '.png';
                    link.href = canvas.toDataURL();
                    link.click();
                });
            });

            // Print card
            document.getElementById('btn-print-card')?.addEventListener('click', function() {
                const card = document.getElementById('member-card');

                html2canvas(card, {
                    scale: 2,
                    backgroundColor: null,
                    logging: false
                }).then(canvas => {
                    const printWindow = window.open('', '', 'width=800,height=600');
                    printWindow.document.write('<html><head><title>Print Kartu Member</title></head><body style="margin:0;padding:20px;">');
                    printWindow.document.write('<img src="' + canvas.toDataURL() + '" style="width: 100%; max-width: 800px;">');
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.focus();

                    setTimeout(() => {
                        printWindow.print();
                        printWindow.close();
                    }, 250);
                });
            });
        });
    </script>
@endpush

<style>
    #member-card-container {
        max-width: 100%;
        overflow: hidden;
    }
</style>

<!-- Right Column: Member Card -->
<div class="col-md-6">
    <h6 class="mb-3 text-primary"><i class="bi bi-credit-card me-2"></i>Kartu Member</h6>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <small>Kartu member dibuat secara otomatis berdasarkan data yang diinput</small>
    </div>

    <!-- Member Card Preview -->
    <div id="member-card-container" class="border rounded shadow-sm p-3 bg-white">
        <div id="member-card" style="width: 800px; height: 500px; transform: scale(0.62); transform-origin: top left; position: relative; background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%); border-radius: 16px; overflow: hidden;">
            <!-- Background accents -->
            <div style="position: absolute; bottom: 0; left: 0; width: 300px; height: 180px; background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%); clip-path: polygon(0 0, 100% 50%, 100% 100%, 0 100%);"></div>

            <!-- Star Logo (simplified) -->
            <div style="position: absolute; top: 40px; left: 40px;">
                <div style="width: 60px; height: 60px; background: white; clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);"></div>
            </div>

            <!-- Library Title -->
            <div style="position: absolute; top: 120px; left: 80px;">
                <div style="color: white; font-size: 24px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">PERPUSTAKAAN NASIONAL</div>
                <div style="color: white; font-size: 18px; margin-top: 5px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">REPUBLIK INDONESIA</div>
            </div>

            <!-- UMUM Badge -->
            <div style="position: absolute; top: 60px; right: 100px; background: rgba(0,0,0,0.3); padding: 8px 24px; border-radius: 8px;">
                <div style="color: #ffd700; font-size: 24px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">UMUM</div>
            </div>

            <!-- Member Number -->
            <div style="position: absolute; top: 120px; right: 40px; color: white; font-size: 32px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);" id="card-member-id">
                17111300175
            </div>

            <!-- Member Name -->
            <div style="position: absolute; bottom: 200px; left: 80px;">
                <div style="color: #1a1a1a; font-size: 42px; font-weight: bold; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);" id="card-member-name">
                    NASRUL MAKDIS
                </div>
            </div>

            <!-- Barcode representation -->
            <div style="position: absolute; bottom: 80px; left: 80px;">
                <svg width="500" height="60" id="card-barcode">
                    <!-- Barcode will be generated here -->
                </svg>
                <div style="color: #1a1a1a; font-size: 16px; margin-top: 5px; letter-spacing: 4px;" id="card-nik-text">
                    * 1 7 1 1 1 3 0 0 1 7 5 *
                </div>
            </div>

            <!-- Member Photo -->
            <div style="position: absolute; top: 160px; right: 40px; width: 200px; height: 240px; border: 4px solid white; border-radius: 8px; overflow: hidden; background: #e0e0e0; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                <img id="card-photo" src="" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <!-- Validity -->
            <div style="position: absolute; bottom: 60px; right: 40px; color: #1a1a1a; font-size: 14px;">
                <div style="font-weight: normal;">Masa Berlaku Kartu</div>
                <div style="font-weight: bold; font-size: 16px;">Seumur Hidup</div>
            </div>

            <!-- Pusnas Logo Placeholders -->
            <div style="position: absolute; bottom: 200px; right: 40px; display: flex; flex-direction: column; gap: 8px;">
                <div style="width: 60px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 4px;"></div>
                <div style="width: 60px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 4px;"></div>
                <div style="width: 60px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 4px;"></div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-grid gap-2">
        <button type="button" class="btn btn-primary" id="btn-download-card">
            <i class="bi bi-download me-2"></i>Download Kartu Member
        </button>
        <button type="button" class="btn btn-outline-secondary" id="btn-print-card">
            <i class="bi bi-printer me-2"></i>Print Kartu Member
        </button>
    </div>
</div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Download card as image
        document.getElementById('btn-download-card').addEventListener('click', function() {
            const card = document.getElementById('member-card');

            html2canvas(card, {
                scale: 2,
                backgroundColor: null,
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'kartu-member-' + document.getElementById('detail-nik').textContent + '.png';
                link.href = canvas.toDataURL();
                link.click();
            });
        });

        // Print card
        document.getElementById('btn-print-card').addEventListener('click', function() {
            const card = document.getElementById('member-card');

            html2canvas(card, {
                scale: 2,
                backgroundColor: null,
                logging: false
            }).then(canvas => {
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.write('<html><head><title>Print Kartu Member</title></head><body>');
                printWindow.document.write('<img src="' + canvas.toDataURL() + '" style="width: 100%; max-width: 800px;">');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            });
        });
    });
</script>

<style>
    #member-card-container {
        max-width: 100%;
        overflow: hidden;
    }
</style>
