<?php
// backend/api/obtener_especialistas.php

// Permitir peticiones desde cualquier origen (CORS) - útil si frontend y backend están en puertos distintos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Incluir la conexión a la base de datos
require_once '../config.php';

try {
    // Consulta SQL para obtener los especialistas junto con el nombre de su categoría
    $query = "
        SELECT 
            e.id, 
            e.nombre, 
            e.rol_especifico, 
            e.rating, 
            e.ubicacion, 
            e.bio, 
            e.imagen_url, 
            e.verificado,
            c.nombre as categoria_nombre,
            c.icono_clase
        FROM especialistas e
        JOIN categorias c ON e.categoria_id = c.id
        ORDER BY e.rating DESC
    ";
    
    // Ejecutar la consulta
    $stmt = $pdo->query($query);
    
    // Obtener los resultados en un arreglo
    $especialistas = $stmt->fetchAll();
    
    // Devolver los datos en formato JSON
    echo json_encode([
        "status" => "success",
        "data" => $especialistas
    ]);

} catch (PDOException $e) {
    // Si hay un error, devolverlo en formato JSON
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error al obtener especialistas: " . $e->getMessage()
    ]);
}
?>
