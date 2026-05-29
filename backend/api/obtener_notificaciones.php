<?php
// backend/api/obtener_notificaciones.php
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
    $usuario_id = $_SESSION['usuario_id'];

    // Contar mensajes no leídos para el especialista asociado a este usuario
    $query = "SELECT COUNT(m.id) as no_leidos 
              FROM mensajes m 
              JOIN especialistas e ON m.especialista_id = e.id 
              WHERE e.usuario_id = :usuario_id AND m.leido = FALSE";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":usuario_id", $usuario_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "no_leidos" => intval($result['no_leidos'])
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
