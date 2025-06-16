<?php
// invitado_panel.php
session_start();

// Verificar si el usuario está logueado y es un invitado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'invitado') {
    header('Location: index.php'); // Redirigir a la página de login si no es invitado
    exit();
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="panel-container">
    <h1>Bienvenido, Invitado <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Esperamos que disfrutes tu estancia.</p>

    <div style="margin-top: 40px; text-align: center;">
        <h3>Accede a nuestro Calendario:</h3>
    </div>

    <div class="panel-actions">
        <a href="calendario.php" class="button-link">Ver Calendario</a>
        <a href="logout.php" class="button-link">Cerrar Sesión</a>
    </div>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
