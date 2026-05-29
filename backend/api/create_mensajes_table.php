<?php
require_once 'backend/config.php';
try {
    $sql = "CREATE TABLE IF NOT EXISTS mensajes (
        id SERIAL PRIMARY KEY,
        remitente_id INT REFERENCES usuarios(id),
        especialista_id INT REFERENCES especialistas(id),
        contenido TEXT NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";
    $pdo->exec($sql);
    echo "Tabla mensajes creada correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
