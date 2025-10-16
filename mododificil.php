<?php 
session_start(); // Inicia o reanuda la sesión
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
// Se obtiene el número ingresado por el usuario
$num_jugador = (int)$_POST["form_dificilnum"];

// se inicializa la sesión del juego
if (!isset($_SESSION["num_aleatorio2"])) {
    $_SESSION["num_aleatorio2"] = rand(1, 100); // numero del 1al 100
    $_SESSION["intent_rest"] = 5;               // intentos restantes
    $_SESSION["numero_minimo2"] = 1;            // límite min
    $_SESSION["numero_maximo2"] = 100;          // límite max
}

// Se muestra el número de intentos realizados y restantes
$intentos_realizados = 5 - $_SESSION["intent_rest"]; // porque empezamos con 5
?>
<p>Número de intentos: <?php echo $intentos_realizados; //imprime los que lleva ?></p>
<p>Intentos restantes: <?php echo $_SESSION["intent_rest"]; //imprime los restantes?></p>

<?php
// Se comprueba si el número del jugador es correcto
if ($_SESSION["num_aleatorio2"] == $num_jugador) {
    // Si se adivina el número, se acaba el juego
    
    ?>
    <p>¡CORRECTO!</p>
    <p><a href="numerosal.php">NUEVA PARTIDA</a></p>
<?php session_destroy(); ?>
<?php
} else {
    // Si es incorrecto, se resta un intento
    $_SESSION["intent_rest"]--; //se restan intentos

    // Comprobamos si hay intentos restantes
    if ($_SESSION["intent_rest"] == 0) { //si aun quedan intentos continua
        // Se acabaron los intentos: juego perdido
        $numero_secreto = $_SESSION["num_aleatorio2"];
        

        // en caso de que no queden intentos, el usuario pierde ?> 
        <p>¡PERDISTE!se acabaron tus 5 intentos</p>
        <p>El número que tenias que adivinar era: <?php echo $numero_secreto; ?></p>
        <p><a href="numerosal.php">Jugar de nuevo</a></p>
        <?php session_destroy(); ?>
        <?php
    } else { //en caso de que un queden intentos, se continua con el jeugo
        // Se actualizan los valores mínimos y máximos como pista
        if ($num_jugador > $_SESSION["num_aleatorio2"]) {
            $_SESSION["numero_maximo2"] = $num_jugador; //limites para los numeros
            echo "<p>El número es menor que $num_jugador.</p>";
        } else {
            $_SESSION["numero_minimo2"] = $num_jugador;
            echo "<p>El número es mayor que $num_jugador.</p>";
        }
        ?>
        <p><strong>INCORRECTO, Sigue intentando</strong></p>
        <p><strong>Pista:</strong> El número está entre <?php echo $_SESSION["numero_minimo2"]; ?> y <?php echo $_SESSION["numero_maximo2"]; ?></p>
        <p><a href="numerosal.php">INTENTAR DE NUEVO</a></p>
        <?php
    }
}
?>

</body>
</html>
