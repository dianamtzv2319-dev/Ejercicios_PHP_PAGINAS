<?php 
session_start();
//crea una sesion o reanuda la que ya esta 
//es parecida a memoria temporal
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adivina el número: Resultado</title>
</head>
<body>

<?php
// Se obtiene el número ingresado por el usuario en el formulario
$num_jugador = $_POST["form_num"]; //en donde se guarda el num registrado
//$_POST contiene los datos desde el formulario


// Se incrementa el número de intentos
if (isset($_SESSION["num_intentos"])) { 
    //comprueba si ya hay contador de intentos en la sesion
    $_SESSION["num_intentos"]++; //suma 1 al contador
} else {
    $_SESSION["num_intentos"] = 1; //si no se cumple la condicion se crea y empieza por el 1
}

// Se muestra el número de intentos
?>
<p>Número de intentos: <?php echo $_SESSION["num_intentos"]; ?></p>

<?php
//<P> lenguaje de HTML para crear parrafo


// Si no existe número aleatorio en sesión, se genera uno nuevo
if (!isset($_SESSION["num_aleatorio"])) {
    //comprueba si no existe un numero guardado
    
    $_SESSION["num_aleatorio"] = rand(1, 100); 
    //si no lo hay genera un num del 1 al 100
    $_SESSION["numero_minimo"] = 1; //num min 1
    $_SESSION["numero_maximo"] = 100; //num max 100
}

// Se comprueba si el número del jugador es correcto
if ($_SESSION["num_aleatorio"] == $num_jugador) {
    // Si se adivina el número, se destruye la sesión (nuevo juego)
    session_destroy();
    //borra los datos de la sesion para iniciar otra
    ?>
    <p>¡CORRECTO! </p>
    <p><a href="numerosal.php">NUEVA PARTIDA</a></p>
<?php 
} else { 
    // Se actualizan los valores mínimos y máximos
    if ($num_jugador > $_SESSION["num_aleatorio"]) {
        //compara el numero del jugador
        $_SESSION["numero_maximo"] = $num_jugador;
    } else {
        $_SESSION["numero_minimo"] = $num_jugador;
    }
    //en caso de no ser correcto se muestra lo siguiente
    ?> 
    <p>INCORRECTO </p>
    <p><a href="numerosal.php">INTENTARLO DE NUEVO</a></p>
<?php 
}
?>



</body>
</html>
