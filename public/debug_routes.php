<!DOCTYPE html>
<html>
<head>
    <title>Route Debug Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; }
        pre { background: #333; color: #fff; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>CodeIgniter Route Debug Test</h1>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    try {
        // Load environment
        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../app/Config/Paths.php';
        $paths = new Config\Paths();
        require_once rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
        require_once SYSTEMPATH . 'Config/DotEnv.php';
        (new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
    
    echo "<div class='info'>";
    echo "<h3>Environment Check:</h3>";
    echo "GOOGLE_OAUTH_CREDENTIALS_PATH: <strong>" . ($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? 'NOT SET') . "</strong><br>";
    echo "GOOGLE_REDIRECT_URI: <strong>" . ($_ENV['GOOGLE_REDIRECT_URI'] ?? 'NOT SET') . "</strong><br>";
    echo "GOOGLE_DRIVE_FOLDER_ID: <strong>" . ($_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? 'NOT SET') . "</strong><br>";
    echo "Credentials file exists: <strong>" . (file_exists($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? '') ? 'YES ✓' : 'NO ✗') . "</strong><br>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>Available Routes:</h3>";
    echo "Try these URLs:<br>";
    echo "1. <a href='/HRMO/public/google/redirectToGoogle'>/google/redirectToGoogle</a><br>";
    echo "2. <a href='/HRMO/public/dashboard'>/dashboard</a><br>";
    echo "3. <a href='/HRMO/public/'>Home (/)</a><br>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>Controller Check:</h3>";
    $controllerFile = __DIR__ . '/../app/Controllers/GoogleAuth.php';
    echo "GoogleAuth.php exists: <strong>" . (file_exists($controllerFile) ? 'YES ✓' : 'NO ✗') . "</strong><br>";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        echo "Class exists: <strong>" . (class_exists('App\Controllers\GoogleAuth') ? 'YES ✓' : 'NO ✗') . "</strong><br>";
        echo "Method redirectToGoogle exists: <strong>" . (method_exists('App\Controllers\GoogleAuth', 'redirectToGoogle') ? 'YES ✓' : 'NO ✗') . "</strong><br>";
    }
    echo "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<h3>ERROR:</h3>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
        echo "</div>";
    }
    ?>
    
    <p class="success">If you see this page, PHP and CodeIgniter are working!</p>
</body>
</html>
