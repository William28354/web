<?php
// delete_user.php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$user_id = $_GET['id'] ?? null;
$pdo = connect_db();

if ($user_id) {
    try {
        // Asegurarse de que el administrador no pueda eliminarse a sí mismo
        if ($user_id == $_SESSION['user_id']) {
            // No redirigir directamente, tal vez mostrar un mensaje en admin_panel
            $_SESSION['admin_message'] = '<div class="message error">No puedes eliminar tu propia cuenta de administrador.</div>';
            header('Location: admin_panel.php');
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);

        $_SESSION['admin_message'] = '<div class="message success">Usuario eliminado exitosamente.</div>';
    } catch (PDOException $e) {
        error_log("Error al eliminar usuario: " . $e->getMessage());
        $_SESSION['admin_message'] = '<div class="message error">Error al eliminar el usuario.</div>';
    }
} else {
    $_SESSION['admin_message'] = '<div class="message error">ID de usuario no proporcionado.</div>';
}

// Redirigir siempre de vuelta al panel de administración
header('Location: admin_panel.php');
exit();
?>
