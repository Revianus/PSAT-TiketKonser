<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'tiket_konser';
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->connection->connect_error) {
                throw new Exception("Koneksi gagal: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset("utf8");
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

class TicketType {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function getAllTickets() {
        $tickets = array(
            array('id' => 1, 'name' => 'Silver', 'price' => 700000, 'facilities' => 'Masuk reguler'),
            array('id' => 2, 'name' => 'Platinum', 'price' => 1300000, 'facilities' => 'Free minuman'),
            array('id' => 3, 'name' => 'Premium', 'price' => 2000000, 'facilities' => 'Snack dan minuman'),
            array('id' => 4, 'name' => 'VIP', 'price' => 2700000, 'facilities' => 'Meet & greet + souvenir')
        );
        return $tickets;
    }
    
    public function getTicketById($id) {
        $tickets = $this->getAllTickets();
        foreach ($tickets as $ticket) {
            if ($ticket['id'] == $id) {
                return $ticket;
            }
        }
        return null;
    }
}

class Booking {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
        $this->createTable();
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_pelanggan VARCHAR(100) NOT NULL,
            jenis_tiket VARCHAR(50) NOT NULL,
            jumlah_tiket INT NOT NULL,
            harga_tiket DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            ppn DECIMAL(10,2) NOT NULL,
            total_bayar DECIMAL(10,2) NOT NULL,
            fasilitas TEXT NOT NULL,
            tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->query($sql);
    }
    
    public function saveBooking($data) {
        $stmt = $this->db->prepare("INSERT INTO bookings (nama_pelanggan, jenis_tiket, jumlah_tiket, harga_tiket, subtotal, ppn, total_bayar, fasilitas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssidddds", 
            $data['nama_pelanggan'],
            $data['jenis_tiket'],
            $data['jumlah_tiket'],
            $data['harga_tiket'],
            $data['subtotal'],
            $data['ppn'],
            $data['total_bayar'],
            $data['fasilitas']
        );
        
        return $stmt->execute();
    }
    
    public function getAllBookings() {
        $result = $this->db->query("SELECT * FROM bookings ORDER BY tanggal_pesan DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getBookingById($id) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

class Utils {
    public static function formatRupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
    
    public static function formatTanggal($tanggal) {
        return date('d/m/Y H:i:s', strtotime($tanggal));
    }
    
    public static function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    public static function calculateTotal($harga, $jumlah) {
        $subtotal = $harga * $jumlah;
        $ppn = $subtotal * 0.12;
        $total = $subtotal + $ppn;
        
        return array(
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'total' => $total
        );
    }
}
?>