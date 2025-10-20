<?php
$errores = []; //array para guardar errores, si es que los hay
$mensaje_exito = ""; //mensaje por si la contraseña es valida

//verificacion del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST"){

    //obtener contraseña
    // ERROR 1 CORREGIDO: Cambiado "Password" a "password"
    $password = $_POST["password"]; 

    //reglas para la contraseña

    //minimo 8 caracteres
    if (strlen ($password) <8 ){
        $errores[] = "debe contener al menos 8 caracteres";
    }

    //al menos una letra mayuscula
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "debe contener al menos una letra mayúscula";
    }

    //al menos una letra minuscula
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = "debe contener al menos una letra minúscula";
    }

    //al menos un numero
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = "debe contener al menos un número";
    }

    //al menos un caracter especial
    if (!preg_match('/[\W_]/', $password)) {
        // ERROR 2 CORREGIDO: Mensaje de error correcto
        $errores[] = "debe contener al menos un caracter especial";
    }

    //si el array $errores[] esta vacio, la contraseña es valida
    if (empty($errores)) {
        $mensaje_exito= "CONTRASEÑA VALIDA";
    }

    // NOTA: Toda la lógica de 'echo' se movió al HTML de abajo
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
</head>
<body>
<h1>Validador de contraseña </h1>  

<?php
// ERROR 3 CORREGIDO: Lógica de mensajes simplificada y movida DENTRO del body
// Si el array $errores NO está vacío, la contraseña NO es válida:
if (!empty($errores)) {
    echo "<p><b>CONTRASEÑA INVALIDA:</b></p>";
    echo "<ul>"; // Inicia la lista de errores
    foreach ($errores as $error) {
        echo "<li>$error</li>"; // Muestra cada error
    }
    echo "</ul>"; // Cierra la lista
}
// Si no hay errores y $mensaje_exito NO está vacío...
else if (!empty($mensaje_exito)) {
    // ...AQUÍ SE IMPRIME EL MENSAJE DE "¡CONTRASEÑA VÁLIDA!"
    echo "<p><b>$mensaje_exito</b></p>";
}
?>

<form action="" method="POST">
    <label for="password">Introduce una contraseña:</label>
    
    <input type="password" id="password" name="password" required>
    
    <br><br> 
    
    <input type="submit" value="Validar">
</form>

</body>
</html>
