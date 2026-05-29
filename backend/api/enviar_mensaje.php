<?php
// backend/api/enviar_mensaje.php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Debes iniciar sesión para enviar un mensaje."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->especialista_id) && !empty($data->contenido)) {
    try {
        $remitente_id = $_SESSION['usuario_id'];
        $especialista_id = intval($data->especialista_id);
        $contenido = htmlspecialchars(strip_tags($data->contenido));

        $query = "INSERT INTO mensajes (remitente_id, especialista_id, contenido) VALUES (:remitente, :especialista, :contenido)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":remitente", $remitente_id);
        $stmt->bindParam(":especialista", $especialista_id);
        $stmt->bindParam(":contenido", $contenido);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Mensaje enviado."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "No se pudo enviar el mensaje."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Faltan datos."]);
}
?>
