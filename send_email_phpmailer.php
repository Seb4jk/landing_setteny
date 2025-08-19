<?php
// Configuración de cabeceras para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Verificar si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
    exit;
}

// Incluir Composer autoloader (necesario para PHPMailer)
// Nota: Necesitarás ejecutar 'composer install' para instalar PHPMailer
require 'vendor/autoload.php';

// Importar clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Recibir datos del formulario
$data = json_decode(file_get_contents("php://input"), true);

// Verificar datos requeridos
if (!isset($data["nombre"]) || !isset($data["correo"]) || !isset($data["asunto"])) {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos"]);
    exit;
}

// Datos del formulario
$nombre = $data["nombre"];
$empresa = isset($data["empresa"]) ? $data["empresa"] : "No especificada";
$correo = $data["correo"];
$telefono = isset($data["telefono"]) ? $data["telefono"] : "No especificado";
$mensaje = $data["asunto"];

// Crear el cuerpo del mensaje
$email_body = "Has recibido un nuevo mensaje desde el formulario de contacto.<br><br>";
$email_body .= "<strong>Detalles:</strong><br>";
$email_body .= "<strong>Nombre:</strong> " . htmlspecialchars($nombre) . "<br>";
$email_body .= "<strong>Empresa:</strong> " . htmlspecialchars($empresa) . "<br>";
$email_body .= "<strong>Correo:</strong> " . htmlspecialchars($correo) . "<br>";
$email_body .= "<strong>Teléfono:</strong> " . htmlspecialchars($telefono) . "<br>";
$email_body .= "<strong>Mensaje:</strong><br>" . nl2br(htmlspecialchars($mensaje)) . "<br>";

try {
    // Crear una nueva instancia de PHPMailer
    $mail = new PHPMailer(true);
    
    // Configuración del servidor
    $mail->isSMTP();                                      // Usar SMTP
    $mail->Host       = 'mail.setteny.cl';                // Servidor SMTP de cPanel
    $mail->SMTPAuth   = true;                             // Habilitar autenticación SMTP
    $mail->Username   = 'formularioweb@setteny.cl';        // Usuario SMTP
    $mail->Password   = 'CGY,p=UxgqRB*v';                 // Contraseña SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // Habilitar encriptación SSL
    $mail->Port       = 465;                              // Puerto TCP para conectarse
    
    // Destinatarios
    $mail->setFrom('formularioweb@setteny.cl', 'Formulario Web Setteny Consulting');
    $mail->addAddress('formularioweb@setteny.cl');   // Añadir destinatario
    $mail->addReplyTo($correo, $nombre);                  // Responder a
    
    // Contenido
    $mail->isHTML(true);                                  // Formato HTML
    $mail->Subject = 'Nuevo mensaje de contacto desde el sitio web';
    $mail->Body    = $email_body;
    $mail->AltBody = strip_tags(str_replace("<br>", "\n", $email_body));
    
    // Enviar correo
    $mail->send();
    echo json_encode(["success" => true, "message" => "Mensaje enviado correctamente"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al enviar el mensaje: " . $mail->ErrorInfo]);
}
?>
