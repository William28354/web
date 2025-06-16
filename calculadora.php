<?php
// calculadora.php
session_start();

// Verificar si el usuario está logueado y es un usuario normal
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'normal') {
    header('Location: index.php'); // Redirigir si no es un usuario normal
    exit();
}

include 'includes/header.php'; // Incluir el encabezado HTML
?>

<style>
    /* Estilos específicos para la calculadora */
    .calculator-container {
        background-color: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 350px;
        margin: 30px auto;
        text-align: center;
    }
    .calculator-display {
        background-color: #2d3748;
        color: white;
        font-size: 2.5em;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: right;
        overflow: hidden;
        white-space: nowrap;
    }
    .calculator-buttons {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }
    .calculator-buttons button {
        background-color: #e2e8f0;
        color: #4a5568;
        padding: 20px;
        font-size: 1.5em;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .calculator-buttons button:hover {
        background-color: #cbd5e0;
        transform: translateY(-1px);
    }
    .calculator-buttons button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .calculator-buttons .operator {
        background-color: #4a90e2;
        color: white;
    }
    .calculator-buttons .operator:hover {
        background-color: #357bd8;
    }
    .calculator-buttons .equals {
        background-color: #28a745;
        color: white;
        grid-column: span 2; /* Este estilo hace que el botón de igual ocupe dos columnas */
    }
    .calculator-buttons .equals:hover {
        background-color: #218838;
    }
    .calculator-buttons .clear {
        background-color: #e53e3e;
        color: white;
    }
    .calculator-buttons .clear:hover {
        background-color: #c53030;
    }
</style>

<div class="container">
    <h2>Calculadora</h2>
    <div class="calculator-container">
        <div class="calculator-display" id="display">0</div>
        <div class="calculator-buttons">
            <!-- Primera fila: AC, /, *, - -->
            <button class="clear" onclick="clearDisplay()">AC</button>
            <button class="operator" onclick="appendOperator('/')">/</button>
            <button class="operator" onclick="appendOperator('*')">*</button>
            <button class="operator" onclick="appendOperator('-')">-</button>

            <!-- Segunda fila: 7, 8, 9, + -->
            <button onclick="appendNumber('7')">7</button>
            <button onclick="appendNumber('8')">8</button>
            <button onclick="appendNumber('9')">9</button>
            <button class="operator" onclick="appendOperator('+')">+</button>

            <!-- Tercera fila: 4, 5, 6, (espacio para igualar la cuadrícula) -->
            <button onclick="appendNumber('4')">4</button>
            <button onclick="appendNumber('5')">5</button>
            <button onclick="appendNumber('6')">6</button>
            <!-- Botón invisible para mantener la alineación de la cuadrícula, ya que '=' ocupa dos espacios en la siguiente fila -->
            <button class="operator" style="visibility: hidden; cursor: default;"></button>

            <!-- Cuarta fila: 1, 2, 3, = -->
            <button onclick="appendNumber('1')">1</button>
            <button onclick="appendNumber('2')">2</button>
            <button onclick="appendNumber('3')">3</button>
            <button class="equals" onclick="calculateResult()">=</button>

            <!-- Quinta fila: 0, ., (espacios para igualar la cuadrícula) -->
            <button onclick="appendNumber('0')">0</button>
            <button onclick="appendDecimal('.')">.</button>
            <!-- Dos botones invisibles para rellenar la fila ya que '0' y '.' solo ocupan dos espacios -->
            <button class="operator" style="visibility: hidden; cursor: default;"></button>
            <button class="operator" style="visibility: hidden; cursor: default;"></button>
        </div>
    </div>
    <div class="links" style="margin-top: 30px;">
        <a href="user_panel.php">Volver al Panel de Usuario</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</div>

<script>
    let display = document.getElementById('display');
    let currentInput = '0';
    let operator = null;
    let firstOperand = null;
    let awaitingNextOperand = false;

    function updateDisplay() {
        display.textContent = currentInput;
    }

    function clearDisplay() {
        currentInput = '0';
        operator = null;
        firstOperand = null;
        awaitingNextOperand = false;
        updateDisplay();
    }

    function appendNumber(number) {
        if (awaitingNextOperand) {
            currentInput = number;
            awaitingNextOperand = false;
        } else {
            currentInput = currentInput === '0' ? number : currentInput + number;
        }
        updateDisplay();
    }

    function appendDecimal(dot) {
        if (awaitingNextOperand) {
            currentInput = '0.';
            awaitingNextOperand = false;
        } else if (!currentInput.includes(dot)) {
            currentInput += dot;
        }
        updateDisplay();
    }

    function appendOperator(nextOperator) {
        const inputValue = parseFloat(currentInput);

        if (firstOperand === null && !isNaN(inputValue)) {
            firstOperand = inputValue;
        } else if (operator && !awaitingNextOperand) {
            const result = performCalculation[operator](firstOperand, inputValue);
            currentInput = String(result);
            firstOperand = result;
        }

        awaitingNextOperand = true;
        operator = nextOperator;
        updateDisplay(); // Actualiza el display para reflejar el estado, aunque no se muestre el operador
    }

    const performCalculation = {
        '/': (first, second) => second !== 0 ? first / second : 'Error',
        '*': (first, second) => first * second,
        '+': (first, second) => first + second,
        '-': (first, second) => first - second,
    };

    function calculateResult() {
        const inputValue = parseFloat(currentInput);

        if (firstOperand === null || operator === null || awaitingNextOperand) {
            // No hay suficiente información para calcular o ya se calculó
            return;
        }

        const result = performCalculation[operator](firstOperand, inputValue);

        if (result === 'Error') {
            currentInput = 'Error';
        } else {
            currentInput = String(parseFloat(result.toFixed(7))); // Limitar decimales para evitar problemas de flotación
        }

        firstOperand = null;
        operator = null;
        awaitingNextOperand = true; // Para que el siguiente número reemplace el resultado
        updateDisplay();
    }

    clearDisplay(); // Inicializar el display
</script>

<?php include 'includes/footer.php'; // Incluir el pie de página HTML ?>
