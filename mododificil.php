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

if ( !isset($_POST["form_dificilnum"]) || !is_numeric($_POST["form_dificilnum"]) ) {

    echo "<p>Error: No has enviado un número válido.</p>";
    echo '<p><a href="numerosal.php">Volver al juego</a></p>';

} else {
    // SI LO ANTERIOR PASA, SE EJECUTA EL JUEGO

    $num_jugador = (int)$_POST["form_dificilnum"];

    // se inicializa la sesión del juego (solo la primera vez)
    if (!isset($_SESSION["num_aleatorio2"])) {
        $_SESSION["num_aleatorio2"] = rand(1, 100); //NUMERO RANDOM 1-100
        $_SESSION["intent_rest"] = 5;          
        $_SESSION["numero_minimo2"] = 1;      
        $_SESSION["numero_maximo2"] = 100;    
    }

    //conteo de intentos
    $intento_actual = 5 - $_SESSION["intent_rest"] + 1;
    ?>
    <p><strong>Intento: <?php echo $intento_actual; ?> de 5</strong></p>
    <?php

    // Se comprueba si el número del jugador es correcto
    if ($_SESSION["num_aleatorio2"] == $num_jugador) {
        //SI SE GANO EL JUEGO:
        ?>
        <p>¡CORRECTO! </p>
        <p><a href="numerosal.php">NUEVA PARTIDA</a></p>
        <?php 








        session_destroy(); 
    } else {
        // SI ES INCORRECTO
        $_SESSION["intent_rest"]--; // Se resta un intento

        if ($_SESSION["intent_rest"] == 0) {
            // SSI ES INCORRECTO
            $numero_secreto = $_SESSION["num_aleatorio2"];
            ?> 
            <p>¡PERDISTE! Se acabaron tus 5 intentos.</p>
            <p>El número que tenias que adivinar era: <?php echo $numero_secreto; ?></p>
            <p><a href="numerosal.php">Jugar de nuevo</a></p>
            <?php 
            session_destroy(); 

        } else {
            // SI AUN TIENE OPRTUNIDADES, SE DAN PISTAS
            if ($num_jugador > $_SESSION["num_aleatorio2"]) {
                $_SESSION["numero_maximo2"] = $num_jugador; 
                echo "<p>El número es <strong>menor</strong> que $num_jugador.</p>";
            } else {
                $_SESSION["numero_minimo2"] = $num_jugador;
                echo "<p>El número es <strong>mayor</strong> que $num_jugador.</p>";
            }
            ?>
            <p><strong>INCORRECTO, Sigue intentando...</strong></Gg>
            <p><strong>Pista:</strong> El número está entre <?php echo $_SESSION["numero_minimo2"]; ?> y <?php echo $_SESSION["numero_maximo2"]; ?></p>
            <p><a href="numerosal.php">INTENTAR DE NUEVO</a></p>
            <?php
        }
    }
} // Cierre del 'else' de la validación
?>

</body>
</html>