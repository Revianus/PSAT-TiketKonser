<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $ticketType = new TicketType($database);
    $booking = new Booking($database);
    
    // Validasi input
    $nama_pelanggan = Utils::sanitizeInput($_POST['nama_pelanggan']);
    $jenis_tiket_id = (int)$_POST['jenis_tiket'];
    $jumlah_tiket = (int)$_POST['jumlah_tiket'];
    
    // Validasi data
    if (empty($nama_pelanggan) || $jenis_tiket_id <= 0 || $jumlah_tiket <= 0 || $jumlah_tiket > 10) {
        $_SESSION['message'] = 'Data tidak valid! Pastikan semua field terisi dengan benar.';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }
    
    // Ambil data tiket
    $ticket = $ticketType->getTicketById($jenis_tiket_id);
    
    if (!$ticket) {
        $_SESSION['message'] = 'Jenis tiket tidak ditemukan!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }
    
    // Hitung total
    $calculation = Utils::calculateTotal($ticket['price'], $jumlah_tiket);
    
    // Data untuk disimpan
    $bookingData = array(
        'nama_pelanggan' => $nama_pelanggan,
        'jenis_tiket' => $ticket['name'],
        'jumlah_tiket' => $jumlah_tiket,
        'harga_tiket' => $ticket['price'],
        'subtotal' => $calculation['subtotal'],
        'ppn' => $calculation['ppn'],
        'total_bayar' => $calculation['total'],
        'fasilitas' => $ticket['facilities']
    );
    
    // Simpan ke database
    if ($booking->saveBooking($bookingData)) {
        $_SESSION['message'] = 'Pemesanan tiket berhasil! Silakan lihat struk pemesanan.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Gagal menyimpan pemesanan! Silakan coba lagi.';
        $_SESSION['message_type'] = 'danger';
    }
    
    header('Location: index.php');
    exit();
} else {
    header('Location: index.php');
    exit();
}
?>