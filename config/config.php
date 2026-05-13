<?php
// Change APP_BASE if your folder name is different inside htdocs.
define('APP_BASE', '/projet_js');
define('APP_NAME', 'Dar Tunisie');
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('UPLOAD_URL', APP_BASE . '/public/uploads/');

// MySQL settings for XAMPP. Change them for production hosting.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'tunisie_logement');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set('Africa/Tunis');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
