<?php
// backend/api/solicitar_recuperacion.php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email)) {
    try {
        $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);

        // Verificar si existe el usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generar token seguro
            $token = bin2hex(random_bytes(32));
            // Expira en 1 hora
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Guardar token en BD
            $update = $pdo->prepare("UPDATE usuarios SET reset_token = :token, reset_expires = :expires WHERE id = :id");
            $update->bindParam(":token", $token);
            $update->bindParam(":expires", $expires);
            $update->bindParam(":id", $user['id']);
            $update->execute();

            // Generar enlace dinámico según si es local (XAMPP con subcarpeta) o producción (Clever Cloud)
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            if ($host === 'localhost' || $host === '127.0.0.1' || strpos($host, '192.168.') === 0) {
                $enlace = "http://" . $host . "/programacion%20web/frontend/restablecer.html?token=" . $token;
            } else {
                // En producción (Clever Cloud)
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                if (strpos($host, 'cleverapps.io') !== false) {
                    $protocol = "https"; // Clever Cloud siempre soporta y fuerza HTTPS
                }
                $enlace = $protocol . "://" . $host . "/frontend/restablecer.html?token=" . $token;
            }

            // Enviar correo real usando PHPMailer
            require '../libs/PHPMailer/Exception.php';
            require '../libs/PHPMailer/PHPMailer.php';
            require '../libs/PHPMailer/SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'conectapro202605@gmail.com'; 
                $mail->Password   = 'tarzkbalghymtdxg'; // Contraseña de aplicación
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('conectapro202605@gmail.com', 'ConectaPro Soporte');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Recupera tu contraseña de ConectaPro';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                        <h2 style='color: #4f46e5; text-align: center;'>Recuperación de Contraseña</h2>
                        <p>Hola,</p>
                        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en ConectaPro.</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$enlace}' style='background-color: #4f46e5; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Crear nueva contraseña</a>
                        </div>
                        <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                        <p style='word-break: break-all; color: #666;'>{$enlace}</p>
                        <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                        <p style='font-size: 12px; color: #999;'>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                    </div>
                ";

                $mail->send();
                
                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "¡El correo se ha enviado exitosamente a tu bandeja de entrada!"
                ]);
            } catch (Exception $e) {
                // Si falla por la contraseña de Google
                http_response_code(500);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Error de SMTP de Google. Es necesario crear una 'Contraseña de Aplicación' en tu cuenta de Google. Error: {$mail->ErrorInfo}"
                ]);
            }
        } else {
            // No existe
            http_response_code(200);
            echo json_encode(["status" => "error", "message" => "Si el correo está registrado, recibirás un enlace en breve."]);
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
