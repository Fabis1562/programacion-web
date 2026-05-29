<?php
require_once 'backend/config.php';
try {
    $nuevas = [
        ['Carpintería', 'fa-solid fa-hammer'],
        ['Limpieza', 'fa-solid fa-broom'],
        ['Jardinería', 'fa-solid fa-leaf'],
        ['Mecánica', 'fa-solid fa-wrench'],
        ['Pintura', 'fa-solid fa-paint-roller']
    ];

    foreach ($nuevas as $cat) {
        $query = "INSERT INTO categorias (nombre, icono_clase) VALUES (:nombre, :icono) ON CONFLICT (nombre) DO NOTHING";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":nombre", $cat[0]);
        $stmt->bindParam(":icono", $cat[1]);
        $stmt->execute();
    }
    echo "Categorías añadidas correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
