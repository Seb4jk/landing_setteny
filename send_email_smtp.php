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

// Configuración SMTP - Reemplaza estos valores con tus credenciales de cPanel
$smtp_host = "mail.setteny.cl"; // Servidor SMTP de cPanel
$smtp_port = 465; // Puerto SMTP (normalmente 587 o 465)
$smtp_username = "formularioweb@setteny.cl"; // Tu dirección de correo en cPanel
$smtp_password = "CGY,p=UxgqRB*v"; // Tu contraseña de correo en cPanel
$smtp_from = "formularioweb@setteny.cl"; // Dirección desde la que se envía
$smtp_from_name = "Formulario Web Setteny Consulting"; // Nombre del remitente
$smtp_to = "contacto@settenyconsulting.cl"; // Dirección a la que se envía el correo

// Crear el cuerpo del mensaje
$email_body = "Has recibido un nuevo mensaje desde el formulario de contacto.\n\n";
$email_body .= "Detalles:\n";
$email_body .= "Nombre: " . $nombre . "\n";
$email_body .= "Empresa: " . $empresa . "\n";
$email_body .= "Correo: " . $correo . "\n";
$email_body .= "Teléfono: " . $telefono . "\n";
$email_body .= "Mensaje:\n" . $mensaje . "\n";

// Intentar enviar el correo usando la función mail() básica
// Nota: Para una implementación más robusta, considera usar PHPMailer o SwiftMailer
$subject = "Nuevo mensaje de contacto desde el sitio web";
$headers = "From: " . $smtp_from . "\r\n";
$headers .= "Reply-To: " . $correo . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

try {
    if (mail($smtp_to, $subject, $email_body, $headers)) {
        echo json_encode(["success" => true, "message" => "Mensaje enviado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al enviar el mensaje"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
