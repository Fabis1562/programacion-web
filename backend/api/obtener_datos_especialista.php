<?php
// backend/api/obtener_datos_especialista.php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "No autorizado."]);
    exit();
}

try {
    $query = "SELECT categoria_id, rol_especifico, ubicacion, bio FROM especialistas WHERE usuario_id = :usuario_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":usuario_id", $_SESSION['usuario_id']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "is_especialista" => true,
            "data" => $data
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "is_especialista" => false
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
