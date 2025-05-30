<?php
session_start();

// Initialize game state
if (!isset($_SESSION['game_initialized']) || isset($_GET['reset'])) {
    $_SESSION['target_number'] = rand(1, 100);
    $_SESSION['attempts'] = 0;
    $_SESSION['max_attempts'] = 7;
    $_SESSION['hints'] = [];
    $_SESSION['last_guess'] = null;
    $_SESSION['game_over'] = false;
    $_SESSION['tricky_mode'] = isset($_GET['mode']) ? (int)$_GET['mode'] : rand(1, 4); // Allow mode selection
    $_SESSION['game_initialized'] = true;
    $_SESSION['message'] = "I'm thinking of a number between 1 and 100. Can you guess it?";
    $_SESSION['difficulty_level'] = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'normal';
    
    // Set max attempts based on difficulty
    if ($_SESSION['difficulty_level'] == 'easy') {
        $_SESSION['max_attempts'] = 10;
    } else if ($_SESSION['difficulty_level'] == 'hard') {
        $_SESSION['max_attempts'] = 5;
    }
    
    // Initialize score tracking
    if (!isset($_SESSION['games_played'])) {
        $_SESSION['games_played'] = 0;
        $_SESSION['games_won'] = 0;
        $_SESSION['best_score'] = PHP_INT_MAX;
    }
    $_SESSION['games_played']++;
    
    // Initialize time tracking
    $_SESSION['start_time'] = time();
}

// Process guess
if (isset($_POST['guess']) && !$_SESSION['game_over']) {
    $guess = (int)$_POST['guess'];
    
    // Validate input
    if ($guess < 1 || $guess > 100) {
        $_SESSION['message'] = "Please enter a number between 1 and 100.";
    } else {
        $_SESSION['last_guess'] = $guess;
        $_SESSION['attempts']++;
        
        // Apply the tricky mode behavior
        $target = $_SESSION['target_number'];
        $original_target = $target; // Store original target for comparison
        $difference = abs($target - $guess);
        
        switch ($_SESSION['tricky_mode']) {
            case 1: // Moving target - number slightly changes after each guess
                if ($difference > 20) {
                    $shift = rand(-3, 3);
                    $_SESSION['target_number'] += $shift;
                    // Keep target in bounds
                    $_SESSION['target_number'] = max(1, min(100, $_SESSION['target_number']));
                    if ($shift != 0) {
                        // Add a subtle hint about the moving target
                        $_SESSION['message_extra'] = "Did you feel that? Something just shifted...";
                    }
                }
                break;
                
            case 2: // Misleading feedback - sometimes gives opposite feedback
                $_SESSION['is_misleading'] = (rand(1, 5) == 1);
                if ($_SESSION['is_misleading']) {
                    // This will make the hint misleading
                    $difference = -$difference;
                    // Add a subtle visual cue for misleading hints
                    $_SESSION['hint_class'] = 'misleading';
                } else {
                    $_SESSION['hint_class'] = '';
                }
                break;
                
            case 3: // Hidden patterns - target is actually a pattern
                // In this mode, the target isn't fixed but follows a pattern
                $_SESSION['target_number'] = (($_SESSION['attempts'] * 7) % 100) + 1;
                $target = $_SESSION['target_number']; // Update target
                $difference = abs($target - $guess);
                
                // Provide subtle pattern hints after 3 attempts
                if ($_SESSION['attempts'] >= 3) {
                    $_SESSION['message_extra'] = "There seems to be a pattern here...";
                }
                break;
                
            case 4: // Zone defense - different zones give different feedback
                // The feedback accuracy depends on which zone the guess is in
                if ($guess < 25) {
                    $difference = abs($target - $guess) * 1.5;
                    $_SESSION['zone'] = 'cold';
                } else if ($guess > 75) {
                    $difference = abs($target - $guess) * 0.5;
                    $_SESSION['zone'] = 'hot';
                } else {
                    $_SESSION['zone'] = 'neutral';
                }
                break;
        }
        
        // Generate feedback
        if ($guess == $target) {
            $_SESSION['game_over'] = true;
            $time_taken = time() - $_SESSION['start_time'];
            $_SESSION['games_won']++;
            
            // Update best score
            if ($_SESSION['attempts'] < $_SESSION['best_score']) {
                $_SESSION['best_score'] = $_SESSION['attempts'];
            }
            
            $_SESSION['message'] = "Congratulations! You've guessed the number {$target} in {$_SESSION['attempts']} attempts and {$time_taken} seconds!";
            $_SESSION['hint_class'] = 'success';
        } else {
            // Create a hint
            if ($guess < $target) {
                $hint = "Your guess is too low";
            } else {
                $hint = "Your guess is too high";
            }
            
            // Add approximate distance hint
            if ($difference <= 5) {
                $hint .= " (very close!)";
                $distance_class = 'very-close';
            } else if ($difference <= 15) {
                $hint .= " (getting closer)";
                $distance_class = 'closer';
            } else if ($difference <= 30) {
                $hint .= " (still quite far)";
                $distance_class = 'far';
            } else {
                $hint .= " (way off)";
                $distance_class = 'way-off';
            }
            
            // Add zone information for mode 4
            if ($_SESSION['tricky_mode'] == 4 && isset($_SESSION['zone'])) {
                $hint .= " [Zone: {$_SESSION['zone']}]";
            }
            
            // Add misleading indicator for mode 2
            if ($_SESSION['tricky_mode'] == 2 && isset($_SESSION['is_misleading']) && $_SESSION['is_misleading']) {
                // We don't explicitly tell the player the hint is misleading,
                // but we will style it differently in the UI
            }
            
            array_push($_SESSION['hints'], [
                'text' => $hint,
                'guess' => $guess,
                'class' => $distance_class . (isset($_SESSION['hint_class']) ? ' ' . $_SESSION['hint_class'] : '')
            ]);
            
            // Check for game over
            if ($_SESSION['attempts'] >= $_SESSION['max_attempts']) {
                $_SESSION['game_over'] = true;
                $_SESSION['message'] = "Game over! You ran out of attempts. The number was {$target}.";
                $_SESSION['hint_class'] = 'game-over';
            } else {
                $remaining = $_SESSION['max_attempts'] - $_SESSION['attempts'];
                $_SESSION['message'] = $hint . ". {$remaining} attempts remaining.";
                
                // Add any extra message hints
                if (isset($_SESSION['message_extra'])) {
                    $_SESSION['message'] .= "<br><small>{$_SESSION['message_extra']}</small>";
                    unset($_SESSION['message_extra']);
                }
            }
        }
    }
}

// Difficulty description based on tricky mode
$difficulty_desc = [
    1 => "This mode features a moving target that shifts slightly when you're far off.",
    2 => "Beware of occasional misleading feedback in this challenge!",
    3 => "The target follows a hidden pattern rather than being fixed.",
    4 => "Different zones of numbers provide different accuracy of feedback."
];

// Determine hint colors based on remaining attempts
$urgency_color = function($attempts_left, $max_attempts) {
    $percentage = $attempts_left / $max_attempts;
    if ($percentage > 0.5) return '#d4edda'; // Green (safe)
    if ($percentage > 0.2) return '#fff3cd'; // Yellow (caution)
    return '#f8d7da'; // Red (danger)
};

// Get win rate
$win_rate = $_SESSION['games_played'] > 0 ? 
    round(($_SESSION['games_won'] / $_SESSION['games_played']) * 100) : 0;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tricky Number Guessing Game</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
            transition: background-color 0.3s ease;
        }
        
        body.dark-mode {
            background-color: #2c3e50;
            color: #ecf0f1;
        }
        
        h1, h2, h3 {
            color: var(--secondary-color);
            text-align: center;
        }
        
        .dark-mode h1, .dark-mode h2, .dark-mode h3 {
            color: var(--light-color);
        }
        
        .game-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .dark-mode .game-container {
            background-color: var(--dark-color);
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1);
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            transition: background-color 0.3s ease;
        }
        
        .game-over .message {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            animation: pulse 2s infinite;
        }
        
        .success .message {
            background-color: #cce5ff;
            border-color: #b8daff;
            color: #004085;
            animation: success-pulse 2s infinite;
        }
        
        form {
            display: flex;
            margin-bottom: 20px;
        }
        
        input[type="number"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .dark-mode input[type="number"] {
            background-color: #34495e;
            color: white;
            border-color: #2c3e50;
        }
        
        input[type="number"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        button, .button {
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        
        button:hover, .button:hover {
            background-color: #2980b9;
        }
        
        .reset {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        
        .reset:hover {
            background-color: #5a6268;
        }
        
        .hints {
            list-style-type: none;
            padding: 0;
        }
        
        .hints li {
            padding: 12px;
            margin-bottom: 8px;
            background-color: #e9ecef;
            border-radius: 5px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dark-mode .hints li {
            background-color: #4a6380;
        }
        
        .hints li .guess-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .hints li.very-close {
            border-left: 5px solid var(--success-color);
        }
        
        .hints li.closer {
            border-left: 5px solid var(--warning-color);
        }
        
        .hints li.far {
            border-left: 5px solid var(--danger-color);
        }
        
        .hints li.way-off {
            border-left: 5px solid #6c757d;
            opacity: 0.8;
        }
        
        .hints li.misleading {
            border-style: dashed;
            animation: blink 2s infinite;
        }
        
        .difficulty {
            font-style: italic;
            margin-bottom: 15px;
            color: #6c757d;
            text-align: center;
            padding: 10px;
            background-color: rgba(0,0,0,0.05);
            border-radius: 5px;
        }
        
        .dark-mode .difficulty {
            color: #d1d8e0;
            background-color: rgba(255,255,255,0.05);
        }
        
        .attempts {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .progress-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 10px;
            background-color: var(--primary-color);
            width: 0;
            transition: width 0.5s;
        }
        
        .options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }
        
        .options a {
            padding: 8px 15px;
            background-color: #e9ecef;
            color: #333;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .dark-mode .options a {
            background-color: #4a6380;
            color: white;
        }
        
        .options a:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .options a.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .stats {
            background-color: rgba(0,0,0,0.05);
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        
        .dark-mode .stats {
            background-color: rgba(255,255,255,0.05);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .stat-card {
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dark-mode .stat-card {
            background-color: #34495e;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .dark-mode .stat-number {
            color: #3498db;
        }
        
        .toggle-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .mode-toggle {
            background-color: var(--dark-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
        }
        
        .dark-mode .mode-toggle {
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .mode-toggle:hover {
            transform: rotate(30deg);
        }
        
        #range-display {
            width: 100%;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .dark-mode #range-display {
            background-color: #4a6380;
        }
        
        .range-marker {
            position: absolute;
            top: 0;
            height: 100%;
            background-color: rgba(52, 152, 219, 0.3);
            transition: all 0.3s;
        }
        
        #guess-marker {
            position: absolute;
            width: 2px;
            height: 40px;
            background-color: red;
            top: 0;
            transition: left 0.3s;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        @keyframes success-pulse {
            0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }
        
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .new-game-options {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }
        
        .new-game-options.show {
            max-height: 500px;
        }
        
        .difficulty-selector {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        
        .difficulty-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0 5px;
        }
        
        .dark-mode .difficulty-option {
            border-color: #4a6380;
        }
        
        .difficulty-option:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .difficulty-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(52, 152, 219, 0.2);
        }
        
        .mode-selector {
            margin: 20px 0;
        }
        
        .mode-description {
            margin-top: 10px;
            font-style: italic;
            color: #6c757d;
        }
        
        .dark-mode .mode-description {
            color: #d1d8e0;
        }
        
        .number-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 5px;
            margin: 20px 0;
        }
        
        .number-cell {
            text-align: center;
            padding: 8px 0;
            background-color: #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .dark-mode .number-cell {
            background-color: #4a6380;
        }
        
        .number-cell:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        .number-cell.used {
            background-color: #6c757d;
            color: white;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .number-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="toggle-container">
        <button class="mode-toggle" id="mode-toggle">🌙</button>
    </div>

    <h1>Tricky Number Guessing Game</h1>
    
    <div class="game-container <?php echo $_SESSION['game_over'] ? ($_SESSION['last_guess'] == $_SESSION['target_number'] ? 'success' : 'game-over') : ''; ?>">
        <div class="difficulty">
            Mode: <?php echo $difficulty_desc[$_SESSION['tricky_mode']]; ?> | 
            Difficulty: <?php echo ucfirst($_SESSION['difficulty_level']); ?>
        </div>
        
        <div class="attempts">
            Attempts: <?php echo $_SESSION['attempts']; ?>/<?php echo $_SESSION['max_attempts']; ?>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar" style="width: <?php echo ($_SESSION['attempts'] / $_SESSION['max_attempts']) * 100; ?>%"></div>
        </div>
        
        <div class="message" style="background-color: <?php echo $urgency_color($_SESSION['max_attempts'] - $_SESSION['attempts'], $_SESSION['max_attempts']); ?>">
            <?php echo $_SESSION['message']; ?>
        </div>

        <?php if (!$_SESSION['game_over']): ?>
            <div id="range-display">
                <div class="range-marker" style="left: 0%; width: 100%;"></div>
                <?php if (isset($_SESSION['last_guess'])): ?>
                    <div id="guess-marker" style="left: <?php echo $_SESSION['last_guess']; ?>%;"></div>
                <?php endif; ?>
            </div>
            
            <div class="number-grid">
                <?php
                // Create a grid of numbers for quick selection
                $used_numbers = array_map(function($hint) { return $hint['guess']; }, $_SESSION['hints'] ?? []);
                for ($i = 1; $i <= 100; $i++) {
                    $used_class = in_array($i, $used_numbers) ? 'used' : '';
                    echo "<div class='number-cell $used_class' data-number='$i'>$i</div>";
                }
                ?>
            </div>
            
            <form method="post" action="">
                <input type="number" name="guess" id="guess-input" min="1" max="100" required placeholder="Enter your guess (1-100)" autofocus>
                <button type="submit">Guess</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($_SESSION['hints'])): ?>
            <h3>Previous hints:</h3>
            <ul class="hints">
                <?php foreach(array_reverse($_SESSION['hints']) as $index => $hint): ?>
                    <li class="<?php echo $hint['class']; ?>">
                        <span>
                            <span class="guess-number"><?php echo $hint['guess']; ?></span>
                            <?php echo $hint['text']; ?>
                        </span>
                        <span>Attempt #<?php echo $_SESSION['attempts'] - $index; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="stats">
            <h3>Your Stats</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div>Games Played</div>
                    <div class="stat-number"><?php echo $_SESSION['games_played']; ?></div>
                </div>
                <div class="stat-card">
                    <div>Win Rate</div>
                    <div class="stat-number"><?php echo $win_rate; ?>%</div>
                </div>
                <div class="stat-card">
                    <div>Best Score</div>
                    <div class="stat-number"><?php echo $_SESSION['best_score'] == PHP_INT_MAX ? '-' : $_SESSION['best_score']; ?></div>
                </div>
            </div>
        </div>

        <button id="new-game-button" class="reset">New Game</button>
        
        <div class="new-game-options" id="new-game-options">
            <div class="difficulty-selector">
                <div class="difficulty-option" data-difficulty="easy">Easy<br>(10 tries)</div>
                <div class="difficulty-option selected" data-difficulty="normal">Normal<br>(7 tries)</div>
                <div class="difficulty-option" data-difficulty="hard">Hard<br>(5 tries)</div>
            </div>
            
            <div class="mode-selector">
                <label for="mode-select">Choose a tricky mode:</label>
                <select id="mode-select" class="form-control">
                    <option value="0">Random</option>
                    <option value="1">Moving Target</option>
                    <option value="2">Misleading Feedback</option>
                    <option value="3">Hidden Pattern</option>
                    <option value="4">Zone Defense</option>
                </select>
                <div class="mode-description" id="mode-description">
                    Select a mode to see its description
                </div>
            </div>
            
            <a href="?reset=1" id="start-game" class="reset" style="background-color: var(--success-color);">Start Game</a>
        </div>
    </div>

    <script>
        // Dark mode toggle
        const modeToggle = document.getElementById('mode-toggle');
        const body = document.body;
        
        // Check for saved user preference
        if (localStorage.getItem('darkMode') === 'true') {
            body.classList.add('dark-mode');
            modeToggle.textContent = '☀️';
        }
        
        modeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDarkMode = body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
            modeToggle.textContent = isDarkMode ? '☀️' : '🌙';
        });
        
        // Number grid functionality
        const numberCells = document.querySelectorAll('.number-cell');
        const guessInput = document.getElementById('guess-input');
        
        numberCells.forEach(cell => {
            cell.addEventListener('click', () => {
                if (!cell.classList.contains('used')) {
                    guessInput.value = cell.dataset.number;
                    // Auto-submit the form
                    setTimeout(() => {
                        document.querySelector('form').submit();
                    }, 100);
                }
            });
        });
        
        // New game options
        const newGameBtn = document.getElementById('new-game-button');
        const newGameOptions = document.getElementById('new-game-options');
        const difficultyOptions = document.querySelectorAll('.difficulty-option');
        const modeSelect = document.getElementById('mode-select');
        const modeDescription = document.getElementById('mode-description');
        const startGameLink = document.getElementById('start-game');
        
        // Mode descriptions
        const modeDescriptions = {
            0: "Random mode - surprise yourself!",
            1: "The target number shifts slightly when your guess is far off.",
            2: "Sometimes you'll receive misleading feedback. Can you tell when?",
            3: "The target isn't fixed but follows a hidden pattern.",
            4: "Different ranges of numbers provide feedback with varying accuracy."
        };
        
        newGameBtn.addEventListener('click', () => {
            newGameOptions.classList.toggle('show');
        });
        
        difficultyOptions.forEach(option => {
            option.addEventListener('click', () => {
                difficultyOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                updateStartGameLink();
            });
        });
        
        modeSelect.addEventListener('change', () => {
            modeDescription.textContent = modeDescriptions[modeSelect.value];
            updateStartGameLink();
        });
        
        function updateStartGameLink() {
            const selectedDifficulty = document.querySelector('.difficulty-option.selected').dataset.difficulty;
            const selectedMode = modeSelect.value;
            
            let url = '?reset=1&difficulty=' + selectedDifficulty;
            if (selectedMode != 0) {
                url += '&mode=' + selectedMode;
            }
            startGameLink.href = url;
        }
        
        // Initialize mode description and start game link
        modeDescription.textContent = modeDescriptions[modeSelect.value];
        updateStartGameLink();
        
        // Range display based on hints
        function updateRangeDisplay() {
            const rangeMarker = document.querySelector('.range-marker');
            const hints = <?php echo json_encode($_SESSION['hints'] ?? []); ?>;
            
            if (hints.length === 0) return;
            
            let min = 1;
            let max = 100;
            
            hints.forEach(hint => {
                const text = hint.text;
                const guess = hint.guess;
                
                // This is a simplified logic and might need adjustment for misleading hints
                if (text.includes('too low')) {
                    min = Math.max(min, guess + 1);
                } else if (text.includes('too high')) {
                    max = Math.min(max, guess - 1);
                }
            });
            
            // Calculate percentages for CSS positioning
            const leftPercent = (min - 1);
            const widthPercent = (max - min + 1);
            
            rangeMarker.style.left = leftPercent + '%';
            rangeMarker.style.width = widthPercent + '%';
        }
        
        // Call once on page load
        updateRangeDisplay();
        
        // Confetti effect for winner
        <?php if ($_SESSION['game_over'] && isset($_SESSION['last_guess']) && $_SESSION['last_guess'] == $_SESSION['target_number']): ?>
        // Simple confetti effect
        function createConfetti() {
            const confettiCount = 150;
            const container = document.querySelector('.game-container');
            
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.style.position = 'absolute';
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                confetti.style.top = '-20px';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                confetti.style.zIndex = '1000';
                confetti.style.opacity = Math.random() * 0.7 + 0.3;
                
                container.appendChild(confetti);
                
                // Animate falling
                const duration = Math.random() * 3 + 2;
                const animation = confetti.animate([
                    { transform: `translateY(0) rotate(0deg)`, opacity: 1 },
                    { transform: `translateY(${container.offsetHeight}px) rotate(${Math.random() * 720}deg)`, opacity: 0 }
                ], {
                    duration: duration * 1000,
                    easing: 'ease-in',
                    fill: 'forwards'
                });
                
                animation.onfinish = () => confetti.remove();
            }
        }
        
        // Call confetti when page loads on win
        window.addEventListener('load', createConfetti);
        <?php endif; ?>
        
        // Add sound effects
        let audioContext;
        
        function initAudio() {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        function playTone(frequency, duration) {
            if (!audioContext) initAudio();
            
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.type = 'sine';
            oscillator.frequency.value = frequency;
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            gainNode.gain.setValueAtTime(0, audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.5, audioContext.currentTime + 0.01);
            gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + duration);
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + duration);
        }
        
        // Play different tones when clicking numbers
        numberCells.forEach(cell => {
            cell.addEventListener('click', () => {
                if (!cell.classList.contains('used')) {
                    // Play different tones based on the number value
                    const number = parseInt(cell.dataset.number);
                    const baseFrequency = 220;
                    const frequency = baseFrequency + (number * 5);
                    playTone(frequency, 0.2);
                }
            });
        });
        
        // Initialize audio context on first user interaction
        document.addEventListener('click', function initializeAudio() {
            initAudio();
            document.removeEventListener('click', initializeAudio);
        }, { once: true });
    </script>
</body>
</html>