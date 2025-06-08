<?php
require_once 'koneksi.php';

$database = new Database();
$ticketType = new TicketType($database);
$booking = new Booking($database);

$tickets = $ticketType->getAllTickets();
$bookings = $booking->getAllBookings();

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pemesanan Tiket Konser NCT 127</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
            --success-color: #059669;
            --danger-color: #dc2626;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 50%, #8b5cf6 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-icons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .hero-icons i {
            font-size: 3rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 50%;
            backdrop-filter: blur(10px);
            animation: float 3s ease-in-out infinite;
        }

        .hero-icons i:nth-child(3) {
            animation-delay: 1.5s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-top: 1rem;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .booking-card .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
        }

        .table-card .card-header {
            background: linear-gradient(135deg, var(--success-color) 0%, #10b981 100%);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
        }

        .card-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
            outline: none;
        }

        .price-calculation {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border: 2px solid #bfdbfe;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }

        .calculation-details {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .total-row {
            font-size: 1.25rem;
            color: var(--primary-color);
            padding-top: 0.5rem;
            border-top: 2px solid #e5e7eb;
        }

        .facilities-info {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
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
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            color: white;
            border: none;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0e7490 0%, #0891b2 100%);
            color: white;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 2px solid #cbd5e1;
            font-weight: 600;
            color: var(--primary-color);
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .customer-info {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .price-cell {
            font-weight: 600;
            color: var(--success-color);
            font-size: 1.1rem;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid var(--accent-color);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-icons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .hero-icons i {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .hero-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="hero-icons">
                            <i class="fas fa-music"></i>
                            <h1 class="hero-title">Tiket Konser NCT 127</h1>
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <p class="hero-subtitle">Sistem Pemesanan Tiket Konser Premium</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : ($messageType == 'danger' ? 'exclamation-circle' : 'exclamation-triangle'); ?>"></i>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Booking Form -->
        <div class="card booking-card mb-5">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Pemesanan Tiket Konser
                </h2>
            </div>
            <div class="card-body">
                <form method="POST" action="proses-tiket.php" id="bookingForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_pelanggan" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Nama Pelanggan
                            </label>
                            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" 
                                   placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_tiket" class="form-label">
                                <i class="fas fa-tags me-1"></i>
                                Jenis Tiket
                            </label>
                            <select class="form-select" id="jenis_tiket" name="jenis_tiket" required>
                                <option value="">Pilih jenis tiket</option>
                                <?php foreach ($tickets as $ticket): ?>
                                <option value="<?php echo $ticket['id']; ?>" 
                                        data-price="<?php echo $ticket['price']; ?>"
                                        data-name="<?php echo $ticket['name']; ?>"
                                        data-facilities="<?php echo $ticket['facilities']; ?>">
                                    <?php echo $ticket['name']; ?> - <?php echo Utils::formatRupiah($ticket['price']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_tiket" class="form-label">
                                <i class="fas fa-sort-numeric-up me-1"></i>
                                Jumlah Tiket
                            </label>
                            <input type="number" class="form-control" id="jumlah_tiket" name="jumlah_tiket" 
                                   min="1" max="10" value="1" required>
                        </div>
                    </div>

                    <!-- Price Calculation Display -->
                    <div id="priceCalculation" class="price-calculation mb-4">
                        <h5 class="mb-3">Detail Pemesanan</h5>
                        <div class="calculation-details">
                            <div class="d-flex justify-content-between mb-2">
                                <span id="ticketDetails">Tiket x 1</span>
                                <span id="subtotalAmount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>PPN (12%)</span>
                                <span id="ppnAmount">Rp 0</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between total-row">
                                <strong>Total</strong>
                                <strong id="totalAmount">Rp 0</strong>
                            </div>
                        </div>
                        
                        <div class="facilities-info mt-3">
                            <strong>Fasilitas:</strong>
                            <span id="facilitiesText">-</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-credit-card me-2"></i>
                        Pesan Tiket
                    </button>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card table-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Data Pemesanan Tiket
                </h2>
            </div>
            <div class="card-body">
                <?php if (count($bookings) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jenis Tiket</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($bookings as $row): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <div class="customer-info">
                                        <i class="fas fa-user-circle me-2"></i>
                                        <?php echo htmlspecialchars($row['nama_pelanggan']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $row['jenis_tiket']; ?></span>
                                </td>
                                <td><?php echo $row['jumlah_tiket']; ?></td>
                                <td class="price-cell"><?php echo Utils::formatRupiah($row['total_bayar']); ?></td>
                                <td>
                                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye me-1"></i>
                                        Lihat Struk
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada pemesanan</h5>
                    <p class="text-muted">Pemesanan tiket akan muncul di sini setelah Anda melakukan pesanan.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisSelect = document.getElementById('jenis_tiket');
            const jumlahInput = document.getElementById('jumlah_tiket');
            const priceCalculation = document.getElementById('priceCalculation');
            
            function calculatePrice() {
                const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
                const jumlah = parseInt(jumlahInput.value) || 1;
                
                if (selectedOption.value) {
                    const price = parseFloat(selectedOption.dataset.price);
                    const name = selectedOption.dataset.name;
                    const facilities = selectedOption.dataset.facilities;
                    
                    const subtotal = price * jumlah;
                    const ppn = subtotal * 0.12;
                    const total = subtotal + ppn;
                    
                    document.getElementById('ticketDetails').textContent = `Tiket ${name} x ${jumlah}`;
                    document.getElementById('subtotalAmount').textContent = formatRupiah(subtotal);
                    document.getElementById('ppnAmount').textContent = formatRupiah(ppn);
                    document.getElementById('totalAmount').textContent = formatRupiah(total);
                    document.getElementById('facilitiesText').textContent = facilities;
                    
                    priceCalculation.style.display = 'block';
                } else {
                    priceCalculation.style.display = 'none';
                }
            }
            
            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            }
            
            jenisSelect.addEventListener('change', calculatePrice);
            jumlahInput.addEventListener('input', calculatePrice);
        });
    </script>
</body>
</html>