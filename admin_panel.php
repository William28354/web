<?php
// admin_panel.php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php'); // Redirigir a la página de login si no es admin
    exit();
}

$message = '';
$users = [];
$pdo = connect_db();

// Obtener todos los usuarios para mostrar en la tabla
try {
    $stmt = $pdo->query("SELECT id, username, user_type FROM users ORDER BY user_type DESC, username ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al obtener usuarios: " . $e->getMessage());
    $message = '<div class="message error">Error al cargar la lista de usuarios.</div>';
}

// Mensaje flash desde otras páginas (como delete_user.php)
if (isset($_SESSION['admin_message'])) {
    $message = $_SESSION['admin_message'];
    unset($_SESSION['admin_message']); // Eliminar el mensaje después de mostrarlo
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="panel-container">
    <h1>Bienvenido, Administrador <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <?php echo $message; // Mostrar mensajes de éxito o error ?>

    <div class="panel-actions">
        <a href="create_user.php" class="button-link">Agregar Nuevo Usuario</a>
        <a href="snake_game.php" class="button-link">Jugar a la Serpiente</a> <!-- Nuevo enlace para el juego -->
        <a href="logout.php" class="button-link">Cerrar Sesión</a>
    </div>

    <h2>Gestión de Usuarios</h2>
    <?php if (count($users) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre de Usuario</th>
                <th>Tipo de Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                <td class="table-actions">
                    <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="edit-button">Editar</a>
                    <a href="delete_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="delete-button" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No hay usuarios registrados aún.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
