@extends('layouts.admin')

@section('tituloPage', 'soy el lider     ')

@section('contenido')
    
   <html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Votante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #495057;
        }

        .main-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            border: none;
            padding: 24px;
        }

        .card-header h4 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card-header small {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
            background-color: white;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-label i {
            color: #6b7280;
            margin-right: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
        }

        .info-card {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: none;
            border-radius: 10px;
            color: white;
            margin-top: 20px;
        }

        .info-card .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 16px 20px;
        }

        .info-card .card-body {
            padding: 20px;
        }

        .info-card h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #1e40af;
            border-color: #1e40af;
        }

        .form-check-label {
            color: white;
            font-weight: 500;
        }

        .is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 20%, 40%, 60%, 80% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
        }

        .invalid-feedback {
            display: block;
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 4px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .page-title {
            color: #1e293b;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }

        .submit-btn {
            min-width: 160px;
        }

        .back-btn {
            min-width: 120px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                margin: 10px auto;
                padding: 0 15px;
            }

            .card-header {
                padding: 20px;
            }

            .page-title {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h1 class="page-title">
            <i class="fas fa-user-plus me-2"></i>
            Registrar Nuevo Votante
        </h1>

        <div class="card">
            <div class="card-header">
                <h4>
                    <i class="fas fa-user-plus me-2"></i> Formulario de Registro
                </h4>
                <small>
                    Complete todos los campos obligatorios para registrar un nuevo votante
                </small>
            </div>

            <div class="card-body p-4">
                <form id="formVotante">
                    <!-- Datos básicos del votante -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user"></i> Nombre Completo *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       name="nombre" 
                                       placeholder="Ingrese el nombre completo"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cedula" class="form-label">
                                    <i class="fas fa-id-card"></i> Cédula *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="cedula" 
                                       name="cedula" 
                                       placeholder="Número de cédula"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone"></i> Teléfono
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono"
                                       placeholder="Número de teléfono">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Correo Electrónico
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email"
                                       placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Dirección
                        </label>
                        <textarea class="form-control" 
                                  id="direccion" 
                                  name="direccion" 
                                  placeholder="Ingrese la dirección completa"
                                  rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Opciones de votación -->
                    <div class="card info-card">
                        <div class="card-header">
                            <h6>
                                <i class="fas fa-vote-yea me-2"></i> Opciones de Votación
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="vincular_alcalde" 
                                       name="vincular_alcalde" 
                                       value="1">
                                <label class="form-check-label" for="vincular_alcalde">
                                    Vincular con candidato a alcalde
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary back-btn" onclick="history.back()">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </button>
                        <button type="submit" class="btn btn-primary submit-btn">
                            <i class="fas fa-save me-2"></i> Registrar Votante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formVotante');
            const cedulaInput = document.getElementById('cedula');
            const nombreInput = document.getElementById('nombre');
            const telefonoInput = document.getElementById('telefono');
            const emailInput = document.getElementById('email');

            // Validar cédula mientras escribe
            cedulaInput.addEventListener('input', function() {
                // Solo números
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Validar longitud
                if (this.value.length > 0 && this.value.length < 6) {
                    this.classList.add('is-invalid');
                    this.nextElementSibling.textContent = 'La cédula debe tener al menos 6 dígitos';
                } else {
                    this.classList.remove('is-invalid');
                    this.nextElementSibling.textContent = '';
                }
            });

            // Validar teléfono
            telefonoInput.addEventListener('input', function() {
                // Solo números, espacios y guiones
                this.value = this.value.replace(/[^0-9\s-]/g, '');
            });

            // Formatear nombre
            nombreInput.addEventListener('blur', function() {
                this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) {
                    return l.toUpperCase();
                });
            });

            // Validar email en tiempo real
            emailInput.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                    this.nextElementSibling.textContent = 'Por favor ingrese un email válido';
                } else {
                    this.classList.remove('is-invalid');
                    this.nextElementSibling.textContent = '';
                }
            });

            // Validación antes de enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let isValid = true;
                
                // Validar nombre
                if (nombreInput.value.trim().length < 3) {
                    nombreInput.classList.add('is-invalid');
                    nombreInput.nextElementSibling.textContent = 'El nombre debe tener al menos 3 caracteres';
                    isValid = false;
                } else {
                    nombreInput.classList.remove('is-invalid');
                    nombreInput.nextElementSibling.textContent = '';
                }

                // Validar cédula
                if (cedulaInput.value.length < 6) {
                    cedulaInput.classList.add('is-invalid');
                    cedulaInput.nextElementSibling.textContent = 'La cédula debe tener al menos 6 dígitos';
                    isValid = false;
                } else {
                    cedulaInput.classList.remove('is-invalid');
                    cedulaInput.nextElementSibling.textContent = '';
                }

                // Validar email si se proporciona
                if (emailInput.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailInput.value)) {
                        emailInput.classList.add('is-invalid');
                        emailInput.nextElementSibling.textContent = 'Por favor ingrese un email válido';
                        isValid = false;
                    }
                }

                if (isValid) {
                    // Simular envío exitoso
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Registrando...';
                    submitBtn.disabled = true;
                    
                    setTimeout(() => {
                        alert('¡Votante registrado exitosamente!');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        form.reset();
                    }, 2000);
                } else {
                    // Scroll al primer campo con error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        });
    </script>
</body>
</html>
@endsection
@section('scripts')
   