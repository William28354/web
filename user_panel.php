<?php
// user_panel.php
session_start();

// Verificar si el usuario está logueado y es un usuario normal
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'normal') {
    header('Location: index.php'); // Redirigir a la página de login si no es normal
    exit();
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<div class="panel-container">
    <h1>Bienvenido: <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>¡Gracias por ser parte de nuestra secta!</p>

    <div style="margin-top: 40px; text-align: center;">
        <img src="img/Windows.gif" alt="Imagen de bienvenida" class="user-panel-image">
    </div>

    <div class="panel-actions">
        <a href="calculadora.php" class="button-link">Acceder a la Calculadora</a>
        <a href="logout.php" class="button-link">Cerrar Sesión</a>
    </div>
</div>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
