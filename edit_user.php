<?php
// edit_user.php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$message = '';
$user_id = $_GET['id'] ?? null;
$user = null;
$pdo = connect_db();

// Si no se proporciona un ID de usuario, redirigir
if (!$user_id) {
    header('Location: admin_panel.php');
    exit();
}

// Obtener datos del usuario a editar
try {
    $stmt = $pdo->prepare("SELECT id, username, user_type FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = '<div class="message error">Usuario no encontrado.</div>';
        // Opcional: Redirigir si el usuario no existe
        // header('Location: admin_panel.php');
        // exit();
    }
} catch (PDOException $e) {
    error_log("Error al obtener usuario para editar: " . $e->getMessage());
    $message = '<div class="message error">Error al cargar los datos del usuario.</div>';
}

// Lógica para actualizar el usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user) {
    $new_username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['password'] ?? '');
    $new_user_type = $_POST['user_type'] ?? 'normal';

    if (empty($new_username)) {
        $message = '<div class="message error">El nombre de usuario no puede estar vacío.</div>';
    } else {
        try {
            $update_sql = "UPDATE users SET username = :username, user_type = :user_type";
            $params = [
                ':username' => $new_username,
                ':user_type' => $new_user_type,
                ':id' => $user_id
            ];

            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql .= ", password = :password";
                $params[':password'] = $hashed_password;
            }

            $update_sql .= " WHERE id = :id";

            $stmt = $pdo->prepare($update_sql);
            $stmt->execute($params);

            $message = '<div class="message success">Usuario "' . htmlspecialchars($new_username) . '" actualizado exitosamente.</div>';
            // Actualizar el objeto $user para reflejar los cambios en el formulario si no se redirige
            $user['username'] = $new_username;
            $user['user_type'] = $new_user_type;
            // Opcional: Redirigir al panel de administración después de la actualización
            // header('Location: admin_panel.php');
            // exit();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Código de error para entrada duplicada
                $message = '<div class="message error">El nombre de usuario ya existe. Por favor, elige otro.</div>';
            } else {
                error_log("Error al actualizar usuario: " . $e->getMessage());
                $message = '<div class="message error">Error al actualizar el usuario.</div>';
            }
        }
    }
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="form-container">
    <h2>Editar Usuario: <?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></h2>
    <?php echo $message; // Mostrar mensajes de éxito o error ?>

    <?php if ($user): ?>
    <form action="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" method="POST">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
        <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">

        <label for="user_type">Tipo de Usuario:</label>
        <select id="user_type" name="user_type" required>
            <option value="normal" <?php echo ($user['user_type'] == 'normal') ? 'selected' : ''; ?>>Usuario Normal</option>
            <option value="admin" <?php echo ($user['user_type'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
            <option value="invitado" <?php echo ($user['user_type'] == 'invitado') ? 'selected' : ''; ?>>Invitado</option> <!-- Nueva opción -->
        </select>
        <button type="submit">Guardar Cambios</button>
    </form>
    <?php endif; ?>

    <div class="links">
        <a href="admin_panel.php">Volver al Panel de Administración</a>
    </div>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
