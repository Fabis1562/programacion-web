<?php
// backend/api/obtener_categorias.php
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config.php';

try {
    $query = "SELECT id, nombre, icono_clase FROM categorias ORDER BY nombre ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $categorias
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
