<?php
// backend/test_db.php

// Mostrar errores en pantalla para facilitar la depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requerir el archivo de configuración
require_once 'config.php';

// Si llegamos a esta línea sin que el script muera (die), la conexión fue exitosa.
echo "<div style='font-family: Arial, sans-serif; padding: 20px; text-align: center;'>";
echo "<h1 style='color: #4CAF50;'>¡Conexión Exitosa! ✅</h1>";
echo "<p>PHP se ha conectado correctamente a tu base de datos PostgreSQL (<b>$dbname</b>).</p>";
echo "<hr style='border: 1px solid #ddd; margin: 20px 0;'>";

try {
    // Vamos a probar hacer una consulta sencilla
    // Verificamos si la tabla 'especialistas' existe
    $stmt = $pdo->query("SELECT to_regclass('public.especialistas')");
    $tablaExiste = $stmt->fetchColumn();

    if ($tablaExiste) {
        // Consultar cuántos especialistas hay
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM especialistas");
        $row = $stmt->fetch();
        $totalEspecialistas = $row['total'];
        
        echo "<p>Se encontró la tabla <b>especialistas</b> con $totalEspecialistas registro(s).</p>";
    } else {
        echo "<p style='color: #f44336;'>La base de datos está conectada, pero la tabla <b>especialistas</b> aún no existe o no tiene el esquema correcto.</p>";
        echo "<p>Recuerda ejecutar tu archivo <code>schema.sql</code> en pgAdmin o psql.</p>";
    }

} catch (PDOException $e) {
    echo "<h3 style='color: #f44336;'>Error ejecutando consulta de prueba:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "</div>";
?>
