<?php
// backend/api/verificar_especialista.php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "No autorizado."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (isset($data->especialista_id) && isset($data->verificado)) {
    try {
        $stmtAdmin = $pdo->prepare("SELECT rol FROM usuarios WHERE id = :id");
        $stmtAdmin->bindParam(":id", $_SESSION['usuario_id']);
        $stmtAdmin->execute();
        $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        if ($user['rol'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Acceso denegado. No eres administrador."]);
            exit();
        }

        $query = "UPDATE especialistas SET verificado = :verificado WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $verificado = filter_var($data->verificado, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        $stmt->bindParam(":verificado", $verificado);
        $stmt->bindParam(":id", $data->especialista_id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Estado actualizado."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar el estado."]);
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
