<?php
// backend/api/actualizar_perfil.php
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

if (!empty($data->nombre) && !empty($data->email)) {
    try {
        // Verificar si el nuevo email ya está en uso por otro usuario
        $queryCheck = "SELECT id FROM usuarios WHERE email = :email AND id != :id";
        $stmtCheck = $pdo->prepare($queryCheck);
        $stmtCheck->bindParam(":email", $data->email);
        $stmtCheck->bindParam(":id", $_SESSION['usuario_id']);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El correo electrónico ya está en uso por otra cuenta."]);
            exit();
        }

        // Actualizar el usuario
        $queryUpdate = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
        $stmtUpdate = $pdo->prepare($queryUpdate);
        
        $nombre = htmlspecialchars(strip_tags($data->nombre));
        $email = htmlspecialchars(strip_tags($data->email));
        
        $stmtUpdate->bindParam(":nombre", $nombre);
        $stmtUpdate->bindParam(":email", $email);
        $stmtUpdate->bindParam(":id", $_SESSION['usuario_id']);

        if ($stmtUpdate->execute()) {
            // Actualizar la sesión también
            $_SESSION['usuario_nombre'] = $nombre;
            
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Perfil actualizado correctamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar el perfil."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error de base de datos."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos incompletos. Se requiere nombre y email."]);
}
?>
