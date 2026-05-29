<?php
// backend/api/subir_datos_especialista.php
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

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['usuario_nombre']; // Usamos el nombre del usuario

// Para soportar FormData, los datos vienen en $_POST en lugar de php://input (json)
$categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
$rol_especifico = isset($_POST['rol_especifico']) ? htmlspecialchars(strip_tags($_POST['rol_especifico'])) : null;
$ubicacion = isset($_POST['ubicacion']) ? htmlspecialchars(strip_tags($_POST['ubicacion'])) : null;
$bio = isset($_POST['bio']) ? htmlspecialchars(strip_tags($_POST['bio'])) : '';

if ($categoria_id && $rol_especifico && $ubicacion) {
    try {
        // Manejo de la subida de imagen
        $imagen_url = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['imagen']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Crear un nombre único para la imagen
                $newName = "perfil_" . $usuario_id . "_" . time() . "." . $ext;
                $uploadPath = "../uploads/" . $newName;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
                    // La URL que se guardará en la base de datos
                    $imagen_url = "../backend/uploads/" . $newName;
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo guardar la imagen."]);
                    exit();
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Formato de imagen no válido."]);
                exit();
            }
        }

        // Verificar si ya existe un registro para este usuario
        $queryCheck = "SELECT id, imagen_url FROM especialistas WHERE usuario_id = :usuario_id";
        $stmtCheck = $pdo->prepare($queryCheck);
        $stmtCheck->bindParam(":usuario_id", $usuario_id);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Ya existe -> Actualizar
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            // Si no se subió una imagen nueva, mantenemos la anterior
            if ($imagen_url === null) {
                $imagen_url = $row['imagen_url'];
            }

            $queryUpdate = "UPDATE especialistas 
                            SET categoria_id = :categoria_id, rol_especifico = :rol_especifico, 
                                ubicacion = :ubicacion, bio = :bio, nombre = :nombre, imagen_url = :imagen_url 
                            WHERE usuario_id = :usuario_id";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->bindParam(":categoria_id", $categoria_id);
            $stmtUpdate->bindParam(":rol_especifico", $rol_especifico);
            $stmtUpdate->bindParam(":ubicacion", $ubicacion);
            $stmtUpdate->bindParam(":bio", $bio);
            $stmtUpdate->bindParam(":nombre", $nombre);
            $stmtUpdate->bindParam(":imagen_url", $imagen_url);
            $stmtUpdate->bindParam(":usuario_id", $usuario_id);

            if ($stmtUpdate->execute()) {
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Datos actualizados correctamente."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "No se pudo actualizar los datos."]);
            }
        } else {
            // Insertar nuevo
            $queryInsert = "INSERT INTO especialistas (nombre, categoria_id, rol_especifico, ubicacion, bio, usuario_id, imagen_url, verificado) 
                            VALUES (:nombre, :categoria_id, :rol_especifico, :ubicacion, :bio, :usuario_id, :imagen_url, FALSE)";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->bindParam(":nombre", $nombre);
            $stmtInsert->bindParam(":categoria_id", $categoria_id);
            $stmtInsert->bindParam(":rol_especifico", $rol_especifico);
            $stmtInsert->bindParam(":ubicacion", $ubicacion);
            $stmtInsert->bindParam(":bio", $bio);
            $stmtInsert->bindParam(":usuario_id", $usuario_id);
            $stmtInsert->bindParam(":imagen_url", $imagen_url);

            if ($stmtInsert->execute()) {
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Te has registrado como especialista correctamente."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "No se pudo registrar."]);
            }
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
}
?>
