<?php 
session_start();
//inicia sesion
//es una memoria


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adivina el número</title>
</head>
<body>
    <?php


    //JUEGO 1
    if (!isset($_SESSION["numero_minimo"])) {  //si !=NO EXISTE
        $_SESSION["numero_minimo"] = 1;//numero minimo para adivina
        $_SESSION["numero_maximo"]= 100; //numero maximo para adivina
    } 


    //JUEGO 2
  
    if (!isset($_SESSION["numero_minimo2"])) { 
        $_SESSION["numero_minimo2"] = 1; //numero minimo para adivina
        $_SESSION["numero_maximo2"]= 100;//numero maximo para adivina
    } 
    ?>
<center>
   

<p><strong>Juego: Adivina un número</strong></p>
<form method="post" action="comprobar.php"> 
   
   
    <p>Escribe un número entre <?php echo $_SESSION["numero_minimo"]?> y <?php echo $_SESSION["numero_maximo"]?>:</p>
    
    <input type="number" name="form_num" 
           min="<?php echo $_SESSION["numero_minimo"]; ?>" 
           max="<?php echo $_SESSION["numero_maximo"]; ?>" 
           required/>
    <br>
    <input type="submit" value="comprobrar"/>
</form>
    
<br><br><br><br><br><br><br><br><br>

<p><strong>Juego: Adivina un número (remasterizado) <br> ¿cansado de demasiados intentos...? <br> Prueba el modo dificil </strong></p>
<form method="post" action="mododificil.php">
    <p>Escribe un número entre <?php echo $_SESSION["numero_minimo2"]?> y <?php echo $_SESSION["numero_maximo2"]?>:</p>

    <input type="number" name="form_dificilnum" 
           min="<?php echo $_SESSION["numero_minimo2"]; ?>" 
           max="<?php echo $_SESSION["numero_maximo2"]; ?>" 
           required/>
    <br>
    <input type="submit" value="Probar"/>
</form>
</center>

</body>
</html>




