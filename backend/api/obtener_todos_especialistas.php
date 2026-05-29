<?php
// backend/api/obtener_todos_especialistas.php
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
    // Verificar que sea admin
    $stmtAdmin = $pdo->prepare("SELECT rol FROM usuarios WHERE id = :id");
    $stmtAdmin->bindParam(":id", $_SESSION['usuario_id']);
    $stmtAdmin->execute();
    $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($user['rol'] !== 'admin') {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Acceso denegado. No eres administrador."]);
        exit();
    }

    $query = "SELECT e.*, c.nombre as categoria_nombre 
              FROM especialistas e 
              LEFT JOIN categorias c ON e.categoria_id = c.id
              ORDER BY e.id DESC";
              
    $stmt = $pdo->query($query);
    $especialistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $especialistas
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
