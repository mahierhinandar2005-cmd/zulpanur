<?php
session_start();

// LOGIN DUMMY
if (isset($_POST['login'])) {
    if ($_POST['user'] == 'admin' && $_POST['pass'] == '123') {
        $_SESSION['login'] = true;
        $_SESSION['user'] = 'admin';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['login'])) {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login - PD ZULPA NUR</title>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            *{margin:0;padding:0;box-sizing:border-box;}
            body{font-family:"Inter",sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;display:flex;justify-content:center;align-items:center;}
            .login-box{background:white;border-radius:20px;display:flex;max-width:1000px;width:100%;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);}
            .login-left{background:linear-gradient(135deg,#1e3c72,#2a5298);padding:50px;text-align:center;color:white;width:45%;}
            .login-left .icon{font-size:70px;margin-bottom:20px;}
            .login-left h1{font-size:28px;margin-bottom:10px;}
            .login-right{padding:50px;width:55%;}
            .login-right h2{color:#333;margin-bottom:10px;}
            .login-right p{color:#666;margin-bottom:30px;}
            .form-group{margin-bottom:20px;}
            .form-group label{display:block;margin-bottom:8px;font-weight:600;color:#555;}
            .form-group input{width:100%;padding:12px;border:1px solid #ddd;border-radius:10px;font-size:14px;}
            .form-group input:focus{outline:none;border-color:#667eea;box-shadow:0 0 0 3px rgba(102,126,234,0.1);}
            .btn-login{width:100%;padding:12px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;}
            .btn-login:hover{transform:scale(1.02);}
        </style>
    </head>
    <body>
        <div class="login-box">
            <div class="login-left"><div class="icon">🧸</div><h1>PD ZULPA NUR</h1><p>Produsen Boneka & Mainan</p></div>
            <div class="login-right">
                <h2>Login</h2>
                <p>Masuk ke dashboard administrator</p>
                <form method="POST">
                    <div class="form-group"><label>Username</label><input type="text" name="user" placeholder="admin" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="pass" placeholder="123" required></div>
                    <button type="submit" name="login" class="btn-login">Login</button>
                </form>
                <div style="margin-top:30px;text-align:center;font-size:12px;color:#999;">Demo: admin / 123</div>
            </div>
        </div>
    </body>
    </html>
    ';
    exit();
}

// KONEKSI DATABASE
$conn = mysqli_connect('localhost', 'root', '', 'zulpa_nur');
if (!$conn) {
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS zulpa_nur");
    mysqli_select_db($conn, 'zulpa_nur');
}

// BUAT TABEL
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS boneka (
    id_boneka INT AUTO_INCREMENT PRIMARY KEY,
    kode_boneka VARCHAR(50),
    nama_boneka VARCHAR(100) NOT NULL,
    stok INT DEFAULT 0,
    harga INT NOT NULL,
    gambar VARCHAR(10) DEFAULT '🧸'
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS customer (
    id_customer INT AUTO_INCREMENT PRIMARY KEY,
    nama_customer VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_telepon VARCHAR(20)
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS petugas (
    id_petugas INT AUTO_INCREMENT PRIMARY KEY,
    nama_petugas VARCHAR(100) NOT NULL,
    jabatan VARCHAR(50)
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS nota (
    no_nota INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    total INT DEFAULT 0,
    id_customer INT,
    id_petugas INT
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS detail_nota (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    no_nota INT,
    id_boneka INT,
    jumlah INT NOT NULL,
    harga INT NOT NULL,
    subtotal INT NOT NULL
)");

// DATA AWAL
$cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM boneka"))['total'] ?? 0;
if ($cek == 0) {
    mysqli_query($conn, "INSERT INTO customer (id_customer, nama_customer, alamat, no_telepon) VALUES 
        (1, 'MASNAH (INOY - CASH)', 'KP. CAKUING NO.39 RT.004/RW.004 KEL. JATISARI, KEC. JATIASIH BEKASI', '08123456789'),
        (2, 'BUDI SANTOSO', 'Jl. Merdeka No.10, Jakarta', '08987654321'),
        (3, 'RINA WATI', 'Jl. Diponegoro No.5, Bandung', '087812345678')");
    
    mysqli_query($conn, "INSERT INTO petugas (id_petugas, nama_petugas, jabatan) VALUES 
        (1, 'Admin', 'Owner'),
        (2, 'Siti', 'Kasir'),
        (3, 'Budi', 'Produksi')");
    
    mysqli_query($conn, "INSERT INTO boneka (id_boneka, kode_boneka, nama_boneka, stok, harga, gambar) VALUES 
        (1, 'BD-001', 'Teddy Bear Pink', 50, 75000, '🧸'),
        (2, 'BD-002', 'Boneka Kelinci Putih', 45, 68000, '🐰'),
        (3, 'BD-003', 'Boneka Panda Gendut', 30, 95000, '🐼'),
        (4, 'BD-004', 'Boneka Dino Hijau', 25, 85000, '🦕'),
        (5, 'BD-005', 'Boneka Kucing Oren', 60, 60000, '🐱'),
        (6, 'BD-006', 'Boneka Beruang Coklat', 40, 70000, '🐻'),
        (7, 'BD-007', 'Boneka Monyet Lucu', 35, 55000, '🐵')");
}

$total_boneka = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM boneka"))['total'];
$total_customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customer"))['total'];
$total_petugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM nota"))['total'];
$total_pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as total FROM nota"))['total'] ?? 0;

// PROSES CRUD
if(isset($_POST['tambah_boneka'])){
    mysqli_query($conn, "INSERT INTO boneka (kode_boneka, nama_boneka, stok, harga) VALUES ('".$_POST['kode_boneka']."','".$_POST['nama_boneka']."',".(int)$_POST['stok'].",".(int)$_POST['harga'].")");
    header("Location: ?page=boneka"); exit();
}
if(isset($_POST['edit_boneka'])){
    $id = (int)$_POST['id_boneka'];
    mysqli_query($conn, "UPDATE boneka SET kode_boneka='".$_POST['kode_boneka']."', nama_boneka='".$_POST['nama_boneka']."', stok=".(int)$_POST['stok'].", harga=".(int)$_POST['harga']." WHERE id_boneka=$id");
    header("Location: ?page=boneka"); exit();
}
if(isset($_GET['hapus_boneka'])){
    mysqli_query($conn, "DELETE FROM boneka WHERE id_boneka=".(int)$_GET['hapus_boneka']);
    header("Location: ?page=boneka"); exit();
}
if(isset($_POST['tambah_customer'])){
    mysqli_query($conn, "INSERT INTO customer (nama_customer, alamat, no_telepon) VALUES ('".$_POST['nama_customer']."','".$_POST['alamat']."','".$_POST['no_telepon']."')");
    header("Location: ?page=customer"); exit();
}
if(isset($_POST['edit_customer'])){
    $id = (int)$_POST['id_customer'];
    mysqli_query($conn, "UPDATE customer SET nama_customer='".$_POST['nama_customer']."', alamat='".$_POST['alamat']."', no_telepon='".$_POST['no_telepon']."' WHERE id_customer=$id");
    header("Location: ?page=customer"); exit();
}
if(isset($_GET['hapus_customer'])){
    mysqli_query($conn, "DELETE FROM customer WHERE id_customer=".(int)$_GET['hapus_customer']);
    header("Location: ?page=customer"); exit();
}
if(isset($_POST['tambah_petugas'])){
    mysqli_query($conn, "INSERT INTO petugas (nama_petugas, jabatan) VALUES ('".$_POST['nama_petugas']."','".$_POST['jabatan']."')");
    header("Location: ?page=petugas"); exit();
}
if(isset($_POST['edit_petugas'])){
    $id = (int)$_POST['id_petugas'];
    mysqli_query($conn, "UPDATE petugas SET nama_petugas='".$_POST['nama_petugas']."', jabatan='".$_POST['jabatan']."' WHERE id_petugas=$id");
    header("Location: ?page=petugas"); exit();
}
if(isset($_GET['hapus_petugas'])){
    mysqli_query($conn, "DELETE FROM petugas WHERE id_petugas=".(int)$_GET['hapus_petugas']);
    header("Location: ?page=petugas"); exit();
}

// KERANJANG
if(isset($_POST['add_to_cart'])){
    $item = ['id_boneka'=>(int)$_POST['id_boneka'],'kode_boneka'=>$_POST['kode_boneka'],'nama_boneka'=>$_POST['nama_boneka'],'harga'=>(int)$_POST['harga'],'jumlah'=>(int)$_POST['jumlah'],'gambar'=>$_POST['gambar']];
    if(!isset($_SESSION['cart'])) $_SESSION['cart']=[];
    $found=false; foreach($_SESSION['cart'] as $k=>$v) if($v['id_boneka']==$item['id_boneka']){$_SESSION['cart'][$k]['jumlah']+=$item['jumlah'];$found=true;break;}
    if(!$found) $_SESSION['cart'][]=$item;
    header("Location: ?page=tambah_transaksi"); exit();
}
if(isset($_GET['remove_from_cart'])){
    unset($_SESSION['cart'][(int)$_GET['remove_from_cart']]);
    $_SESSION['cart']=array_values($_SESSION['cart']);
    header("Location: ?page=tambah_transaksi"); exit();
}
if(isset($_POST['proses_transaksi'])){
    $no_nota=(int)$_POST['no_nota']; $tanggal=$_POST['tanggal']; $id_customer=(int)$_POST['id_customer']; $id_petugas=(int)$_POST['id_petugas']; $total=0;
    foreach($_SESSION['cart'] as $item) $total+=$item['harga']*$item['jumlah'];
    mysqli_query($conn, "INSERT INTO nota (no_nota, tanggal, total, id_customer, id_petugas) VALUES ($no_nota, '$tanggal', $total, $id_customer, $id_petugas)");
    foreach($_SESSION['cart'] as $item){
        $subtotal=$item['harga']*$item['jumlah'];
        mysqli_query($conn, "INSERT INTO detail_nota (no_nota, id_boneka, jumlah, harga, subtotal) VALUES ($no_nota, ".$item['id_boneka'].", ".$item['jumlah'].", ".$item['harga'].", $subtotal)");
        mysqli_query($conn, "UPDATE boneka SET stok = stok - ".$item['jumlah']." WHERE id_boneka = ".$item['id_boneka']);
    }
    unset($_SESSION['cart']);
    header("Location: ?page=cetak_nota&id=$no_nota"); exit();
}
if(isset($_GET['hapus_transaksi'])){
    mysqli_query($conn, "DELETE FROM detail_nota WHERE no_nota=".(int)$_GET['hapus_transaksi']);
    mysqli_query($conn, "DELETE FROM nota WHERE no_nota=".(int)$_GET['hapus_transaksi']);
    header("Location: ?page=transaksi"); exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PD ZULPA NUR - Boneka</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#f5f7fa;}
        .container{max-width:1400px;margin:0 auto;}
        .sidebar{position:fixed;left:0;top:0;width:280px;height:100vh;background:white;box-shadow:2px 0 12px rgba(0,0,0,0.05);z-index:1000;}
        .sidebar-header{padding:30px 24px;text-align:center;border-bottom:1px solid #eef2f6;}
        .logo-img{width:70px;height:70px;object-fit:contain;margin-bottom:15px;border-radius:12px;}
        .sidebar-header h1{font-size:20px;color:#1a2a4f;margin-bottom:5px;}
        .sidebar-header p{font-size:12px;color:#7a8a9e;}
        .sidebar-nav{padding:20px 16px;}
        .sidebar-nav a{display:flex;align-items:center;gap:12px;padding:12px 16px;color:#4a5a72;text-decoration:none;border-radius:12px;margin-bottom:6px;transition:all 0.2s;font-weight:500;}
        .sidebar-nav a:hover,.sidebar-nav a.active{background:#667eea;color:white;}
        .sidebar-nav .logout{margin-top:40px;border-top:1px solid #eef2f6;padding-top:20px;}
        .main{margin-left:280px;padding:30px 40px;}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
        .page-header h2{font-size:28px;font-weight:700;color:#1a2a4f;}
        .user-badge{background:white;padding:10px 20px;border-radius:40px;box-shadow:0 2px 8px rgba(0,0,0,0.04);}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-bottom:32px;}
        .stat-card{background:white;border-radius:20px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.04);}
        .stat-card .icon{font-size:40px;margin-bottom:12px;}
        .stat-card .value{font-size:32px;font-weight:700;color:#1a2a4f;}
        .stat-card .label{font-size:13px;color:#7a8a9e;margin-top:5px;}
        .data-table{width:100%;background:white;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);border-collapse:collapse;}
        .data-table th{padding:14px 16px;text-align:left;background:#f8fafc;font-weight:600;color:#4a5a72;border-bottom:1px solid #eef2f6;}
        .data-table td{padding:12px 16px;border-bottom:1px solid #f0f4f8;color:#5a6a82;}
        .data-table tr:hover{background:#f8fafc;}
        .btn{display:inline-block;padding:8px 18px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:500;margin:2px;cursor:pointer;border:none;transition:all 0.2s;}
        .btn-primary{background:#667eea;color:white;}
        .btn-primary:hover{background:#5a67d8;}
        .btn-success{background:#10b981;color:white;}
        .btn-success:hover{background:#059669;}
        .btn-danger{background:#ef4444;color:white;}
        .btn-danger:hover{background:#dc2626;}
        .btn-warning{background:#f59e0b;color:white;}
        .btn-info{background:#06b6d4;color:white;}
        .btn-outline{background:white;border:1px solid #e2e8f0;color:#4a5a72;}
        .action-buttons{margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;}
        .form-card{background:white;border-radius:20px;padding:30px;max-width:550px;box-shadow:0 2px 8px rgba(0,0,0,0.04);}
        .form-group{margin-bottom:20px;}
        .form-group label{display:block;margin-bottom:8px;font-weight:600;color:#4a5a72;}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:10px;font-size:14px;}
        .cart-table{width:100%;margin:20px 0;border-collapse:collapse;}
        .cart-table th,.cart-table td{padding:10px;text-align:left;border-bottom:1px solid #eef2f6;}
        .cart-table th{background:#f8fafc;}
        .total-display{text-align:right;font-size:18px;font-weight:700;margin-top:15px;padding-top:15px;border-top:2px solid #eef2f6;color:#1a2a4f;}
        .welcome-card{background:linear-gradient(135deg,#667eea,#764ba2);border-radius:24px;padding:50px;text-align:center;margin-bottom:32px;color:white;}
        .welcome-card .icon{font-size:70px;margin-bottom:15px;}
        .welcome-card h2{font-size:28px;margin-bottom:10px;}
        .nota-wrapper{background:#f0f4f8;padding:24px;display:flex;justify-content:center;border-radius:20px;}
        .nota{max-width:360px;background:white;padding:20px;font-family:'Courier New',monospace;font-size:11px;border:1px solid #e2e8f0;border-radius:12px;}
        .nota-info{margin-bottom:12px;}
        .nota-info td{padding:3px;font-size:10px;}
        .nota-items{width:100%;border-collapse:collapse;margin-bottom:12px;}
        .nota-items th,.nota-items td{border:1px solid #e2e8f0;padding:6px;font-size:9px;}
        .nota-items th{background:#f8fafc;text-align:center;}
        .nota-items td:last-child,.nota-items th:last-child{text-align:right;}
        .nota-total{text-align:right;font-weight:bold;margin-top:10px;padding-top:8px;border-top:1px dashed #e2e8f0;color:#1a2a4f;}
        .ttd{display:flex;justify-content:space-between;margin-top:15px;}
        .ttd div{text-align:center;font-size:9px;}
        @media (max-width:768px){.sidebar{display:none;}.main{margin-left:0;padding:20px;}.stats{grid-template-columns:1fr;}}
        @media print{body *{visibility:hidden;}.nota,.nota *{visibility:visible;}.nota{position:absolute;top:0;left:0;width:100%;}.action-buttons,.sidebar,.main>*:not(.nota-wrapper){display:none;}}
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="assets/logo.jpeg" alt="Logo" class="logo-img" onerror="this.style.display='none'">
            <h1>PD ZULPA NUR</h1>
            <p>Produsen Boneka</p>
        </div>
        <div class="sidebar-nav">
            <a href="?page=home">🏠 Beranda</a>
            <a href="?page=dashboard">📊 Dashboard</a>
            <a href="?page=boneka">🧸 Boneka</a>
            <a href="?page=customer">👥 Customer</a>
            <a href="?page=petugas">👨‍💼 Petugas</a>
            <a href="?page=transaksi">🧾 Transaksi</a>
            <a href="?logout=1" class="logout">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main">
        <div class="page-header">
            <h2><?php echo match($_GET['page'] ?? 'home') {
                'home' => 'Beranda',
                'dashboard' => 'Dashboard',
                'boneka' => 'Data Boneka',
                'customer' => 'Data Customer',
                'petugas' => 'Data Petugas',
                'transaksi' => 'Data Transaksi',
                'tambah_transaksi' => 'Tambah Transaksi',
                default => 'Dashboard'
            }; ?></h2>
            <div class="user-badge"><span>👋 <?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Admin'; ?></span></div>
        </div>
        
        <?php
        $page = $_GET['page'] ?? 'home';
        
        // HOME
        if ($page == 'home') {
            echo '
            <div class="welcome-card">
                <div class="icon">🧸</div>
                <h2>Selamat Datang di PD ZULPA NUR</h2>
                <p>Produsen Boneka Berkualitas & Terpercaya</p>
                <p style="margin-top:16px;">Jl Perjuangan Rt. 04/04 No. 39 Gg. Eman Ebor<br>Kel. Jati Sari Kec. Jati Asih Kota Bekasi 17426</p>
                <p>📞 Telp 0838 0710 9757</p>
            </div>
            <div class="stats">
                <div class="stat-card"><div class="icon">🧸</div><div class="value">'.$total_boneka.'</div><div class="label">Jenis Boneka</div></div>
                <div class="stat-card"><div class="icon">👥</div><div class="value">'.$total_customer.'</div><div class="label">Customer</div></div>
                <div class="stat-card"><div class="icon">👨‍💼</div><div class="value">'.$total_petugas.'</div><div class="label">Petugas</div></div>
                <div class="stat-card"><div class="icon">💰</div><div class="value">Rp '.number_format($total_pemasukan,0,',','.').'</div><div class="label">Omzet</div></div>
            </div>
            <div class="action-buttons"><a href="?page=tambah_transaksi" class="btn btn-success">+ Transaksi Baru</a></div>';
            
            $transaksi_terbaru = mysqli_query($conn, "SELECT n.*, c.nama_customer FROM nota n JOIN customer c ON n.id_customer = c.id_customer ORDER BY n.no_nota DESC LIMIT 5");
            echo '<h3 style="margin:20px 0 15px;">📋 Transaksi Terbaru</h3>
            <table class="data-table"><thead><tr><th>No Nota</th><th>Tanggal</th><th>Customer</th><th>Total</th><th>Aksi</th></tr></thead><tbody>';
            while($r=mysqli_fetch_assoc($transaksi_terbaru)) echo '<tr>
                    <td>'.$r['no_nota'].'</a></td>
                    <td>'.date('d-m-Y',strtotime($r['tanggal'])).'</a></td>
                    <td>'.htmlspecialchars($r['nama_customer']).'</a></td>
                    <td>Rp '.number_format($r['total'],0,',','.').'</a></td>
                    <td><a href="?page=detail_transaksi&id='.$r['no_nota'].'" class="btn btn-info">Detail</a> <a href="?page=cetak_nota&id='.$r['no_nota'].'" class="btn btn-primary">Cetak</a></a></td>
                </tr>';
            echo '</tbody><td>';
        }
        
        // DASHBOARD
        elseif ($page == 'dashboard') {
            echo '
            <div class="stats">
                <div class="stat-card"><div class="icon">🧸</div><div class="value">'.$total_boneka.'</div><div class="label">Boneka</div></div>
                <div class="stat-card"><div class="icon">👥</div><div class="value">'.$total_customer.'</div><div class="label">Customer</div></div>
                <div class="stat-card"><div class="icon">👨‍💼</div><div class="value">'.$total_petugas.'</div><div class="label">Petugas</div></div>
                <div class="stat-card"><div class="icon">💰</div><div class="value">Rp '.number_format($total_pemasukan,0,',','.').'</div><div class="label">Omzet</div></div>
            </div>';
            
            $transaksi = mysqli_query($conn, "SELECT n.*, c.nama_customer, p.nama_petugas FROM nota n JOIN customer c ON n.id_customer = c.id_customer JOIN petugas p ON n.id_petugas = p.id_petugas ORDER BY n.no_nota DESC LIMIT 10");
            echo '<h3>📋 Transaksi Terbaru</h3>
            <table class="data-table"><thead><tr><th>No Nota</th><th>Tanggal</th><th>Customer</th><th>Petugas</th><th>Total</th><th>Aksi</th></tr></thead><tbody>';
            while($r=mysqli_fetch_assoc($transaksi)) echo '<tr>
                    <td>'.$r['no_nota'].'</a></td>
                    <td>'.date('d-m-Y',strtotime($r['tanggal'])).'</a></td>
                    <td>'.htmlspecialchars($r['nama_customer']).'</a></td>
                    <td>'.htmlspecialchars($r['nama_petugas']).'</a></td>
                    <td>Rp '.number_format($r['total'],0,',','.').'</a></td>
                    <td><a href="?page=detail_transaksi&id='.$r['no_nota'].'" class="btn btn-info">Detail</a> <a href="?page=cetak_nota&id='.$r['no_nota'].'" class="btn btn-primary">Cetak</a></a></td>
                </tr>';
            echo '</tbody><tr>';
        }
        
        // BONEKA
        elseif ($page == 'boneka') {
            if(isset($_GET['edit'])){
                $d=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM boneka WHERE id_boneka=".(int)$_GET['edit']));
                echo '
                <h2>Edit Boneka</h2>
                <div class="form-card">
                    <form method="POST">
                        <input type="hidden" name="id_boneka" value="'.$d['id_boneka'].'">
                        <div class="form-group"><label>Kode Boneka</label><input type="text" name="kode_boneka" value="'.htmlspecialchars($d['kode_boneka']).'"></div>
                        <div class="form-group"><label>Nama Boneka</label><input type="text" name="nama_boneka" value="'.htmlspecialchars($d['nama_boneka']).'" required></div>
                        <div class="form-group"><label>Stok</label><input type="number" name="stok" value="'.$d['stok'].'"></div>
                        <div class="form-group"><label>Harga</label><input type="number" name="harga" value="'.$d['harga'].'" required></div>
                        <button type="submit" name="edit_boneka" class="btn btn-primary">Update</button>
                        <a href="?page=boneka" class="btn btn-outline">Batal</a>
                    </form>
                </div>';
            } elseif(isset($_GET['tambah'])){
                echo '
                <h2>Tambah Boneka</h2>
                <div class="form-card">
                    <form method="POST">
                        <div class="form-group"><label>Kode Boneka</label><input type="text" name="kode_boneka"></div>
                        <div class="form-group"><label>Nama Boneka</label><input type="text" name="nama_boneka" required></div>
                        <div class="form-group"><label>Stok Awal</label><input type="number" name="stok" value="0"></div>
                        <div class="form-group"><label>Harga</label><input type="number" name="harga" required></div>
                        <button type="submit" name="tambah_boneka" class="btn btn-success">Simpan</button>
                        <a href="?page=boneka" class="btn btn-outline">Batal</a>
                    </form>
                </div>';
            } else {
                $data = mysqli_query($conn, "SELECT * FROM boneka ORDER BY id_boneka DESC");
                echo '<div class="action-buttons"><a href="?page=boneka&tambah=1" class="btn btn-success">+ Tambah Boneka</a></div>
                <h2>Data Boneka</h2>
                <table class="data-table">
                    <thead>
                        <tr><th>Kode</th><th>Nama Boneka</th><th>Stok</th><th>Harga</th><th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>';
                while($r=mysqli_fetch_assoc($data)){
                    echo '<tr>
                        <td>'.htmlspecialchars($r['kode_boneka']).'</a></td>
                        <td><span style="font-size:18px;">'.$r['gambar'].'</span> '.htmlspecialchars($r['nama_boneka']).'</a></td>
                        <td>'.$r['stok'].' PCS</a></td>
                        <td>Rp '.number_format($r['harga'],0,',','.').'</a></td>
                        <td><a href="?page=boneka&edit='.$r['id_boneka'].'" class="btn btn-warning">Edit</a> <a href="?hapus_boneka='.$r['id_boneka'].'" class="btn btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</a></a></td>
                    </tr>';
                }
                echo '</tbody>
                </table>';
            }
        }
        
        // CUSTOMER
        elseif ($page == 'customer') {
            if(isset($_GET['edit'])){
                $d=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM customer WHERE id_customer=".(int)$_GET['edit']));
                echo '
                <h2>Edit Customer</h2>
                <div class="form-card">
                    <form method="POST">
                        <input type="hidden" name="id_customer" value="'.$d['id_customer'].'">
                        <div class="form-group"><label>Nama Customer</label><input type="text" name="nama_customer" value="'.htmlspecialchars($d['nama_customer']).'" required></div>
                        <div class="form-group"><label>Alamat</label><textarea name="alamat">'.htmlspecialchars($d['alamat']).'</textarea></div>
                        <div class="form-group"><label>No Telepon</label><input type="text" name="no_telepon" value="'.$d['no_telepon'].'"></div>
                        <button type="submit" name="edit_customer" class="btn btn-primary">Update</button>
                        <a href="?page=customer" class="btn btn-outline">Batal</a>
                    </form>
                </div>';
            } elseif(isset($_GET['tambah'])){
                echo '
                <h2>Tambah Customer</h2>
                <div class="form-card">
                    <form method="POST">
                        <div class="form-group"><label>Nama Customer</label><input type="text" name="nama_customer" required></div>
                        <div class="form-group"><label>Alamat</label><textarea name="alamat"></textarea></div>
                        <div class="form-group"><label>No Telepon</label><input type="text" name="no_telepon"></div>
                        <button type="submit" name="tambah_customer" class="btn btn-success">Simpan</button>
                        <a href="?page=customer" class="btn btn-outline">Batal</a>
                    </form>
                </div>';
            } else {
                $data = mysqli_query($conn, "SELECT * FROM customer ORDER BY id_customer DESC");
                echo '<div class="action-buttons"><a href="?page=customer&tambah=1" class="btn btn-success">+ Tambah Customer</a></div>
                <h2>Data Customer</h2>
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nama Customer</th><th>Alamat</th><th>No Telepon</th><th>Aksi</th></tr></thead>
                    <tbody>';
                while($r=mysqli_fetch_assoc($data)) echo '<tr><td>'.$r['id_customer'].'</td><td>'.htmlspecialchars($r['nama_customer']).'</td><td>'.($r['alamat']?:'-').'</td><td>'.$r['no_telepon'].'</td><td><a href="?page=customer&edit='.$r['id_customer'].'" class="btn btn-warning">Edit</a> <a href="?hapus_customer='.$r['id_customer'].'" class="btn btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</a></td></tr>';
                echo '</tbody></table>';
            }
        }
        
        // PETUGAS
        elseif ($page == 'petugas') {
            if(isset($_GET['edit'])){
                $d=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM petugas WHERE id_petugas=".(int)$_GET['edit']));
                echo '
                <h2>Edit Petugas</h2>
                <div class="form-card">
                    <form method="POST">
                        <input type="hidden" name="id_petugas" value="'.$d['id_petugas'].'">
                        <div class="form-group"><label>Nama Petugas</label><input type="text" name="nama_petugas" value="'.htmlspecialchars($d['nama_petugas']).'" required></div>
                        <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" value="'.$d['jabatan'].'"></div>
                        <button type="submit" name="edit_petugas" class="btn btn-primary">Update</button>
                        <a href="?page=petugas" class="btn btn-outline">Batal</a>
                    </form>
                </div>';
            } elseif(isset($_GET['tambah'])){
                echo '
                <h2>Tambah Petugas</h2>
                <div class="form-card">
                    <form method="POST">
                        <div class="form-group"><label>Nama Petugas</label><input type="text" name="nama_petugas" required></div>
                        <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan"></div>
                        <button type="submit" name="tambah_petugas" class="btn btn-success">Simpan</button>
                        <a href="?page=petugas" class="btn btn-outline">Batal</a>
                    </form>
                </div>';
            } else {
                $data = mysqli_query($conn, "SELECT * FROM petugas ORDER BY id_petugas DESC");
                echo '<div class="action-buttons"><a href="?page=petugas&tambah=1" class="btn btn-success">+ Tambah Petugas</a></div>
                <h2>Data Petugas</h2>
                <table class="data-table"><thead><tr><th>ID</th><th>Nama Petugas</th><th>Jabatan</th><th>Aksi</th></tr></thead><tbody>';
                while($r=mysqli_fetch_assoc($data)) echo '<tr><td>'.$r['id_petugas'].'</td><td>'.htmlspecialchars($r['nama_petugas']).'</td><td>'.$r['jabatan'].'</td><td><a href="?page=petugas&edit='.$r['id_petugas'].'" class="btn btn-warning">Edit</a> <a href="?hapus_petugas='.$r['id_petugas'].'" class="btn btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</a></td></tr>';
                echo '</tbody></table>';
            }
        }
        
        // TRANSAKSI
        elseif ($page == 'transaksi') {
            $data = mysqli_query($conn, "SELECT n.*, c.nama_customer, p.nama_petugas FROM nota n JOIN customer c ON n.id_customer = c.id_customer JOIN petugas p ON n.id_petugas = p.id_petugas ORDER BY n.no_nota DESC");
            echo '<div class="action-buttons"><a href="?page=tambah_transaksi" class="btn btn-success">+ Transaksi Baru</a></div>
            <h2>Data Transaksi</h2>
            <table class="data-table"><thead><tr><th>No Nota</th><th>Tanggal</th><th>Customer</th><th>Petugas</th><th>Total</th><th>Aksi</th></tr></thead><tbody>';
            while($r=mysqli_fetch_assoc($data)) echo '<tr><td>'.$r['no_nota'].'</td><td>'.date('d-m-Y',strtotime($r['tanggal'])).'</td><td>'.htmlspecialchars($r['nama_customer']).'</td><td>'.htmlspecialchars($r['nama_petugas']).'</td><td>Rp '.number_format($r['total'],0,',','.').'</td><td><a href="?page=detail_transaksi&id='.$r['no_nota'].'" class="btn btn-info">Detail</a> <a href="?page=cetak_nota&id='.$r['no_nota'].'" class="btn btn-primary">Cetak</a> <a href="?hapus_transaksi='.$r['no_nota'].'" class="btn btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</a></td></tr>';
            echo '</tbody></table>';
        }
        
        // TAMBAH TRANSAKSI
        elseif ($page == 'tambah_transaksi') {
            $boneka = mysqli_query($conn, "SELECT * FROM boneka");
            $customer = mysqli_query($conn, "SELECT * FROM customer");
            $petugas = mysqli_query($conn, "SELECT * FROM petugas");
            $last = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(no_nota) as max FROM nota"));
            $no_nota = ($last['max'] ?? 0) + 1;
            $total_cart = 0;
            if(isset($_SESSION['cart'])) foreach($_SESSION['cart'] as $item) $total_cart += $item['harga'] * $item['jumlah'];
            
            echo '
            <h2>Tambah Transaksi</h2>
            <div class="form-card" style="max-width:100%;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    <div><label>No Nota</label><input type="text" id="no_nota" value="'.$no_nota.'" readonly></div>
                    <div><label>Tanggal</label><input type="date" id="tanggal_transaksi" value="'.date('Y-m-d').'"></div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    <div><label>Customer</label><select id="select_customer"><option value="">-- Pilih --</option>';
                    while($c=mysqli_fetch_assoc($customer)) echo '<option value="'.$c['id_customer'].'">'.htmlspecialchars($c['nama_customer']).'</option>';
            echo '</select></div>
                    <div><label>Petugas</label><select id="select_petugas"><option value="">-- Pilih --</option>';
                    while($p=mysqli_fetch_assoc($petugas)) echo '<option value="'.$p['id_petugas'].'">'.htmlspecialchars($p['nama_petugas']).'</option>';
            echo '</select></div>
                </div>
                <hr>
                <h3>🧸 Daftar Belanja</h3>
                <div style="display:flex; gap:10px; margin-bottom:20px;">
                    <select id="boneka_select" style="flex:2; padding:12px;">
                        <option value="">-- Pilih Boneka --</option>';
                        while($b=mysqli_fetch_assoc($boneka)) echo '<option value="'.$b['id_boneka'].'" data-nama="'.htmlspecialchars($b['nama_boneka']).'" data-harga="'.$b['harga'].'" data-kode="'.$b['kode_boneka'].'" data-gambar="'.$b['gambar'].'">'.$b['gambar'].' ['.$b['kode_boneka'].'] '.htmlspecialchars($b['nama_boneka']).' - Rp '.number_format($b['harga'],0,',','.').' (Stok: '.$b['stok'].')</option>';
            echo '</select>
                    <input type="number" id="jumlah_boneka" value="1" min="1" style="width:80px; padding:12px;">
                    <button type="button" class="btn btn-primary" onclick="tambahKeKeranjang()">➕ Tambah</button>
                </div>
                <table class="cart-table"><thead><tr><th>Kode</th><th>Nama Boneka</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr></thead><tbody id="cart_body">';
            if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
                $idx=0;
                foreach($_SESSION['cart'] as $item){
                    $subtotal=$item['harga']*$item['jumlah'];
                    echo '<tr id="cart_row_'.$idx.'">
                            <td>'.$item['kode_boneka'].'</td>
                            <td><span style="font-size:18px;">'.$item['gambar'].'</span> '.$item['nama_boneka'].'</td>
                            <td>Rp '.number_format($item['harga'],0,',','.').'</td>
                            <td>'.$item['jumlah'].'</td>
                            <td>Rp '.number_format($subtotal,0,',','.').'</td>
                            <td><a href="?remove_from_cart='.$idx.'" class="btn btn-danger btn-sm" onclick="return confirm(\'Hapus?\')">Hapus</a></td>
                          </tr>';
                    $idx++;
                }
            } else echo '<tr><td colspan="6" style="text-align:center;">Keranjang kosong</td></tr>';
            echo '</tbody></table>
                <div class="total-display">Total: Rp <span id="total_cart">'.number_format($total_cart,0,',','.').'</span></div>
                <button type="button" class="btn btn-success" style="width:100%; margin-top:20px; padding:14px;" onclick="prosesTransaksi()">Proses Transaksi</button>
            </div>
            <script>
            function tambahKeKeranjang(){
                let s=document.getElementById("boneka_select");
                if(!s.value){alert("Pilih boneka!");return;}
                let form=document.createElement("form");form.method="POST";
                form.innerHTML="<input type=\"hidden\" name=\"add_to_cart\" value=\"1\"><input type=\"hidden\" name=\"id_boneka\" value=\""+s.value+"\"><input type=\"hidden\" name=\"kode_boneka\" value=\""+s.options[s.selectedIndex].getAttribute("data-kode")+"\"><input type=\"hidden\" name=\"nama_boneka\" value=\""+s.options[s.selectedIndex].getAttribute("data-nama")+"\"><input type=\"hidden\" name=\"gambar\" value=\""+s.options[s.selectedIndex].getAttribute("data-gambar")+"\"><input type=\"hidden\" name=\"harga\" value=\""+s.options[s.selectedIndex].getAttribute("data-harga")+"\"><input type=\"hidden\" name=\"jumlah\" value=\""+document.getElementById("jumlah_boneka").value+"\">";
                document.body.appendChild(form);form.submit();
            }
            function prosesTransaksi(){
                let c=document.getElementById("select_customer").value;
                let p=document.getElementById("select_petugas").value;
                if(!c){alert("Pilih customer!");return;}
                if(!p){alert("Pilih petugas!");return;}
                let rows=document.getElementById("cart_body").rows;
                if(rows.length===0||(rows[0].cells[0].innerText==="Keranjang kosong")){alert("Keranjang kosong!");return;}
                if(confirm("Proses transaksi?")){
                    let form=document.createElement("form");form.method="POST";
                    form.innerHTML="<input type=\"hidden\" name=\"proses_transaksi\" value=\"1\"><input type=\"hidden\" name=\"no_nota\" value=\""+document.getElementById("no_nota").value+"\"><input type=\"hidden\" name=\"tanggal\" value=\""+document.getElementById("tanggal_transaksi").value+"\"><input type=\"hidden\" name=\"id_customer\" value=\""+c+"\"><input type=\"hidden\" name=\"id_petugas\" value=\""+p+"\">";
                    document.body.appendChild(form);form.submit();
                }
            }
            </script>';
        }
        
        // DETAIL TRANSAKSI
        elseif ($page == 'detail_transaksi') {
            $id = (int)$_GET['id'];
            $nota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT n.*, c.nama_customer, c.alamat, c.no_telepon, p.nama_petugas FROM nota n JOIN customer c ON n.id_customer = c.id_customer JOIN petugas p ON n.id_petugas = p.id_petugas WHERE n.no_nota = $id"));
            $detail = mysqli_query($conn, "SELECT dn.*, b.kode_boneka, b.nama_boneka, b.gambar FROM detail_nota dn JOIN boneka b ON dn.id_boneka = b.id_boneka WHERE dn.no_nota = $id");
            
            if(!$nota){
                echo '<div style="text-align:center; padding:60px;"><h3>Transaksi tidak ditemukan</h3><a href="?page=transaksi" class="btn btn-primary">Kembali</a></div>';
            } else {
                echo '
                <div class="action-buttons">
                    <a href="?page=cetak_nota&id='.$id.'" class="btn btn-primary">🖨️ Cetak Nota</a>
                    <a href="?page=edit_transaksi&id='.$id.'" class="btn btn-warning">✏️ Edit Transaksi</a>
                    <a href="?hapus_transaksi='.$id.'" class="btn btn-danger" onclick="return confirm(\'Yakin hapus?\')">🗑️ Hapus Transaksi</a>
                    <a href="?page=transaksi" class="btn btn-outline">← Kembali</a>
                </div>
                <h2>Detail Transaksi #'.$id.'</h2>
                <div class="form-card" style="max-width:100%;">
                    <h3>Informasi Nota</h3>
                    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:24px;">
                        <div><strong>No Nota</strong><br>'.$nota['no_nota'].'</div>
                        <div><strong>Tanggal</strong><br>'.date('d-m-Y',strtotime($nota['tanggal'])).'</div>
                        <div><strong>Customer</strong><br>'.htmlspecialchars($nota['nama_customer']).'</div>
                        <div><strong>No Telepon</strong><br>'.($nota['no_telepon'] ?: '-').'</div>
                        <div><strong>Alamat</strong><br>'.($nota['alamat'] ?: '-').'</div>
                        <div><strong>Petugas</strong><br>'.htmlspecialchars($nota['nama_petugas']).'</div>
                    </div>
                    <h3>Detail Boneka</h3>
                    <table class="data-table"><thead><tr><th>Kode</th><th>Nama Boneka</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead><tbody>';
                while($d=mysqli_fetch_assoc($detail)) echo '<tr><td>'.$d['kode_boneka'].'</td><td><span style="font-size:18px;">'.$d['gambar'].'</span> '.$d['nama_boneka'].'</td><td>Rp '.number_format($d['harga'],0,',','.').'</td><td>'.$d['jumlah'].' PCS</td><td>Rp '.number_format($d['subtotal'],0,',','.').'</td></tr>';
                echo '<tr style="background:#f8fafc;"><td colspan="4" align="right"><strong>Total</strong></td><td><strong>Rp '.number_format($nota['total'],0,',','.').'</strong></td></tr></tbody></table></div>';
            }
        }
        
        // CETAK NOTA
        elseif ($page == 'cetak_nota') {
            $id = (int)$_GET['id'];
            $nota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT n.*, c.nama_customer, c.alamat, c.no_telepon, p.nama_petugas FROM nota n JOIN customer c ON n.id_customer = c.id_customer JOIN petugas p ON n.id_petugas = p.id_petugas WHERE n.no_nota = $id"));
            $detail = mysqli_query($conn, "SELECT dn.*, b.kode_boneka, b.nama_boneka FROM detail_nota dn JOIN boneka b ON dn.id_boneka = b.id_boneka WHERE dn.no_nota = $id");
            
            if(!$nota){
                echo '<div style="text-align:center; padding:60px;"><h3>Transaksi tidak ditemukan</h3><a href="?page=transaksi" class="btn btn-primary">Kembali</a></div>';
            } else {
                echo '
                <div class="action-buttons">
                    <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Nota</button>
                    <a href="?page=detail_transaksi&id='.$id.'" class="btn btn-outline">📋 Kembali</a>
                    <a href="?page=transaksi" class="btn btn-outline">📋 Daftar Transaksi</a>
                </div>
                <div class="nota-wrapper">
                    <div class="nota">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:15px; border-bottom:2px dashed #e2e8f0; padding-bottom:12px;">
                            <div style="width:50px; height:50px; flex-shrink:0;">
                                <img src="assets/logo.jpeg" alt="Logo" style="width:100%; height:100%; object-fit:contain;" onerror="this.style.display=\'none\'">
                            </div>
                            <div style="text-align:left;">
                                <h2 style="color:#1a2a4f; font-size:14px; margin:0;">PD ZULPA NUR</h2>
                                <p style="font-size:8px; margin:2px 0; color:#7a8a9e;">Jl Perjuangan Rt. 04/04 No. 39 Gg. Eman Ebor</p>
                                <p style="font-size:8px; margin:2px 0; color:#7a8a9e;">Kel. Jati Sari Kec. Jati Asih Kota Bekasi 17426</p>
                                <p style="font-size:8px; margin:2px 0; color:#7a8a9e;">Telp 0838 0710 9757</p>
                            </div>
                        </div>
                        <div class="nota-info">
                            <table style="width:100%;">
                                <tr><td width="80">No. Nota</td><td>: '.$nota['no_nota'].'</td></tr>
                                <tr><td>Tanggal</td><td>: '.date('d-m-Y',strtotime($nota['tanggal'])).'</td></tr>
                                <tr><td>Customer</td><td colspan="2">: '.htmlspecialchars($nota['nama_customer']).'</td></tr>
                            </table>
                        </div>
                        <table class="nota-items">
                            <thead><tr><th>No</th><th>Banyaknya</th><th>Item</th><th>Harga</th><th>Jumlah</th></tr></thead>
                            <tbody>';
                $no=1;
                while($d=mysqli_fetch_assoc($detail)){
                    echo '<tr>
                            <td>'.$no++.'</td>
                            <td>'.$d['jumlah'].' PCS</td>
                            <td>'.$d['kode_boneka'].' - '.$d['nama_boneka'].'</td>
                            <td align="right">'.number_format($d['harga'],0,',','.').'</td>
                            <td align="right">'.number_format($d['subtotal'],0,',','.').'</td>
                          </tr>';
                }
                echo '</tbody>
                        </table>
                        <div class="nota-total">Total: Rp '.number_format($nota['total'],0,',','.').'</div>
                        <div class="ttd">
                            <div>Hormat Kami,<br><br><br>(__________________)</div>
                            <div>Penerima,<br><br><br>(__________________)</div>
                        </div>
                    </div>
                </div>';
            }
        }
        ?>
    </div>
</div>
</body>
</html>