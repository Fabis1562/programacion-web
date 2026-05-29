<?php
// backend/api/login.php
header("Access-Control-Allow-Origin: http://localhost"); // Ajusta esto si tu frontend está en otro dominio
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config.php';

session_start();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    try {
        $query = "SELECT id, nombre, password_hash FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($query);
        
        $email = htmlspecialchars(strip_tags($data->email));
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            $nombre = $row['nombre'];
            $password_hash = $row['password_hash'];

            // Verificar la contraseña
            if (password_verify($data->password, $password_hash)) {
                // Iniciar sesión
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_nombre'] = $nombre;

                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Inicio de sesión exitoso.",
                    "usuario" => [
                        "id" => $id,
                        "nombre" => $nombre
                    ]
                ]);
            } else {
                http_response_code(401); // No autorizado
                echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
            }
        } else {
            http_response_code(404); // No encontrado
            echo json_encode(["status" => "error", "message" => "El correo no está registrado."]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos incompletos. Se requiere email y contraseña."]);
}
?>
