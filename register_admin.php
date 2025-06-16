<?php
// register_admin.php
session_start();
require_once 'config/database.php';

$message = '';
$pdo = connect_db();

// Verificar si ya existe un administrador
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS admin_count FROM users WHERE user_type = 'admin'");
    $stmt->execute();
    $result = $stmt->fetch();
    $admin_exists = ($result['admin_count'] > 0);

    if ($admin_exists) {
        $message = '<div class="message error">El usuario administrador ya ha sido creado.</div>';
        // Puedes redirigir a index.php después de un breve retraso o dejar el mensaje
        // header('Refresh: 3; URL=index.php'); // Redirige después de 3 segundos
    }
} catch (PDOException $e) {
    error_log("Error al verificar administrador existente: " . $e->getMessage());
    $message = '<div class="message error">Error interno del servidor.</div>';
}

// Lógica de registro de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$admin_exists) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $message = '<div class="message error">Por favor, complete todos los campos.</div>';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, user_type) VALUES (:username, :password, 'admin')");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password
            ]);

            $message = '<div class="message success">Administrador creado exitosamente. ¡Ahora puedes iniciar sesión!</div>';
            // Redirigir al index.php después del registro exitoso
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Código de error para entrada duplicada (UNIQUE constraint)
                $message = '<div class="message error">El nombre de usuario ya existe. Por favor, elige otro.</div>';
            } else {
                error_log("Error al registrar administrador: " . $e->getMessage());
                $message = '<div class="message error">Error al registrar el administrador.</div>';
            }
        }
    }
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="form-container">
    <h2>Registrar Administrador</h2>
    <?php echo $message; // Mostrar mensajes de éxito o error ?>
    <?php if (!$admin_exists): // Mostrar el formulario solo si no existe un admin ?>
    <form action="register_admin.php" method="POST">
        <input type="text" name="username" placeholder="Nombre de Usuario (Admin)" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar Administrador</button>
    </form>
    <?php else: ?>
    <div class="links">
        <a href="index.php">Ir a Iniciar Sesión</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
