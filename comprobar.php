<?php 
session_start();
//crea una sesion o reanuda la que ya esta 



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

// VALIDACIÓN PRIMERO

if ( !isset($_POST["form_num"]) || !is_numeric($_POST["form_num"]) ) {
//if != si no 
    echo "<p>Error: No has enviado un número válido.</p>";
    echo '<p><a href="numerosal.php">Volver al juego</a></p>';

} else {

    // Si la validación pasa, continuamos con el juego

    // Se obtiene el número (convertido a entero)
    $num_jugador = (int)$_POST["form_num"];

    // Se incrementa el número de intentos
    if (isset($_SESSION["num_intentos"])) { 
        $_SESSION["num_intentos"]++;
    } else {
        $_SESSION["num_intentos"] = 1;
    }

    // Se muestra el número de intentos
    ?>
    <p>Número de intentos: <?php echo $_SESSION["num_intentos"]; ?></p>
    <?php

    // Si no existe número aleatorio en sesión, se genera uno nuevo
    if (!isset($_SESSION["num_aleatorio"])) {
        $_SESSION["num_aleatorio"] = rand(1, 100);  //NUMERO RANDOM 1-100
        $_SESSION["numero_minimo"] = 1; 
        $_SESSION["numero_maximo"] = 100;
    }

    // Se comprueba si el número del jugador es correcto
    if ($_SESSION["num_aleatorio"] == $num_jugador) {
        // Si se adivina el número, se destruye la sesión (nuevo juego)
        session_destroy();
        ?>
        <p>¡CORRECTO! El número era <?php echo $num_jugador; ?>.</p>

        <p><a href="numerosal.php">NUEVA PARTIDA</a></p>
    <?php 
    } else { 
        // Se actualizan los valores mínimos y máximos
        if ($num_jugador > $_SESSION["num_aleatorio"]) {
            $_SESSION["numero_maximo"] = $num_jugador;
        } else {
            $_SESSION["numero_minimo"] = $num_jugador;
        }
        ?> 
        <p>INCORRECTO </p>
        
        <p>El número secreto está entre 
           <b><?php echo $_SESSION["numero_minimo"]; ?></b> y 
           <b><?php echo $_SESSION["numero_maximo"]; ?></b>
        </p>
        
        <p><a href="numerosal.php">INTENTARLO DE NUEVO</a></p>
    <?php 
    }
} // Cierre del 'else' de la validación
?>

</body>
</html>