<?php
// backend/api/obtener_perfil.php
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
    $query = "SELECT id, nombre, email, creado_en FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $_SESSION['usuario_id']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "usuario" => $usuario
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Usuario no encontrado."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
