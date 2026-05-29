<?php
// backend/api/registro.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config.php';

// Leer los datos recibidos (esperamos JSON)
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nombre) && !empty($data->email) && !empty($data->password)) {
    try {
        // Verificar si el correo ya existe
        $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt_check->bindParam(':email', $data->email);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El correo electrónico ya está registrado."]);
            exit;
        }

        // Encriptar la contraseña
        $password_hash = password_hash($data->password, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario
        $query = "INSERT INTO usuarios (nombre, email, password_hash) VALUES (:nombre, :email, :password_hash)";
        $stmt = $pdo->prepare($query);

        // Limpiar los datos
        $nombre = htmlspecialchars(strip_tags($data->nombre));
        $email = htmlspecialchars(strip_tags($data->email));

        // Asignar parámetros
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password_hash", $password_hash);

        if ($stmt->execute()) {
            http_response_code(201); // Creado
            echo json_encode(["status" => "success", "message" => "Usuario registrado exitosamente."]);
        } else {
            http_response_code(503); // Servicio no disponible
            echo json_encode(["status" => "error", "message" => "No se pudo registrar el usuario."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()]);
    }
} else {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["status" => "error", "message" => "Datos incompletos. Se requiere nombre, email y contraseña."]);
}
?>
