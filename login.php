<?php
$errores = []; //array para guardar errores, si es que los hay
$mensaje_exito = ""; //mensaje por si la contraseña es valida

//ARCHIVO LOOOOOOOG

$log_file = 'password_int.log';




//verificacion de la contraseña con las siguientes reglas:

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    //obtener contraseña
    
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
        //[\W]= engloba caracteres especiales, menos el _, así que se le agrega el _
        // por lo que quedaria: [\W]_
        $errores[] = "debe contener al menos un caracter especial";
    }

    //codigo log
    //si el array $errores[] esta vacio, la contraseña es valida
    if (empty($errores)) {
        $mensaje_exito= "CONTRASEÑA VALIDA";
    }



//si el array $errores[] esta vacio, la contraseña es invalida
    if (empty($errores)) {
        $mensaje_exito= "CONTRASEÑA VALIDA";
        
        // NO SE GUARDA NADA AQUÍ
    

    } else {
        // LOG DE ERRORES 
        //fecha de la hora en el momento en que se hizo lo de la contraseña invalida
        $fecha = date('Y-m-d H:i:s'); //(year-month-day)
        //comando para que de la fecha en tiempo real
        
        //                implode() para convertir el array de errores en un solo string
        $errores_string = implode(", ", $errores); 
        
        // Creamos el mensaje
        //para que se guarde en el archivo .log
        $mensaje_log = "[$fecha] - CONTRASEÑA INVÁLIDA. Errores: [$errores_string]" . PHP_EOL; 
        //. PHP_EOL  (END OF LINES)
        // Guardamos en el archivo
        file_put_contents($log_file, $mensaje_log, FILE_APPEND | LOCK_EX); 
        //FILE_APPEND: aÑADIR AL FINAL
        //LOCK_EX: BLOQUEO ESCLUSIVO PARA NO ECRIBIR AL MISMO TIEMPPO
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>LOGIN</title>
</head>
<body>
<h1>Validador de contraseña </h1>  

<?php
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