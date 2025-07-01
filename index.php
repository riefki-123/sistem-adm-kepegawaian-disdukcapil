<?php
// config.php - Konfigurasi Database
class Database {
    private $host = "localhost";
    private $db_name = "sistem_adm_disdukcapil";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// functions.php - Fungsi-fungsi utama
function getAllPNS($conn) {
    $query = "SELECT p.*, rp.pangkat_golongan, rp.jabatan, rg.nominal_gaji 
              FROM pns p 
              LEFT JOIN riwayat_pangkat rp ON p.NIP = rp.NIP AND rp.is_current = 1
              LEFT JOIN riwayat_gaji rg ON p.NIP = rg.NIP AND rg.is_current = 1
              ORDER BY p.nama";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPNSByNIP($conn, $nip) {
    $query = "SELECT p.*, rp.pangkat_golongan, rp.jabatan, rg.nominal_gaji 
              FROM pns p 
              LEFT JOIN riwayat_pangkat rp ON p.NIP = rp.NIP AND rp.is_current = 1
              LEFT JOIN riwayat_gaji rg ON p.NIP = rg.NIP AND rg.is_current = 1
              WHERE p.NIP = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $nip);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllPengajuan($conn) {
    $query = "SELECT pg.*, p.nama, jp.nama_jenis, o.nama_lengkap_operator
              FROM pengajuan pg
              JOIN pns p ON pg.NIP = p.NIP
              JOIN jenis_pengajuan jp ON pg.kode_jenis = jp.kode_jenis
              JOIN operator o ON pg.id_operator = o.id_operator
              ORDER BY pg.tgl_pengajuan DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getJenisPengajuan($conn) {
    $query = "SELECT * FROM jenis_pengajuan ORDER BY nama_jenis";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateStatusPengajuan($conn, $id_pengajuan, $status, $keterangan) {
    $query = "UPDATE pengajuan SET status_pengajuan = ?, tanggal_persetujuan = CURDATE(), keterangan = ? WHERE id_pengajuan = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$status, $keterangan, $id_pengajuan]);
}

// Inisialisasi database
$database = new Database();
$conn = $database->getConnection();

// Routing sederhana
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $id_pengajuan = $_POST['id_pengajuan'];
        $status = $_POST['status'];
        $keterangan = $_POST['keterangan'];
        updateStatusPengajuan($conn, $id_pengajuan, $status, $keterangan);
        header("Location: ?page=pengajuan&msg=updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Administrasi Kepegawaian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            text-align: center;
            opacity: 0.9;
        }
        
        .navbar {
            background-color: #34495e;
            padding: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .navbar li {
            margin: 0;
        }
        
        .navbar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: background-color 0.3s;
        }
        
        .navbar a:hover, .navbar a.active {
            background-color: #2c3e50;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .table tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            margin: 0.25rem;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-diajukan {
            background-color: #f39c12;
            color: white;
        }
        
        .status-disetujui {
            background-color: #27ae60;
            color: white;
        }
        
        .status-ditolak {
            background-color: #e74c3c;
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #000;
        }
        
        @media (max-width: 768px) {
            .navbar ul {
                flex-direction: column;
            }
            
            .table {
                font-size: 0.9rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Administrasi Kepegawaian</h1>
        <p>Dinas Kependudukan dan Pencatatan Sipil</p>
    </div>

    <nav class="navbar">
        <ul>
            <li><a href="?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="?page=pns" class="<?= $page == 'pns' ? 'active' : '' ?>">Data PNS</a></li>
            <li><a href="?page=pengajuan" class="<?= $page == 'pengajuan' ? 'active' : '' ?>">Pengajuan</a></li>
            <li><a href="?page=laporan" class="<?= $page == 'laporan' ? 'active' : '' ?>">Laporan</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        switch($page) {
            case 'dashboard':
                // Dashboard dengan statistik
                $total_pns = $conn->query("SELECT COUNT(*) FROM pns")->fetchColumn();
                $pns_aktif = $conn->query("SELECT COUNT(*) FROM pns WHERE status_asn = 'Aktif'")->fetchColumn();
                $pengajuan_pending = $conn->query("SELECT COUNT(*) FROM pengajuan WHERE status_pengajuan = 'Diajukan'")->fetchColumn();
                $pengajuan_bulan_ini = $conn->query("SELECT COUNT(*) FROM pengajuan WHERE MONTH(tgl_pengajuan) = MONTH(CURDATE()) AND YEAR(tgl_pengajuan) = YEAR(CURDATE())")->fetchColumn();
                ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_pns ?></div>
                        <div class="stat-label">Total PNS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $pns_aktif ?></div>
                        <div class="stat-label">PNS Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $pengajuan_pending ?></div>
                        <div class="stat-label">Pengajuan Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $pengajuan_bulan_ini ?></div>
                        <div class="stat-label">Pengajuan Bulan Ini</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Pengajuan Terbaru</div>
                    <div class="card-body">
                        <?php
                        $recent_pengajuan = $conn->query("
                            SELECT pg.*, p.nama, jp.nama_jenis 
                            FROM pengajuan pg
                            JOIN pns p ON pg.NIP = p.NIP
                            JOIN jenis_pengajuan jp ON pg.kode_jenis = jp.kode_jenis
                            ORDER BY pg.tgl_pengajuan DESC 
                            LIMIT 5
                        ")->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama PNS</th>
                                    <th>Jenis Pengajuan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_pengajuan as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_pengajuan'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_jenis']) ?></td>
                                    <td><span class="status-badge status-<?= strtolower($row['status_pengajuan']) ?>"><?= $row['status_pengajuan'] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                break;

            case 'pns':
                $pns_data = getAllPNS($conn);
                ?>
                <div class="card">
                    <div class="card-header">Data Pegawai Negeri Sipil</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Pangkat/Golongan</th>
                                    <th>Jabatan</th>
                                    <th>Gaji</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pns_data as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['NIP']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['pangkat_golongan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
                                    <td>Rp <?= number_format($row['nominal_gaji'] ?? 0, 0, ',', '.') ?></td>
                                    <td><span class="status-badge status-<?= strtolower($row['status_asn']) ?>"><?= $row['status_asn'] ?></span></td>
                                    <td>
                                        <button class="btn btn-primary" onclick="viewDetail('<?= $row['NIP'] ?>')">Detail</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                break;

            case 'pengajuan':
                if(isset($_GET['msg']) && $_GET['msg'] == 'updated') {
                    echo '<div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">Status pengajuan berhasil diperbarui!</div>';
                }
                
                $pengajuan_data = getAllPengajuan($conn);
                ?>
                <div class="card">
                    <div class="card-header">Daftar Pengajuan Kenaikan Pangkat/Gaji</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Nama PNS</th>
                                    <th>Jenis Pengajuan</th>
                                    <th>Operator</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pengajuan_data as $row): ?>
                                <tr>
                                    <td><?= $row['id_pengajuan'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tgl_pengajuan'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_jenis']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap_operator']) ?></td>
                                    <td><span class="status-badge status-<?= strtolower($row['status_pengajuan']) ?>"><?= $row['status_pengajuan'] ?></span></td>
                                    <td>
                                        <?php if($row['status_pengajuan'] == 'Diajukan'): ?>
                                        <button class="btn btn-success" onclick="updateStatus(<?= $row['id_pengajuan'] ?>, 'Disetujui')">Setuju</button>
                                        <button class="btn btn-danger" onclick="updateStatus(<?= $row['id_pengajuan'] ?>, 'Ditolak')">Tolak</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                break;

            case 'laporan':
                // Laporan statistik
                $stats_pangkat = $conn->query("
                    SELECT jp.kategori_pengajuan, COUNT(*) as jumlah, 
                           SUM(CASE WHEN pg.status_pengajuan = 'Disetujui' THEN 1 ELSE 0 END) as disetujui
                    FROM pengajuan pg 
                    JOIN jenis_pengajuan jp ON pg.kode_jenis = jp.kode_jenis 
                    GROUP BY jp.kategori_pengajuan
                ")->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="card">
                    <div class="card-header">Laporan Statistik Pengajuan</div>
                    <div class="card-body">
                        <h3>Rekapitulasi Pengajuan per Kategori</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Total Pengajuan</th>
                                    <th>Disetujui</th>
                                    <th>Persentase Persetujuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stats_pangkat as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['kategori_pengajuan']) ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td><?= $row['disetujui'] ?></td>
                                    <td><?= $row['jumlah'] > 0 ? round(($row['disetujui'] / $row['jumlah']) * 100, 1) : 0 ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                break;

            default:
                echo '<div class="card"><div class="card-body"><h2>Halaman tidak ditemukan</h2></div></div>';
        }
        ?>
    </div>

    <!-- Modal untuk update status -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Status Pengajuan</h2>
            <form method="POST">
                <input type="hidden" id="modal_id_pengajuan" name="id_pengajuan">
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" id="modal_status" class="form-control" required>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Keterangan:</label>
                    <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        var modal = document.getElementById("statusModal");
        var span = document.getElementsByClassName("close")[0];

        function updateStatus(id, status) {
            document.getElementById("modal_id_pengajuan").value = id;
            document.getElementById("modal_status").value = status;
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function viewDetail(nip) {
            alert("Fitur detail PNS untuk NIP: " + nip + " akan segera tersedia");
        }
    </script>
</body>
</html>
