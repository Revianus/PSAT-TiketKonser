<?php
require_once 'koneksi.php';

$database = new Database();
$booking = new Booking($database);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit();
}

$bookingData = $booking->getBookingById($id);

if (!$bookingData) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Tiket Konser - <?php echo htmlspecialchars($bookingData['nama_pelanggan']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --success-color: #059669;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .receipt-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .receipt-header-content {
            position: relative;
            z-index: 2;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .receipt-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .receipt-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .receipt-body {
            padding: 2rem;
        }

        .detail-section {
            background: #f8fafc;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .detail-label i {
            margin-right: 0.5rem;
            width: 20px;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
            text-align: right;
        }

        .price-breakdown {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 2px solid #e5e7eb;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .total-row {
            border-top: 2px solid var(--primary-color);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .total-row .detail-label,
        .total-row .detail-value {
            color: var(--primary-color);
            font-size: 1.25rem;
            font-weight: 700;
        }

        .facilities-section {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border: 2px solid #bfdbfe;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .facilities-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .facilities-title i {
            margin-right: 0.5rem;
        }

        .facilities-text {
            color: #1e40af;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.4);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #10b981 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);
            transform: translateY(-1px);
            color: white;
        }

        .booking-info {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .booking-info-text {
            color: #0369a1;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .booking-info-text i {
            margin-right: 0.5rem;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .action-buttons {
                display: none !important;
            }
            
            .receipt-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }

        @media (max-width: 576px) {
            .receipt-container {
                margin: 0 1rem;
            }
            
            .receipt-header {
                padding: 1.5rem;
            }
            
            .receipt-title {
                font-size: 1.5rem;
            }
            
            .receipt-body {
                padding: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>

</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-header-content">
                <div class="success-icon">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h1 class="receipt-title">Struk Tiket Konser</h1>
                <p class="receipt-subtitle">NCT 127 Concert Ticket</p>
            </div>
        </div>

        <div class="receipt-body">
            <div class="text-center mb-4">
                <h2 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>
                    Pemesanan Berhasil!
                </h2>
                <p class="text-muted">Terima kasih atas pemesanan Anda</p>
            </div>

            <div class="detail-section">
                <h5 class="mb-3 text-primary">
                    <i class="fas fa-user me-2"></i>
                    Informasi Pemesanan
                </h5>
                
                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-user"></i>
                        Nama
                    </span>
                    <span class="detail-value"><?php echo htmlspecialchars($bookingData['nama_pelanggan']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-ticket-alt"></i>
                        Jenis Tiket
                    </span>
                    <span class="detail-value"><?php echo $bookingData['jenis_tiket']; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-sort-numeric-up"></i>
                        Jumlah Tiket
                    </span>
                    <span class="detail-value"><?php echo $bookingData['jumlah_tiket']; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-calendar"></i>
                        Tanggal Pesan
                    </span>
                    <span class="detail-value"><?php echo Utils::formatTanggal($bookingData['tanggal_pesan']); ?></span>
                </div>
            </div>

            <div class="facilities-section">
                <div class="facilities-title">
                    <i class="fas fa-star"></i>
                    Fasilitas yang Didapat
                </div>
                <div class="facilities-text">
                    <?php echo $bookingData['fasilitas']; ?>
                </div>
            </div>

            <div class="price-breakdown">
                <h5 class="mb-3 text-primary">
                    <i class="fas fa-calculator me-2"></i>
                    Rincian Harga
                </h5>
                
                <div class="price-row">
                    <span class="detail-label">Harga Tiket <?php echo $bookingData['jenis_tiket']; ?></span>
                    <span class="detail-value"><?php echo Utils::formatRupiah($bookingData['harga_tiket']); ?></span>
                </div>
                
                <div class="price-row">
                    <span class="detail-label">Jumlah Tiket</span>
                    <span class="detail-value"><?php echo $bookingData['jumlah_tiket']; ?></span>
                </div>
                
                <div class="price-row">
                    <span class="detail-label">Subtotal</span>
                    <span class="detail-value"><?php echo Utils::formatRupiah($bookingData['subtotal']); ?></span>
                </div>
                
                <div class="price-row">
                    <span class="detail-label">PPN (12%)</span>
                    <span class="detail-value"><?php echo Utils::formatRupiah($bookingData['ppn']); ?></span>
                </div>
                
                <div class="price-row total-row">
                    <span class="detail-label">
                        <i class="fas fa-money-bill-wave"></i>
                        Total Bayar
                    </span>
                    <span class="detail-value"><?php echo Utils::formatRupiah($bookingData['total_bayar']); ?></span>
                </div>
            </div>

            <div class="booking-info">
                <p class="booking-info-text">
                    <i class="fas fa-info-circle"></i>
                    Simpan struk ini sebagai bukti pemesanan tiket konser Anda
                </p>
            </div>

            <div class="action-buttons">
                <button onclick="window.print()" class="btn btn-primary flex-fill">
                    <i class="fas fa-print"></i>
                    Cetak Struk
                </button>
                
                <a href="index.php" class="btn btn-secondary flex-fill">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>