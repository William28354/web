<?php
// snake_game.php
session_start();

// Verify if the user is logged in and is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit();
}

include 'includes/header.php'; // Include the HTML header
?>

<style>
    /* Specific styles for the Snake game */
    .game-container {
        background-color: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 500px; /* Adjust max-width for the game */
        margin: 30px auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    #gameCanvas {
        background-color: #a7d94b; /* Green background for the game area */
        border: 5px solid #333;
        border-radius: 8px;
        display: block;
        margin-bottom: 20px;
        touch-action: none; /* Disable default touch actions */
    }
    .game-controls {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap; /* Allow buttons to wrap on small screens */
    }
    .game-score {
        font-size: 1.5em;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 15px;
    }
    .game-message {
        font-size: 1.2em;
        font-weight: 600;
        color: #e53e3e;
        margin-bottom: 15px;
    }

    /* Styles for mobile touch controls */
    .mobile-controls {
        display: none; /* Hidden by default */
        margin-top: 20px;
        width: 100%;
        max-width: 300px;
    }

    .mobile-controls .dpad-container {
        display: grid;
        grid-template-areas:
            ". up ."
            "left . right"
            ". down .";
        gap: 10px;
        width: 100%;
    }

    .mobile-controls button {
        background-color: #4a90e2;
        color: white;
        padding: 15px;
        border: none;
        border-radius: 50%; /* Make them round */
        font-size: 1.5em;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 60px; /* Fixed width for round buttons */
        height: 60px; /* Fixed height for round buttons */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .mobile-controls button:hover {
        background-color: #357bd8;
    }

    /* Grid area positioning for D-pad */
    .mobile-controls .up { grid-area: up; }
    .mobile-controls .left { grid-area: left; }
    .mobile-controls .right { grid-area: right; }
    .mobile-controls .down { grid-area: down; }


    @media (max-width: 768px) {
        .game-container {
            padding: 20px;
            margin: 15px;
        }
        #gameCanvas {
            width: 280px; /* Smaller canvas for mobile */
            height: 280px;
        }
        .game-controls .button-link {
            font-size: 0.9em;
            padding: 8px 15px;
        }
        .mobile-controls {
            display: grid; /* Show on smaller screens */
            grid-template-columns: 1fr;
            justify-items: center;
        }
    }
</style>

<div class="container">
    <h2>Juego de la Serpiente</h2>
    <div class="game-container">
        <div class="game-score">Puntuación: <span id="score">0</span></div>
        <div class="game-message" id="gameMessage"></div>
        <canvas id="gameCanvas" width="400" height="400"></canvas>

        <div class="game-controls">
            <button class="button-link" onclick="startGame()">Iniciar Juego</button>
            <button class="button-link" onclick="stopGame()">Detener Juego</button>
            <a href="admin_panel.php" class="button-link">Volver al Panel</a>
            <a href="logout.php" class="button-link">Cerrar Sesión</a>
        </div>

        <!-- Mobile D-pad controls for touchscreens -->
        <div class="mobile-controls">
            <div class="dpad-container">
                <button class="up" ontouchstart="changeDirection('up')">&#9650;</button> <!-- Up arrow -->
                <button class="left" ontouchstart="changeDirection('left')">&#9664;</button> <!-- Left arrow -->
                <button class="right" ontouchstart="changeDirection('right')">&#9654;</button> <!-- Right arrow -->
                <button class="down" ontouchstart="changeDirection('down')">&#9660;</button> <!-- Down arrow -->
            </div>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    const scoreDisplay = document.getElementById('score');
    const gameMessage = document.getElementById('gameMessage');

    const gridSize = 20; // Size of each segment of the snake and food
    let snake = [{ x: 10, y: 10 }]; // Initial snake position
    let food = {};
    let direction = 'right';
    let score = 0;
    let gameInterval;
    let gameSpeed = 150; // Milliseconds per frame
    let gameStarted = false;
    let gameOver = false;

    // Function to set canvas size responsively
    function setCanvasSize() {
        const containerWidth = canvas.parentElement.clientWidth;
        // Keep a square aspect ratio
        let size = Math.min(containerWidth - 60, 400); // 400px max, with some padding
        if (window.innerWidth < 768) {
            size = Math.min(window.innerWidth - 60, 300); // Smaller for mobile
        }
        canvas.width = size;
        canvas.height = size;
        render(); // Re-render content after resizing
    }

    window.addEventListener('load', setCanvasSize);
    window.addEventListener('resize', setCanvasSize); // Adjust canvas on resize

    function draw() {
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw food
        ctx.fillStyle = 'red';
        ctx.strokeStyle = 'darkred';
        ctx.fillRect(food.x * gridSize, food.y * gridSize, gridSize, gridSize);
        ctx.strokeRect(food.x * gridSize, food.y * gridSize, gridSize, gridSize);

        // Draw snake
        ctx.fillStyle = 'darkgreen';
        ctx.strokeStyle = 'black';
        snake.forEach((segment, index) => {
            ctx.fillRect(segment.x * gridSize, segment.y * gridSize, gridSize, gridSize);
            ctx.strokeRect(segment.x * gridSize, segment.y * gridSize, gridSize, gridSize);
        });
    }

    function generateFood() {
        food = {
            x: Math.floor(Math.random() * (canvas.width / gridSize)),
            y: Math.floor(Math.random() * (canvas.height / gridSize))
        };

        // Ensure food does not spawn on the snake
        for (let i = 0; i < snake.length; i++) {
            if (food.x === snake[i].x && food.y === snake[i].y) {
                generateFood(); // Regenerate if on snake
                return;
            }
        }
    }

    function moveSnake() {
        if (!gameStarted || gameOver) return;

        const head = { x: snake[0].x, y: snake[0].y };

        // Update head position based on direction
        switch (direction) {
            case 'up':
                head.y--;
                break;
            case 'down':
                head.y++;
                break;
            case 'left':
                head.x--;
                break;
            case 'right':
                head.x++;
                break;
        }

        // Check for collisions
        if (checkCollision(head)) {
            endGame();
            return;
        }

        // Add new head to the beginning of the snake
        snake.unshift(head);

        // Check if food is eaten
        if (head.x === food.x && head.y === food.y) {
            score++;
            scoreDisplay.textContent = score;
            generateFood();
            // Increase speed slightly
            if (gameSpeed > 50) { // Limit min speed
                gameSpeed -= 5;
                clearInterval(gameInterval);
                gameInterval = setInterval(gameLoop, gameSpeed);
            }
        } else {
            // Remove tail if no food eaten
            snake.pop();
        }

        draw();
    }

    function checkCollision(head) {
        // Wall collision
        if (head.x < 0 || head.x >= (canvas.width / gridSize) ||
            head.y < 0 || head.y >= (canvas.height / gridSize)) {
            return true;
        }

        // Self-collision
        for (let i = 1; i < snake.length; i++) {
            if (head.x === snake[i].x && head.y === snake[i].y) {
                return true;
            }
        }
        return false;
    }

    function endGame() {
        gameOver = true;
        gameStarted = false;
        clearInterval(gameInterval);
        gameMessage.textContent = `¡Fin del Juego! Tu puntuación fue: ${score}`;
        gameMessage.style.color = '#e53e3e';
    }

    function startGame() {
        if (gameStarted) return; // Prevent multiple starts

        snake = [{ x: 10, y: 10 }]; // Reset snake
        direction = 'right'; // Reset direction
        score = 0;
        scoreDisplay.textContent = score;
        gameSpeed = 150; // Reset speed
        gameOver = false;
        gameStarted = true;
        gameMessage.textContent = ''; // Clear game message

        generateFood();
        draw();
        clearInterval(gameInterval); // Clear any existing interval
        gameInterval = setInterval(gameLoop, gameSpeed);
    }

    function stopGame() {
        gameStarted = false;
        clearInterval(gameInterval);
        gameMessage.textContent = 'Juego Detenido.';
        gameMessage.style.color = '#4a90e2';
    }

    function gameLoop() {
        moveSnake();
    }

    function changeDirection(newDirection) {
        // Prevent immediate reverse direction
        if (newDirection === 'left' && direction !== 'right') {
            direction = 'left';
        } else if (newDirection === 'up' && direction !== 'down') {
            direction = 'up';
        } else if (newDirection === 'right' && direction !== 'left') {
            direction = 'right';
        } else if (newDirection === 'down' && direction !== 'up') {
            direction = 'down';
        }
    }

    // Keyboard controls
    document.addEventListener('keydown', e => {
        if (!gameStarted && e.key !== 'Enter' && e.key !== ' ') return; // Only allow start key if game not started
        switch (e.key) {
            case 'ArrowUp':
            case 'w':
                changeDirection('up');
                break;
            case 'ArrowDown':
            case 's':
                changeDirection('down');
                break;
            case 'ArrowLeft':
            case 'a':
                changeDirection('left');
                break;
            case 'ArrowRight':
            case 'd':
                changeDirection('right');
                break;
            case 'Enter': // Start game with Enter
            case ' ': // Start game with Space
                if (!gameStarted) startGame();
                break;
        }
    });

    // Initial render and setup
    renderCalendar(); // Not needed here, typo from previous file. Removed.
    // Call setCanvasSize initially and then render game elements
    setCanvasSize();
    generateFood(); // Generate initial food
    draw(); // Draw initial state

    gameMessage.textContent = 'Presiona "Iniciar Juego" o Enter/Espacio para comenzar';
</script>

<?php include 'includes/footer.php'; // Include the HTML footer ?>
