<?php

/**
 * Función para encriptar contraseñas de usuarios
 * @param string $password - La contraseña en texto plano
 * @return string - La contraseña encriptada
 */
function encriptarPassword($password) {
    // Usar password_hash con BCRYPT (algoritmo recomendado)
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Función para verificar si una contraseña coincide con su hash
 * @param string $password - La contraseña en texto plano
 * @param string $hash - El hash almacenado en la base de datos
 * @return bool - True si coinciden, False si no
 */
function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

?>
