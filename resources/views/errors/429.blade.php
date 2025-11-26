<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demasiados Intentos - Chankas Car</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a3a47 0%, #6db3c8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #fbc02d 0%, #f9a825 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            box-shadow: 0 5px 15px rgba(251, 192, 45, 0.3);
        }

        h1 {
            color: #1a3a47;
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .error-code {
            color: #6db3c8;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .highlight {
            background: linear-gradient(135deg, rgba(251, 192, 45, 0.1) 0%, rgba(109, 179, 200, 0.1) 100%);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #fbc02d;
        }

        .highlight strong {
            color: #1a3a47;
            display: block;
            margin-bottom: 5px;
        }

        .timer {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1a3a47;
            margin: 20px 0;
        }

        .tips {
            text-align: left;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 25px;
        }

        .tips h3 {
            color: #1a3a47;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .tips ul {
            list-style: none;
            padding-left: 0;
        }

        .tips li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #666;
            font-size: 0.9rem;
        }

        .tips li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #6db3c8;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #1a3a47 0%, #2d5566 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(26, 58, 71, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 58, 71, 0.3);
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 0.85rem;
            color: #999;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .timer {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            🛡️
        </div>
        
        <h1>Demasiados Intentos</h1>
        <div class="error-code">ERROR 429 - RATE LIMIT EXCEEDED</div>
        
        <p>Has excedido el número máximo de intentos permitidos.</p>
        
        <div class="highlight">
            <strong>🔒 Sistema de Seguridad Activado</strong>
            <p style="margin-bottom: 0;">Por tu seguridad y la del sistema, hemos bloqueado temporalmente tu acceso.</p>
        </div>

        <p><strong>Tiempo de espera aproximado:</strong></p>
        <div class="timer" id="timer">1:00</div>

        <div class="tips">
            <h3>💡 Consejos de Seguridad:</h3>
            <ul>
                <li>Verifica que estés usando las credenciales correctas</li>
                <li>Asegúrate de no tener CAPS LOCK activado</li>
                <li>Si olvidaste tu contraseña, usa "Recuperar contraseña"</li>
                <li>Contacta al administrador si el problema persiste</li>
            </ul>
        </div>

        <a href="{{ route('login') }}" class="btn" id="loginBtn" style="pointer-events: none; opacity: 0.5;">
            Volver al Login
        </a>

        <div class="footer">
            Chankas Car - Sistema de Gestión de Taller GNV<br>
            Sucre, Bolivia
        </div>
    </div>

    <script>
        // Temporizador de cuenta regresiva
        let seconds = 60;
        const timerElement = document.getElementById('timer');
        const loginBtn = document.getElementById('loginBtn');

        const countdown = setInterval(function() {
            seconds--;
            
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            timerElement.textContent = minutes + ':' + (secs < 10 ? '0' : '') + secs;

            if (seconds <= 0) {
                clearInterval(countdown);
                timerElement.textContent = '¡Ya puedes intentar de nuevo!';
                timerElement.style.color = '#28a745';
                loginBtn.style.pointerEvents = 'auto';
                loginBtn.style.opacity = '1';
            }
        }, 1000);
    </script>
</body>
</html>
