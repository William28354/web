<?php
// config/database.php

// Define las credenciales de la base de datos
define('DB_HOST', 'localhost'); // Tu host de MySQL, usualmente 'localhost'
define('DB_USER', 'root');     // Tu usuario de MySQL
define('DB_PASS', '');         // Tu contraseña de MySQL
define('DB_NAME', 'logginp');  // El nombre de tu base de datos

/**
 * Establece una conexión a la base de datos usando PDO.
 *
 * @return PDO La instancia de conexión PDO.
 */
function connect_db() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Devolver resultados como arrays asociativos
        PDO::ATTR_EMULATE_PREPARES   => false,                // Deshabilitar la emulación de prepared statements
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // En un entorno de producción, es mejor registrar el error y mostrar un mensaje genérico.
        // echo "Error de conexión a la base de datos: " . $e->getMessage();
        die("Error de conexión a la base de datos."); // Detener la ejecución si hay un error de conexión
    }
}
?>
