<?php
// backend/api/calificar_especialista.php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Debes iniciar sesión para calificar."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->especialista_id) && !empty($data->calificacion)) {
    try {
        $id = intval($data->especialista_id);
        $calificacion = intval($data->calificacion);
        $comentario = isset($data->comentario) ? htmlspecialchars(strip_tags($data->comentario)) : null;
        $autor_id = $_SESSION['usuario_id'];

        // Insertar la reseña en la tabla
        $queryResena = "INSERT INTO resenas (especialista_id, autor_id, calificacion, comentario) 
                        VALUES (:especialista_id, :autor_id, :calificacion, :comentario)";
        $stmtResena = $pdo->prepare($queryResena);
        $stmtResena->bindParam(":especialista_id", $id);
        $stmtResena->bindParam(":autor_id", $autor_id);
        $stmtResena->bindParam(":calificacion", $calificacion);
        $stmtResena->bindParam(":comentario", $comentario);
        $stmtResena->execute();

        // Actualizar el promedio en la tabla especialistas
        $queryPromedio = "UPDATE especialistas 
                          SET rating = (SELECT AVG(calificacion) FROM resenas WHERE especialista_id = :id) 
                          WHERE id = :id";
        $stmtPromedio = $pdo->prepare($queryPromedio);
        $stmtPromedio->bindParam(":id", $id);
        
        if ($stmtPromedio->execute()) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Calificación y reseña guardadas."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar el promedio."]);
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
