<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance";

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el valor de la identificación de la tarjeta desde la solicitud GET
$rfid_serial = $_GET['serial'];

// Consulta SQL para verificar si la tarjeta está en la base de datos
$sql = "SELECT e.name FROM employees e
        INNER JOIN employee_rfid r ON e.id = r.employee_id
        WHERE r.rfid_serial = '$rfid_serial'";

$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    // La tarjeta está asociada a un empleado
    $row = $result->fetch_assoc();
    echo "EXISTE_EMPLEADO:" . $row["name"];
} else {
    // La tarjeta no está en la base de datos
    echo "TARJETA_NO_REGISTRADA";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
