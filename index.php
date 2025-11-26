<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: /home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $validUsername = 'admin';
    $validPassword = '123456';

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: /home.php');
        exit;
    } else {
        $error = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap </title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            animation: zoomIn 0.8s ease;
            position: relative;
            z-index: 10;
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 0 0 10px rgba(0, 123, 255, 0.8);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 16px;
            transition: box-shadow 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            box-shadow: 0 0 10px #007bff;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #007bff;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.8);
        }
        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
            animation: shake 0.3s ease;
            text-shadow: 0 0 5px #ff6b6b;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%, 75% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
        }
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0f2027;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div id="loader">
        <div class="spinner"></div>
    </div>
    <div id="particles-js"></div>
    <div class="login-container" data-tilt>
        <h2>Giriş Yap</h2>
        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
        <form id="login-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Giriş Yap</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
    <script>
        window.onload = function() {
            document.getElementById('loader').style.display = 'none';
        };

        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#007bff' },
                shape: { type: 'circle' },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 150, color: '#007bff', opacity: 0.4, width: 1 },
                move: { enable: true, speed: 2, direction: 'none', random: true }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' } },
                modes: { repulse: { distance: 100 }, push: { particles_nb: 4 } }
            },
            retina_detect: true
        });

        VanillaTilt.init(document.querySelector('.login-container'), {
            max: 15,
            speed: 400,
            glare: true,
            'max-glare': 0.5
        });

        document.getElementById('login-form').addEventListener('submit', function(event) {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            if (username === '' || password === '') {
                event.preventDefault();
                alert('Lütfen tüm alanları doldurun!');
            }
        });
    </script>
</body>

</html>
