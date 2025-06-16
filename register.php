<?php
// register.php
session_start();
require_once 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    // Obtener el user_type desde el formulario, por defecto 'normal' si no se envía o es inválido
    $user_type = in_array($_POST['user_type'] ?? '', ['normal', 'invitado']) ? $_POST['user_type'] : 'normal';

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
                ':user_type' => $user_type // Usar el tipo de usuario seleccionado
            ]);

            $message = '<div class="message success">Usuario registrado exitosamente. ¡Ahora puedes iniciar sesión!</div>';
            // Opcional: redirigir a la página de login después del registro
            // header('Location: index.php');
            // exit();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Código de error para entrada duplicada
                $message = '<div class="message error">El nombre de usuario ya existe. Por favor, elige otro.</div>';
            } else {
                error_log("Error al registrar usuario: " . $e->getMessage());
                $message = '<div class="message error">Error al registrar el usuario.</div>';
            }
        }
    }
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="form-container">
    <h2>Registrar Nuevo Usuario</h2>
    <?php echo $message; // Mostrar mensajes de éxito o error ?>
    <form action="register.php" method="POST">
        <input type="text" name="username" placeholder="Nombre de Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="user_type" required>
            <option value="normal">Usuario Normal</option>
            <option value="invitado">Invitado</option>
        </select>
        <button type="submit">Registrar</button>
    </form>
    <div class="links">
        <p>¿Ya tienes una cuenta? <a href="index.php">Inicia Sesión</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
