// Validaciones para formularios de materiales
document.addEventListener('DOMContentLoaded', function() {
    
    // Obtener elementos del formulario
    const form = document.getElementById('materialForm');
    const nombreMaterialInput = document.getElementById('nombre_material');
    const descripcionInput = document.getElementById('descripcion');
    
    if (!form || !nombreMaterialInput) {
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
    
    // Función para validar el nombre del material
    function validarNombreMaterial(valor) {
        const errores = [];
        
        // Verificar que no esté vacío
        if (!valor || valor.trim() === '') {
            errores.push('El nombre del material es obligatorio.');
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
    
    // Función para validar descripción
    function validarDescripcion(valor) {
        const errores = [];
        
        if (valor && valor.length > 1000) {
            errores.push('La descripción no puede exceder 1000 caracteres.');
        }
        
        // Verificar caracteres especiales peligrosos
        if (valor && /[<>\"'&]/.test(valor)) {
            errores.push('La descripción contiene caracteres no permitidos.');
        }
        
        return errores;
    }
    
    // Validación en tiempo real - nombre del material
    nombreMaterialInput.addEventListener('input', function() {
        const valor = this.value;
        const errores = validarNombreMaterial(valor);
        
        if (errores.length > 0) {
            mostrarError(this, errores[0]);
        } else {
            limpiarError(this);
        }
        
        // Mostrar contador de caracteres
        actualizarContadorNombre();
    });
    
    // Validación en tiempo real - descripción
    if (descripcionInput) {
        descripcionInput.addEventListener('input', function() {
            const valor = this.value;
            const errores = validarDescripcion(valor);
            
            if (errores.length > 0) {
                mostrarError(this, errores[0]);
            } else {
                limpiarError(this);
            }
            
            // Mostrar contador de caracteres
            actualizarContadorDescripcion();
        });
    }
    
    // Función para mostrar contador de caracteres - nombre
    function actualizarContadorNombre() {
        let contador = nombreMaterialInput.parentNode.querySelector('.contador-caracteres');
        
        if (!contador) {
            contador = document.createElement('div');
            contador.className = 'contador-caracteres';
            contador.style.fontSize = '0.8em';
            contador.style.color = '#666';
            contador.style.marginTop = '3px';
            nombreMaterialInput.parentNode.appendChild(contador);
        }
        
        const longitud = nombreMaterialInput.value.length;
        contador.textContent = `${longitud}/100 caracteres`;
        
        if (longitud > 90) {
            contador.style.color = 'red';
        } else if (longitud > 75) {
            contador.style.color = 'orange';
        } else {
            contador.style.color = '#666';
        }
    }
    
    // Función para mostrar contador de caracteres - descripción
    function actualizarContadorDescripcion() {
        if (!descripcionInput) return;
        
        let contador = descripcionInput.parentNode.querySelector('.contador-caracteres-desc');
        
        if (!contador) {
            contador = document.createElement('div');
            contador.className = 'contador-caracteres-desc';
            contador.style.fontSize = '0.8em';
            contador.style.color = '#666';
            contador.style.marginTop = '3px';
            descripcionInput.parentNode.appendChild(contador);
        }
        
        const longitud = descripcionInput.value.length;
        contador.textContent = `${longitud}/1000 caracteres`;
        
        if (longitud > 900) {
            contador.style.color = 'red';
        } else if (longitud > 750) {
            contador.style.color = 'orange';
        } else {
            contador.style.color = '#666';
        }
    }
    
    // Validación al perder el foco
    nombreMaterialInput.addEventListener('blur', function() {
        const valor = this.value;
        const errores = validarNombreMaterial(valor);
        
        if (errores.length > 0) {
            mostrarError(this, errores[0]);
        } else {
            limpiarError(this);
        }
    });
    
    if (descripcionInput) {
        descripcionInput.addEventListener('blur', function() {
            const valor = this.value;
            const errores = validarDescripcion(valor);
            
            if (errores.length > 0) {
                mostrarError(this, errores[0]);
            } else {
                limpiarError(this);
            }
        });
    }
    
    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        let hayErrores = false;
        
        // Validar nombre del material
        const nombreMaterial = nombreMaterialInput.value;
        const erroresNombre = validarNombreMaterial(nombreMaterial);
        
        if (erroresNombre.length > 0) {
            e.preventDefault();
            mostrarError(nombreMaterialInput, erroresNombre[0]);
            hayErrores = true;
        }
        
        // Validar descripción si existe
        if (descripcionInput) {
            const descripcion = descripcionInput.value;
            const erroresDescripcion = validarDescripcion(descripcion);
            
            if (erroresDescripcion.length > 0) {
                e.preventDefault();
                mostrarError(descripcionInput, erroresDescripcion[0]);
                hayErrores = true;
            }
        }
        
        // Si hay errores, enfocar el primer campo con error
        if (hayErrores) {
            nombreMaterialInput.focus();
            
            // Mostrar alerta general
            setTimeout(function() {
                alert('Por favor, corrija los errores en el formulario antes de continuar.');
            }, 100);
            
            return false;
        }
        
        // Limpiar errores antes de enviar
        limpiarError(nombreMaterialInput);
        if (descripcionInput) {
            limpiarError(descripcionInput);
        }
        
        // Mostrar mensaje de procesamiento
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';
            
            // Rehabilitar botón después de 5 segundos por seguridad
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.dataset.originalText || 'Crear Material';
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
                limpiarError(nombreMaterialInput);
                if (descripcionInput) {
                    limpiarError(descripcionInput);
                }
                
                // Remover contadores
                const contadorNombre = nombreMaterialInput.parentNode.querySelector('.contador-caracteres');
                if (contadorNombre) {
                    contadorNombre.remove();
                }
                
                if (descripcionInput) {
                    const contadorDesc = descripcionInput.parentNode.querySelector('.contador-caracteres-desc');
                    if (contadorDesc) {
                        contadorDesc.remove();
                    }
                }
            }, 50);
        });
    }
    
    // Inicializar contadores si hay contenido previo
    if (nombreMaterialInput.value.length > 0) {
        actualizarContadorNombre();
    }
    
    if (descripcionInput && descripcionInput.value.length > 0) {
        actualizarContadorDescripcion();
    }
});

// Función global para validar antes de enviar (backup)
function validarFormularioMaterial() {
    const nombreMaterial = document.getElementById('nombre_material');
    
    if (!nombreMaterial) {
        return false;
    }
    
    const valor = nombreMaterial.value.trim();
    
    if (!valor) {
        alert('El nombre del material es obligatorio.');
        nombreMaterial.focus();
        return false;
    }
    
    if (valor.length > 100) {
        alert('El nombre del material no puede exceder 100 caracteres.');
        nombreMaterial.focus();
        return false;
    }
    
    if (valor.length < 2) {
        alert('El nombre debe tener al menos 2 caracteres.');
        nombreMaterial.focus();
        return false;
    }
    
    return true;
}
