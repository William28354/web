<?php
// calendario.php
session_start();

// Verificar si el usuario está logueado y es un invitado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'invitado') {
    header('Location: index.php'); // Redirigir si no es un invitado
    exit();
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<style>
    /* Estilos específicos para el calendario */
    .calendar-container {
        background-color: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 400px;
        margin: 30px auto;
        text-align: center;
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .calendar-header button {
        background-color: #4a90e2;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.2em;
        transition: background-color 0.2s ease;
    }
    .calendar-header button:hover {
        background-color: #357bd8;
    }
    .calendar-header h3 {
        color: #1a202c;
        margin: 0;
        font-size: 1.8em;
    }
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        margin-bottom: 10px;
        font-weight: 600;
        color: #4a5568;
    }
    .calendar-weekdays div {
        text-align: center;
        padding: 5px 0;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    .calendar-day {
        background-color: #e2e8f0;
        padding: 15px 5px;
        border-radius: 8px;
        text-align: center;
        font-size: 1.1em;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        aspect-ratio: 1 / 1; /* Para que los días sean cuadrados */
    }
    .calendar-day:hover:not(.empty):not(.today) {
        background-color: #cbd5e0;
        transform: translateY(-1px);
    }
    .calendar-day.empty {
        background-color: transparent;
        cursor: default;
        box-shadow: none;
    }
    .calendar-day.today {
        background-color: #4a90e2;
        color: white;
        font-weight: bold;
    }
    .calendar-day.today:hover {
        background-color: #357bd8;
    }

    @media (max-width: 480px) {
        .calendar-container {
            padding: 20px;
            max-width: 95%;
        }
        .calendar-header h3 {
            font-size: 1.5em;
        }
        .calendar-day {
            font-size: 0.9em;
            padding: 10px 3px;
        }
    }
</style>

<div class="container">
    <h2>Calendario</h2>
    <div class="calendar-container">
        <div class="calendar-header">
            <button onclick="prevMonth()">&#9664;</button>
            <h3 id="monthYear"></h3>
            <button onclick="nextMonth()">&#9654;</button>
        </div>
        <div class="calendar-weekdays">
            <div>Dom</div>
            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div>Sáb</div>
        </div>
        <div class="calendar-grid" id="calendarGrid">
            <!-- Los días se renderizarán aquí con JavaScript -->
        </div>
    </div>
    <div class="links" style="margin-top: 30px;">
        <a href="invitado_panel.php">Volver al Panel de Invitado</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>

<script>
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    let today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const monthYearDisplay = document.getElementById('monthYear');
    const calendarGrid = document.getElementById('calendarGrid');

    function renderCalendar() {
        calendarGrid.innerHTML = ''; // Limpiar días anteriores
        monthYearDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;

        // Obtener el primer día del mes actual
        let firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay(); // 0 = Domingo, 1 = Lunes, etc.
        let daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate(); // Último día del mes

        // Días vacíos al inicio (para alinear con el día de la semana)
        for (let i = 0; i < firstDayOfMonth; i++) {
            let emptyDiv = document.createElement('div');
            emptyDiv.classList.add('calendar-day', 'empty');
            calendarGrid.appendChild(emptyDiv);
        }

        // Días del mes
        for (let day = 1; day <= daysInMonth; day++) {
            let dayDiv = document.createElement('div');
            dayDiv.classList.add('calendar-day');
            dayDiv.textContent = day;

            // Marcar el día actual
            if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
                dayDiv.classList.add('today');
            }
            calendarGrid.appendChild(dayDiv);
        }
    }

    function nextMonth() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    }

    function prevMonth() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar();
    }

    // Renderizar el calendario al cargar la página
    renderCalendar();
</script>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
