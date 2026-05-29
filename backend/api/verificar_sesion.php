<?php
// backend/api/verificar_sesion.php
header("Access-Control-Allow-Origin: http://localhost"); // Ajusta esto si tu frontend está en otro dominio
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once '../config.php';

if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_nombre'])) {
    
    // Obtener rol de la base de datos para estar siempre actualizados
    $rol = 'user';
    try {
        $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = :id");
        $stmt->bindParam(":id", $_SESSION['usuario_id']);
        $stmt->execute();
        $userDb = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userDb) {
            $rol = $userDb['rol'];
        }
    } catch(PDOException $e) {}

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "logged_in" => true,
        "usuario" => [
            "id" => $_SESSION['usuario_id'],
            "nombre" => $_SESSION['usuario_nombre'],
            "rol" => $rol
        ]
    ]);
} else {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "logged_in" => false
    ]);
}
?>
