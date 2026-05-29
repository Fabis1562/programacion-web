<?php
// backend/config.php

// Intentar leer de las variables de entorno reales del sistema (común en servidores de producción como Railway, Clever Cloud, etc.)
$host = getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : null);
$port = getenv('DB_PORT') ?: (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : null);
$dbname = getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : null);
$user = getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : null);
$password = getenv('DB_PASSWORD') ?: (isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : null);

// Si no están definidas en el sistema, intentar leer del archivo local .env
if (!$host || !$dbname || !$user) {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $env = parse_ini_file($envFile);
        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $port = $env['DB_PORT'] ?? '5432';
        $dbname = $env['DB_NAME'] ?? 'eclapro_d';
        $user = $env['DB_USER'] ?? 'postgres';
        $password = $env['DB_PASSWORD'] ?? '1564';
    } else {
        die(json_encode(['status' => 'error', 'message' => 'Falta la configuración de la base de datos (variables de entorno o archivo .env).']));
    }
}

// DSN (Data Source Name) para PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    // Crear una instancia de PDO
    $pdo = new PDO($dsn, $user, $password);
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar el modo de obtención de datos por defecto a Array Asociativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si hay un error, mostrar el mensaje y detener la ejecución (solo para desarrollo)
    // En producción es mejor guardar el error en un log y mostrar un mensaje genérico.
    die(json_encode([
        'status' => 'error',
        'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
    ]));
}
?>
