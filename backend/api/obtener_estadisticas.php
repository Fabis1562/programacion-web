<?php
// backend/api/obtener_estadisticas.php
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

$usuario_id = $_SESSION['usuario_id'];

try {
    // 1. Obtener la información base del usuario
    $query = "SELECT id, nombre, email, creado_en FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $usuario_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificar si es especialista para traer estadísticas extra
    $queryEsp = "SELECT id, rating, verificado FROM especialistas WHERE usuario_id = :usuario_id";
    $stmtEsp = $pdo->prepare($queryEsp);
    $stmtEsp->bindParam(":usuario_id", $usuario_id);
    $stmtEsp->execute();
    
    $stats = [
        "is_especialista" => false,
        "total_mensajes" => 0,
        "mensajes_no_leidos" => 0,
        "rating_promedio" => 0,
        "total_resenas" => 0,
        "verificado" => false
    ];

    if ($stmtEsp->rowCount() > 0) {
        $esp = $stmtEsp->fetch(PDO::FETCH_ASSOC);
        $especialista_id = $esp['id'];
        $stats['is_especialista'] = true;
        $stats['rating_promedio'] = floatval($esp['rating']);
        $stats['verificado'] = (bool)$esp['verificado'];

        // Contar mensajes
        $stmtMsg = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN leido = FALSE THEN 1 ELSE 0 END) as no_leidos FROM mensajes WHERE especialista_id = :esp_id");
        $stmtMsg->bindParam(":esp_id", $especialista_id);
        $stmtMsg->execute();
        $msgData = $stmtMsg->fetch(PDO::FETCH_ASSOC);
        
        $stats['total_mensajes'] = intval($msgData['total']);
        $stats['mensajes_no_leidos'] = intval($msgData['no_leidos']);

        // Contar reseñas
        $stmtRes = $pdo->prepare("SELECT COUNT(*) as total_resenas FROM resenas WHERE especialista_id = :esp_id");
        $stmtRes->bindParam(":esp_id", $especialista_id);
        $stmtRes->execute();
        $resData = $stmtRes->fetch(PDO::FETCH_ASSOC);
        
        $stats['total_resenas'] = intval($resData['total_resenas']);
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success", 
        "usuario" => $usuario,
        "estadisticas" => $stats
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
}
?>
