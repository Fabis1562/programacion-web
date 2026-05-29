<?php
require_once 'backend/config.php';
try {
    // 1. Añadir roles y campos de recuperación a usuarios
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS rol VARCHAR(20) DEFAULT 'user'");
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255)");
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS reset_expires TIMESTAMP");

    // 2. Crear tabla de reseñas
    $sqlResenas = "CREATE TABLE IF NOT EXISTS resenas (
        id SERIAL PRIMARY KEY,
        especialista_id INT REFERENCES especialistas(id),
        autor_id INT REFERENCES usuarios(id),
        calificacion INT CHECK (calificacion >= 1 AND calificacion <= 5),
        comentario TEXT,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlResenas);

    // 3. Convertir al primer usuario en administrador para poder probar el panel (Opcional)
    // Buscamos al usuario de id 1 o al primer correo registrado
    $pdo->exec("UPDATE usuarios SET rol = 'admin' WHERE id = 1");

    echo "Base de datos actualizada con éxito para las nuevas funcionalidades.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
