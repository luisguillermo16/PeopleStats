<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1DB584, #0FA968);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: #2C3E50;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            position: relative;
            margin-top: 5vh;
        }
        
        .profile-circle {
            width: 80px;
            height: 80px;
            background: #5A6C7D;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -50px auto 30px;
            position: relative;
            border: 4px solid #2C3E50;
            transition: all 0.3s ease;
        }
        
        .profile-circle i {
            font-size: 35px;
            color: #34495E;
            transition: all 0.3s ease;
        }
        
        .online-indicator {
            width: 20px;
            height: 20px;
            background: #1DB584;
            border-radius: 50%;
            position: absolute;
            bottom: 5px;
            right: 5px;
            border: 3px solid #2C3E50;
        }
        
        .nav-tabs {
            border: none;
            margin-bottom: 2rem;
        }
        
        .nav-tabs .nav-link {
            background: none;
            border: none;
            color: #7F8C8D;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link.active {
            background: none;
            color: white;
            border-bottom: 2px solid #1DB584;
        }
        
        .form-label {
            color: #BDC3C7;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            background: #34495E;
            border: 2px solid #34495E;
            border-radius: 8px;
            color: white;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: #34495E;
            border-color: #1DB584;
            box-shadow: 0 0 0 0.2rem rgba(29, 181, 132, 0.25);
            color: white;
        }
        
        .form-control.is-invalid {
            border-color: #E74C3C;
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
        }
        
        .form-control::placeholder {
            color: #7F8C8D;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #1DB584, #16A085);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-submit:hover:not(:disabled) {
            background: linear-gradient(135deg, #16A085, #138D75);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(29, 181, 132, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .form-check-input {
            background-color: #34495E;
            border-color: #34495E;
        }
        
        .form-check-input:checked {
            background-color: #1DB584;
            border-color: #1DB584;
        }
        
        .form-check-label {
            color: #BDC3C7;
            font-size: 0.9rem;
        }
        
        .forgot-password {
            color: #7F8C8D;
            text-decoration: none;
            font-size: 0.9rem;
            text-align: center;
            display: block;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .forgot-password:hover {
            color: #1DB584;
        }
        
        .tab-pane {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #E74C3C;
            border-left: 4px solid #E74C3C;
        }
        
        .alert-success {
            background-color: rgba(39, 174, 96, 0.1);
            color: #27AE60;
            border-left: 4px solid #27AE60;
        }
        
        .invalid-feedback {
            color: #E74C3C;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spinner-border {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <main class="container align-center p-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5">
                <div class="login-container">
                    <!-- Profile Circle -->
                    <div class="profile-circle" id="profileCircle">
                        <i class="fas fa-user" id="profileIcon"></i>
                        <div class="online-indicator"></div>
                    </div>
                    
                    <!-- Mensajes de éxito/error globales -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs justify-content-center" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                        </li>
                      
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Login Tab -->
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form method="POST" action="{{ route('inicia-sesion') }}" id="loginForm">
                                @csrf
                                
                                <!-- Email Field -->
                                <div class="mb-3">
                                    <label for="emailInput" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="emailInput" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           required 
                                           placeholder="Ingresa tu email">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <!-- Password Field -->
                                <div class="mb-3">
                                    <label for="passwordInput" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="passwordInput" 
                                           name="password" 
                                           required 
                                           placeholder="Ingresa tu contraseña">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-submit" id="loginButton">
                                    <div class="spinner-border loading-spinner" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <span class="button-text">Acceder</span>
                                </button>
                                
                            
                            </form>
                        </div>
                        
                        <!-- Sign Up Tab -->
                        <div class="tab-pane fade" id="signup" role="tabpanel">
                            <form method="POST" action="{{ route('validar-registro') }}" id="signupForm">
                                @csrf
                                
                                <!-- Name Field -->
                                <div class="mb-3">
                                    <label for="nameInput" class="form-label">Nombre</label>
                                    <input type="text" 
                                        class="form-control @error('name') is-invalid @enderror" 
                                        id="nameInput" 
                                        name="name" 
                                        value="{{ old('name') }}"
                                        required 
                                        autocomplete="off" 
                                        placeholder="Ingresa tu nombre">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <!-- Email Field -->
                                <div class="mb-3">
                                    <label for="emailRegInput" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="emailRegInput" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           required 
                                           autocomplete="off" 
                                           placeholder="Ingresa tu email">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="invalid-feedback" id="emailExistsError" style="display: none;">
                                        Este email ya está registrado.
                                    </div>
                                </div>
                                
                                <!-- Password Field -->
                                <div class="mb-3">
                                    <label for="passwordRegInput" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="passwordRegInput" 
                                           name="password" 
                                           required 
                                           placeholder="Ingresa tu contraseña">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <!-- Confirm Password Field -->
                                <div class="mb-3">
                                    <label for="passwordConfirmInput" class="form-label">Confirmar Password</label>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="passwordConfirmInput" 
                                           name="password_confirmation" 
                                           required 
                                           placeholder="Confirma tu contraseña">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="invalid-feedback" id="passwordMismatchError" style="display: none;">
                                        Las contraseñas no coinciden.
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-submit" id="signupButton">
                                    <div class="spinner-border loading-spinner" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <span class="button-text">Registrarse</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginTab = document.getElementById('login-tab');
            const signupTab = document.getElementById('signup-tab');
            const profileIcon = document.getElementById('profileIcon');
            const loginForm = document.getElementById('loginForm');
            const signupForm = document.getElementById('signupForm');
            const emailRegInput = document.getElementById('emailRegInput');
            const passwordRegInput = document.getElementById('passwordRegInput');
            const passwordConfirmInput = document.getElementById('passwordConfirmInput');
            
            // Cambiar icono cuando se cambia de pestaña
            loginTab.addEventListener('click', function() {
                profileIcon.className = 'fas fa-user';
            });
            
            signupTab.addEventListener('click', function() {
                profileIcon.className = 'fas fa-user-plus';
            });
            
            // Mostrar spinner en formularios
            function showLoading(form, button) {
                const spinner = button.querySelector('.loading-spinner');
                const text = button.querySelector('.button-text');
                
                spinner.style.display = 'inline-block';
                text.textContent = 'Procesando...';
                button.disabled = true;
            }
            
            function hideLoading(form, button, originalText) {
                const spinner = button.querySelector('.loading-spinner');
                const text = button.querySelector('.button-text');
                
                spinner.style.display = 'none';
                text.textContent = originalText;
                button.disabled = false;
            }
            
            // Manejar envío de formulario de login
            loginForm.addEventListener('submit', function(e) {
                const button = document.getElementById('loginButton');
                showLoading(loginForm, button);
                
                // Si hay errores, ocultar loading después de un momento
                setTimeout(() => {
                    if (document.querySelector('.is-invalid')) {
                        hideLoading(loginForm, button, 'Acceder');
                    }
                }, 100);
            });
            
            // Manejar envío de formulario de registro
            signupForm.addEventListener('submit', function(e) {
                const button = document.getElementById('signupButton');
                
                // Validar que las contraseñas coincidan
                if (passwordRegInput.value !== passwordConfirmInput.value) {
                    e.preventDefault();
                    document.getElementById('passwordMismatchError').style.display = 'block';
                    passwordConfirmInput.classList.add('is-invalid');
                    return;
                }
                
                showLoading(signupForm, button);
                
                // Si hay errores, ocultar loading después de un momento
                setTimeout(() => {
                    if (document.querySelector('.is-invalid')) {
                        hideLoading(signupForm, button, 'Registrarse');
                    }
                }, 100);
            });
            
            // Validación en tiempo real de confirmación de contraseña
            passwordConfirmInput.addEventListener('input', function() {
                const errorDiv = document.getElementById('passwordMismatchError');
                
                if (this.value !== passwordRegInput.value && this.value.length > 0) {
                    this.classList.add('is-invalid');
                    errorDiv.style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    errorDiv.style.display = 'none';
                }
            });
            
            // Limpiar errores cuando el usuario empiece a escribir
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                });
            });
            
            // Auto-dismiss alerts después de 5 segundos
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // Si hay errores, mostrar la pestaña correcta
            @if($errors->any())
                @if(old('_token') && request()->route()->getName() === 'validar-registro')
                    // Mostrar pestaña de registro si hay errores de registro
                    signupTab.click();
                @endif
            @endif
        });
    </script>
</body>
</html>