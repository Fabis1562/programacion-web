<?php
require_once 'backend/config.php';
try {
    $sql = "ALTER TABLE especialistas ADD COLUMN IF NOT EXISTS usuario_id INT REFERENCES usuarios(id);";
    $pdo->exec($sql);
    echo "Columna usuario_id añadida correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
