<?php
// index.php
session_start();
require_once 'config/database.php';

$message = '';

// Conectar a la base de datos
$pdo = connect_db();

// Verificar si ya existe un administrador
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS admin_count FROM users WHERE user_type = 'admin'");
    $stmt->execute();
    $result = $stmt->fetch();
    $admin_exists = ($result['admin_count'] > 0);

    // Si no existe un administrador, redirigir a la página de registro de administrador
    if (!$admin_exists) {
        header('Location: register_admin.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error al verificar administrador: " . $e->getMessage());
    $message = '<div class="message error">Error interno del servidor. Por favor, intente más tarde.</div>';
}

// Lógica de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $message = '<div class="message error">Por favor, ingrese su nombre de usuario y contraseña.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, user_type FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Inicio de sesión exitoso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redirigir según el tipo de usuario
                if ($user['user_type'] == 'admin') {
                    header('Location: admin_panel.php');
                } elseif ($user['user_type'] == 'normal') { // Agregado para usuario normal
                    header('Location: user_panel.php');
                } elseif ($user['user_type'] == 'invitado') { // Agregado para invitado
                    header('Location: invitado_panel.php');
                }
                exit();
            } else {
                $message = '<div class="message error">Nombre de usuario o contraseña incorrectos.</div>';
            }
        } catch (PDOException $e) {
            error_log("Error al iniciar sesión: " . $e->getMessage());
            $message = '<div class="message error">Error al intentar iniciar sesión.</div>';
        }
    }
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="container">
    <h2>Iniciar Sesión</h2>
    <?php echo $message; // Mostrar mensajes de éxito o error ?>
    <form action="index.php" method="POST">
        <input type="text" name="username" placeholder="Nombre de Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>
    <div class="links">
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
