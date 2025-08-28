<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Iniciar Sesión</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1DB584;
            --primary-dark: #16A085;
            --background: #0a0e27;
            --surface: #151932;
            --surface-light: #1e2347;
            --text-primary: #ffffff;
            --text-secondary: #8b9dc3;
            --error: #ff4757;
            --success: #2ed573;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 20% 20%, var(--primary-color) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, var(--primary-dark) 0%, transparent 50%);
            opacity: 0.1;
            pointer-events: none;
        }
        .login-container {
            background: var(--surface);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            position: relative;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media (max-width: 480px) {
            body { padding: 0; align-items: stretch; }
            .login-container {
                border-radius: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 40px 24px;
                border: none;
                transition: padding 0.3s ease;
            }
        }
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            position: relative;
        }
        .logo-icon::before {
            content: '';
            position: absolute;
            inset: 2px;
            background: var(--surface);
            border-radius: 14px;
        }
        .logo-icon i {
            font-size: 28px;
            color: var(--primary-color);
            position: relative;
            z-index: 1;
        }
        .logo h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo p {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 4px 0 0;
        }
        @media (max-width: 480px) {
            .logo { margin-bottom: 48px; }
            .logo h1 { font-size: 28px; }
            .logo p { font-size: 16px; }
        }
        .form-group { margin-bottom: 24px; position: relative; }
        .form-label {
            display: block;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .input-wrapper { position: relative; }
        .form-control {
            width: 100%;
            padding: 16px 50px 16px 20px;
            background: var(--surface-light);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            color: var(--text-primary) !important;
            -webkit-text-fill-color: var(--text-primary) !important;
            caret-color: var(--primary-color);
        }
        @media (max-width: 480px) {
            .form-control {
                padding: 18px 50px 18px 20px;
                font-size: 16px;
                border-radius: 16px;
            }
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(29, 181, 132, 0.1);
            background: var(--surface-light);
            scroll-margin-top: 100px;
        }
        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }
        .form-control.is-invalid {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
        }
        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 18px;
            pointer-events: none;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px var(--surface-light) inset !important;
            -webkit-text-fill-color: var(--text-primary) !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: 8px;
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @media (max-width: 480px) {
            .btn-login {
                padding: 18px;
                font-size: 17px;
                border-radius: 16px;
                min-height: 58px;
                margin-top: 16px;
            }
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(29, 181, 132, 0.3);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover::before { left: 100%; }
        .loading-spinner {
            width: 20px; height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .forgot-password {
            display: block;
            text-align: center;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            margin-top: 24px;
            transition: color 0.3s ease;
        }
        .forgot-password:hover { color: var(--primary-color); }
        @media (max-width: 480px) {
            .forgot-password { font-size: 16px; margin-top: 32px; }
        }
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: none;
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        @media (max-width: 480px) {
            .alert { border-radius: 16px; padding: 18px; }
        }
        .alert-success {
            background: rgba(46, 213, 115, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        .alert-danger {
            background: rgba(255, 71, 87, 0.1);
            color: var(--error);
            border-left: 4px solid var(--error);
        }
        .invalid-feedback {
            color: var(--error);
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }
        @media (max-width: 480px) {
            .invalid-feedback { font-size: 14px; margin-top: 8px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (hover: none) and (pointer: coarse) {
            .btn-login:hover { transform: none; box-shadow: none; }
            .btn-login:active { transform: scale(0.98); background: var(--primary-dark); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Iniciar Sesión</h1>
            <p>Accede a tu cuenta</p>
        </div>
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        <!-- Login Form -->
        <form method="POST" action="{{ route('inicia-sesion') }}" id="loginForm" novalidate>
            @csrf
            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="ejemplo@correo.com"
                        autocomplete="email"
                        inputmode="email"
                        required>
                  
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••••"
                        autocomplete="current-password"
                        required>
                   
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Submit Button -->
            <button type="submit" class="btn-login" id="loginBtn">
                <div class="loading-spinner" id="spinner"></div>
                <span id="btnText">Iniciar Sesión</span>
            </button>
        </form>
        <!-- Forgot Password -->
        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');

            function showLoading() {
                spinner.style.display = 'block';
                btnText.textContent = 'Iniciando...';
                loginBtn.disabled = true;
            }
            function hideLoading() {
                spinner.style.display = 'none';
                btnText.textContent = 'Iniciar Sesión';
                loginBtn.disabled = false;
            }
            form.addEventListener('submit', function(e) {
                showLoading();
                setTimeout(() => {
                    if (document.querySelector('.is-invalid')) {
                        hideLoading();
                    }
                }, 100);
            });
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.parentNode.querySelector('.invalid-feedback');
                    if (feedback) { feedback.style.display = 'none'; }
                });
                input.addEventListener('focus', function() {
                    if (window.innerWidth <= 480) {
                        setTimeout(() => {
                            this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 300);
                    }
                });
            });
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 4000);
            function adjustForKeyboard() {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }
            window.addEventListener('resize', adjustForKeyboard);
            adjustForKeyboard();
            if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
                document.addEventListener('gesturestart', function(e) { e.preventDefault(); });
            }
        });
    </script>
</body>
</html>