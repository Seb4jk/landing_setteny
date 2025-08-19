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

// Configuración de correo
$to = "contacto@setteny.cl"; // Reemplaza con tu dirección de correo
$subject = "Nuevo mensaje de contacto desde el sitio web";

// Crear el cuerpo del mensaje
$email_body = "Has recibido un nuevo mensaje desde el formulario de contacto.\n\n";
$email_body .= "Detalles:\n";
$email_body .= "Nombre: " . $nombre . "\n";
$email_body .= "Empresa: " . $empresa . "\n";
$email_body .= "Correo: " . $correo . "\n";
$email_body .= "Teléfono: " . $telefono . "\n";
$email_body .= "Mensaje:\n" . $mensaje . "\n";

// Cabeceras del correo
$headers = "From: " . $correo . "\r\n";
$headers .= "Reply-To: " . $correo . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Intentar enviar el correo
try {
    if (mail($to, $subject, $email_body, $headers)) {
        echo json_encode(["success" => true, "message" => "Mensaje enviado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al enviar el mensaje"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
