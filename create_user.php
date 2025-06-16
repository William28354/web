<?php
// create_user.php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user_type = $_POST['user_type'] ?? 'normal'; // Valor por defecto 'normal'

    if (empty($username) || empty($password)) {
        $message = '<div class="message error">Por favor, complete todos los campos.</div>';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $pdo = connect_db();

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, user_type) VALUES (:username, :password, :user_type)");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password,
                ':user_type' => $user_type
            ]);

            $message = '<div class="message success">Usuario "' . htmlspecialchars($username) . '" creado exitosamente.</div>';
            // Opcional: Redirigir al panel de administración después de la creación
            // header('Location: admin_panel.php');
            // exit();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Código de error para entrada duplicada
                $message = '<div class="message error">El nombre de usuario ya existe. Por favor, elige otro.</div>';
            } else {
                error_log("Error al crear usuario: " . $e->getMessage());
                $message = '<div class="message error">Error al crear el usuario.</div>';
            }
        }
    }
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="form-container">
    <h2>Crear Nuevo Usuario</h2>
    <?php echo $message; // Mostrar mensajes de éxito o error ?>
    <form action="create_user.php" method="POST">
        <input type="text" name="username" placeholder="Nombre de Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="user_type" required>
            <option value="normal">Usuario Normal</option>
            <option value="admin">Administrador</option>
            <option value="invitado">Invitado</option> <!-- Nueva opción -->
        </select>
        <button type="submit">Crear Usuario</button>
    </form>
    <div class="links">
        <a href="admin_panel.php">Volver al Panel de Administración</a>
    </div>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
