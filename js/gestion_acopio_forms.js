// Validación del formulario de gestión de acopio
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCentroAcopio');
    const nombreCentro = document.getElementById('nombre_centro');
    const direccion = document.getElementById('direccion');
    
    // Contadores de caracteres
    const nombreCentroCount = document.getElementById('nombre_centro_count');
    const direccionCount = document.getElementById('direccion_count');
    
    // Función para actualizar contador de caracteres
    function actualizarContador(input, contador) {
        contador.textContent = input.value.length;
    }
    
    // Inicializar contadores
    if (nombreCentro && nombreCentroCount) {
        actualizarContador(nombreCentro, nombreCentroCount);
        nombreCentro.addEventListener('input', function() {
            actualizarContador(this, nombreCentroCount);
            validarNombreCentro();
        });
    }
    
    if (direccion && direccionCount) {
        actualizarContador(direccion, direccionCount);
        direccion.addEventListener('input', function() {
            actualizarContador(this, direccionCount);
            validarDireccion();
        });
    }
    
    // Función para mostrar errores
    function mostrarError(campo, mensaje) {
        const errorDiv = document.getElementById('error_' + campo);
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.style.display = mensaje ? 'block' : 'none';
        }
    }
    
    // Función para limpiar errores
    function limpiarError(campo) {
        mostrarError(campo, '');
    }
    
    // Validar nombre del centro
    function validarNombreCentro() {
        const valor = nombreCentro.value.trim();
        
        if (valor === '') {
            mostrarError('nombre_centro', 'El nombre del centro es requerido.');
            return false;
        }
        
        if (valor.length < 3) {
            mostrarError('nombre_centro', 'El nombre debe tener al menos 3 caracteres.');
            return false;
        }
        
        if (valor.length > 100) {
            mostrarError('nombre_centro', 'El nombre no puede exceder 100 caracteres.');
            return false;
        }
        
        // Validar que no contenga solo números o caracteres especiales
        if (!/^[a-zA-ZÀ-ÿ\u00f1\u00d1\s0-9\-_\.]+$/.test(valor)) {
            mostrarError('nombre_centro', 'El nombre contiene caracteres no válidos.');
            return false;
        }
        
        limpiarError('nombre_centro');
        return true;
    }
    
    // Validar dirección
    function validarDireccion() {
        const valor = direccion.value.trim();
        
        if (valor.length > 150) {
            mostrarError('direccion', 'La dirección no puede exceder 150 caracteres.');
            return false;
        }
        
        // Si no está vacía, validar formato básico
        if (valor !== '' && valor.length < 10) {
            mostrarError('direccion', 'La dirección debe tener al menos 10 caracteres si se proporciona.');
            return false;
        }
        
        limpiarError('direccion');
        return true;
    }
    
    // Validación en tiempo real
    if (nombreCentro) {
        nombreCentro.addEventListener('blur', validarNombreCentro);
    }
    
    if (direccion) {
        direccion.addEventListener('blur', validarDireccion);
    }
    
    // Validar formulario antes del envío
    if (form) {
        form.addEventListener('submit', function(e) {
            let esValido = true;
            
            // Validar todos los campos
            if (nombreCentro && !validarNombreCentro()) {
                esValido = false;
            }
            
            if (direccion && !validarDireccion()) {
                esValido = false;
            }
            
            if (!esValido) {
                e.preventDefault();
                
                // Enfocar el primer campo con error
                const primerError = form.querySelector('.error-message[style*="block"]');
                if (primerError) {
                    const campoError = primerError.id.replace('error_', '');
                    const campo = document.getElementById(campoError);
                    if (campo) {
                        campo.focus();
                    }
                }
                
                // Mostrar mensaje general
                alert('Por favor, corrige los errores en el formulario antes de continuar.');
            }
        });
    }
    
    // Función para limpiar formulario
    function limpiarFormulario() {
        if (form) {
            form.reset();
            
            // Limpiar errores
            const errores = form.querySelectorAll('.error-message');
            errores.forEach(error => {
                error.textContent = '';
                error.style.display = 'none';
            });
            
            // Resetear contadores
            if (nombreCentroCount) nombreCentroCount.textContent = '0';
            if (direccionCount) direccionCount.textContent = '0';
        }
    }
    
    // Agregar botón para limpiar formulario si existe
    const btnLimpiar = document.getElementById('btnLimpiar');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Estás seguro de que quieres limpiar el formulario?')) {
                limpiarFormulario();
            }
        });
    }
});
