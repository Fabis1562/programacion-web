<?php
// backend/api/restablecer_password.php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->token) && !empty($data->password)) {
    try {
        $token = $data->token;
        $nueva_password = password_hash($data->password, PASSWORD_DEFAULT);

        // Buscar el usuario con ese token y que no haya expirado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND reset_expires >= CURRENT_TIMESTAMP");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Actualizar la contraseña y limpiar el token
            $update = $pdo->prepare("UPDATE usuarios SET password_hash = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id");
            $update->bindParam(":password", $nueva_password);
            $update->bindParam(":id", $user['id']);
            
            if($update->execute()){
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Contraseña actualizada correctamente."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "No se pudo actualizar la contraseña."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El enlace es inválido o ha expirado."]);
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
