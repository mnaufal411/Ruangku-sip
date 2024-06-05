<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Ruang Rapat</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
</head>
<body>
    <header>
        <h1>Ruangku</h1>
        <h2>Sewa Ruang Meeting</h2>
    </header>
    
    <main>
        <div id="calendar"></div>
        
        <div class="login-form">
            <h3>Login</h3>
            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
                unset($_SESSION['error_message']);
            }
            ?>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="username">
                        <input type="text" id="username" name="username" placeholder="username" required>
                        <span class="icon">👤</span>
                    </label>
                </div>
                <div class="input-group">
                    <label for="password">
                        <input type="password" id="password" name="password" placeholder="password" required>
                        <span class="icon">🔒</span>
                        <span class="toggle-password" onclick="togglePassword()">👁️</span>
                    </label>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var passwordFieldType = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = passwordFieldType;
        }

        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: {
                    url: 'assets/js/fetch_events.php',
                    type: 'GET',
                    error: function() {
                        alert('There was an error while fetching events!');
                    }
                }
            });
        });
    </script>
</body>
</html>
