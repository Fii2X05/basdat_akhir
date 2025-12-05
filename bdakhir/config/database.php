<?php
$host = "localhost";
$port = "5432";
$db   = "siakad";
$user = "postgres";
$pass = "rafizf2005";  

$pdo = null;

try {
    // Create DSN (Data Source Name)
    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
    
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass);
    
    // Set error mode ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode ke associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Set charset ke UTF8
    $pdo->exec("SET NAMES 'UTF8'");
    
} catch (PDOException $e) {
    $errorMsg = "
    <div style='
        max-width: 800px; 
        margin: 50px auto; 
        padding: 30px; 
        background: #f8d7da; 
        border: 2px solid #f5c6cb; 
        border-radius: 10px;
        font-family: Arial, sans-serif;
    '>
        <h2 style='color: #721c24; margin-top: 0;'>❌ Database Connection Error</h2>
        <p style='color: #721c24; font-size: 16px;'><strong>Error Message:</strong></p>
        <pre style='background: white; padding: 15px; border-radius: 5px; color: #d9534f;'>" . htmlspecialchars($e->getMessage()) . "</pre>
        
        <hr style='border-color: #f5c6cb;'>
        
        <h3 style='color: #856404;'>🔧 Troubleshooting:</h3>
        <ol style='color: #721c24; line-height: 1.8;'>
            <li><strong>Check PostgreSQL Service:</strong> Pastikan PostgreSQL sudah running di Laragon</li>
            <li><strong>Check Database:</strong> Pastikan database '<strong>siakad</strong>' sudah dibuat</li>
            <li><strong>Check Credentials:</strong> 
                <ul>
                    <li>Host: <code>{$host}</code></li>
                    <li>Port: <code>{$port}</code></li>
                    <li>Database: <code>{$db}</code></li>
                    <li>Username: <code>{$user}</code></li>
                    <li>Password: <code>[hidden]</code> ← Periksa ini!</li>
                </ul>
            </li>
            <li><strong>Test Manual Connection:</strong> Buka pgAdmin atau psql dan coba login dengan credentials di atas</li>
        </ol>
        
        <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;'>
            <strong style='color: #856404;'>💡 Quick Fix:</strong>
            <p style='color: #856404; margin: 10px 0 0 0;'>
                1. Buka Laragon → Start PostgreSQL<br>
                2. Buka pgAdmin → Create database 'siakad'<br>
                3. Edit <code>config/database.php</code> → Sesuaikan password<br>
                4. Refresh halaman ini
            </p>
        </div>
    </div>
    ";
    
    die($errorMsg);
}

if (!$pdo) {
    die("
        <div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #f8d7da; border-radius: 5px;'>
            <h3 style='color: #721c24;'>❌ PDO Object Not Created</h3>
            <p style='color: #721c24;'>Koneksi database gagal tetapi tidak ada exception yang ditangkap.</p>
        </div>
    ");
}

?>