<?php
// backend/api/obtener_mensajes.php
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

    $query = "SELECT m.id, m.contenido, m.creado_en, m.leido, u.nombre as remitente_nombre, u.email as remitente_email
              FROM mensajes m
              JOIN usuarios u ON m.remitente_id = u.id
              JOIN especialistas e ON m.especialista_id = e.id
              WHERE e.usuario_id = :usuario_id
              ORDER BY m.creado_en DESC";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":usuario_id", $usuario_id);
    $stmt->execute();

    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Marcar como leídos
    if (count($mensajes) > 0) {
        $updateQuery = "UPDATE mensajes m 
                        SET leido = TRUE 
                        FROM especialistas e 
                        WHERE m.especialista_id = e.id AND e.usuario_id = :usuario_id AND m.leido = FALSE";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(":usuario_id", $usuario_id);
        $updateStmt->execute();
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $mensajes
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
