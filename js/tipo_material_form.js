// Validaciones para formularios de tipos de material
document.addEventListener('DOMContentLoaded', function() {
    
    // Obtener el formulario
    const form = document.getElementById('tipoMaterialForm');
    const nombreTipoInput = document.getElementById('nombre_tipo');
    
    if (!form || !nombreTipoInput) {
        return; // Si no encuentra los elementos, salir
    }
    
    // Función para mostrar mensajes de error
    function mostrarError(elemento, mensaje) {
        // Remover mensaje de error anterior
        const errorAnterior = elemento.parentNode.querySelector('.error-message');
        if (errorAnterior) {
            errorAnterior.remove();
        }
        
        // Crear nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.9em';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = mensaje;
        
        // Insertar después del input
        elemento.parentNode.appendChild(errorDiv);
        
        // Cambiar estilo del input
        elemento.style.borderColor = 'red';
    }
    
    // Función para limpiar errores
    function limpiarError(elemento) {
        const error = elemento.parentNode.querySelector('.error-message');
        if (error) {
            error.remove();
        }
        elemento.style.borderColor = '';
    }
    
    // Función para validar el nombre del tipo
    function validarNombreTipo(valor) {
        const errores = [];
        
        // Verificar que no esté vacío
        if (!valor || valor.trim() === '') {
            errores.push('El nombre del tipo de material es obligatorio.');
            return errores;
        }
        
        const nombreLimpio = valor.trim();
        
        // Verificar longitud
        if (nombreLimpio.length > 100) {
            errores.push('El nombre no puede exceder 100 caracteres.');
        }
        
        // Verificar que no tenga solo números
        if (/^\d+$/.test(nombreLimpio)) {
            errores.push('El nombre no puede ser solo números.');
        }
        
        // Verificar caracteres especiales excesivos
        if (/[<>\"'&]/.test(nombreLimpio)) {
            errores.push('El nombre contiene caracteres no permitidos.');
        }
        
        // Verificar longitud mínima
        if (nombreLimpio.length < 2) {
            errores.push('El nombre debe tener al menos 2 caracteres.');
        }
        
        return errores;
    }
    
    // Validación en tiempo real mientras escribe
    nombreTipoInput.addEventListener('input', function() {
        const valor = this.value;
        const errores = validarNombreTipo(valor);
        
        if (errores.length > 0) {
            mostrarError(this, errores[0]);
        } else {
            limpiarError(this);
        }
        
        // Mostrar contador de caracteres
        actualizarContador();
    });
    
    // Función para mostrar contador de caracteres
    function actualizarContador() {
        let contador = nombreTipoInput.parentNode.querySelector('.contador-caracteres');
        
        if (!contador) {
            contador = document.createElement('div');
            contador.className = 'contador-caracteres';
            contador.style.fontSize = '0.8em';
            contador.style.color = '#666';
            contador.style.marginTop = '3px';
            nombreTipoInput.parentNode.appendChild(contador);
        }
        
        const longitud = nombreTipoInput.value.length;
        contador.textContent = `${longitud}/100 caracteres`;
        
        if (longitud > 90) {
            contador.style.color = 'red';
        } else if (longitud > 75) {
            contador.style.color = 'orange';
        } else {
            contador.style.color = '#666';
        }
    }
    
    // Validación al perder el foco (blur)
    nombreTipoInput.addEventListener('blur', function() {
        const valor = this.value;
        const errores = validarNombreTipo(valor);
        
        if (errores.length > 0) {
            mostrarError(this, errores[0]);
        } else {
            limpiarError(this);
        }
    });
    
    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        let hayErrores = false;
        
        // Validar nombre del tipo
        const nombreTipo = nombreTipoInput.value;
        const erroresNombre = validarNombreTipo(nombreTipo);
        
        if (erroresNombre.length > 0) {
            e.preventDefault();
            mostrarError(nombreTipoInput, erroresNombre[0]);
            hayErrores = true;
        }
        
        // Si hay errores, enfocar el primer campo con error
        if (hayErrores) {
            nombreTipoInput.focus();
            
            // Mostrar alerta general
            setTimeout(function() {
                alert('Por favor, corrija los errores en el formulario antes de continuar.');
            }, 100);
            
            return false;
        }
        
        // Limpiar errores antes de enviar
        limpiarError(nombreTipoInput);
        
        // Mostrar mensaje de procesamiento
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';
            
            // Rehabilitar botón después de 5 segundos por seguridad
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.dataset.originalText || 'Guardar';
            }, 5000);
        }
        
        return true;
    });
    
    // Guardar texto original del botón
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.dataset.originalText = submitBtn.textContent;
    }
    
    // Limpiar formulario cuando se presiona reset
    const resetBtn = form.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            setTimeout(function() {
                limpiarError(nombreTipoInput);
                const contador = nombreTipoInput.parentNode.querySelector('.contador-caracteres');
                if (contador) {
                    contador.remove();
                }
            }, 50);
        });
    }
    
    // Inicializar contador si hay contenido previo
    if (nombreTipoInput.value.length > 0) {
        actualizarContador();
    }
    
    // Función para capitalizar primera letra (opcional)
    function capitalizarPrimeraLetra(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
    
    // Opción para auto-capitalizar (comentado por defecto)
    /*
    nombreTipoInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.value = capitalizarPrimeraLetra(this.value.trim());
        }
    });
    */
});

// Función global para validar antes de enviar (backup)
function validarFormularioTipoMaterial() {
    const nombreTipo = document.getElementById('nombre_tipo');
    
    if (!nombreTipo) {
        return false;
    }
    
    const valor = nombreTipo.value.trim();
    
    if (!valor) {
        alert('El nombre del tipo de material es obligatorio.');
        nombreTipo.focus();
        return false;
    }
    
    if (valor.length > 100) {
        alert('El nombre del tipo de material no puede exceder 100 caracteres.');
        nombreTipo.focus();
        return false;
    }
    
    if (valor.length < 2) {
        alert('El nombre debe tener al menos 2 caracteres.');
        nombreTipo.focus();
        return false;
    }
    
    return true;
}
