<?php 
session_start();
//crea una sesion o reanuda la que ya esta 
//es parecida a memoria temporal
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

    if (!isset($_SESSION["numero_minimo"])) { 
        //!= NO----
        // -if(!isset)= si no existe
        $_SESSION["numero_minimo"] = 1; //esto solo se cumplira la primera vez despues se mueven los rangos
        $_SESSION["numero_maximo"]= 100;
    } 

  if (!isset($_SESSION["numero_minimo2"])) { 
        $_SESSION["numero_minimo2"] = 1; //esto solo se cumplira la primera vez despues se mueven los rangos
        $_SESSION["numero_maximo2"]= 100;
    } 
    


    ?>
<center>
<p><strong>Juego: Adivina un número</strong></p>
<form method="post" action="comprobar.php">

<!--COMENNNNNNT El formulario se manda al otro archivo -->


            <p>Escribe un número entre <?php echo $_SESSION["numero_minimo"]?> y <?php echo $_SESSION["numero_maximo"]
                //muestra un mensaje que va cambiando
                ?>:</p>
        
        
        <input type="number" name="form_num" min="1" max="100" required/>
<br>
        <input type="submit" value="comprobrar"/>
            
 </form>
        
   
<br><br><br><br><br><br><br><br><br>
<p><strong>Juego: Adivina un número  (remasterizado) <br> ¿cansado de demasiados intentos...? <br> Prueba el modo dificil  </strong></p>
<form method="post" action="mododificil.php">

<p>Escribe un número entre <?php echo $_SESSION["numero_minimo2"]?> y <?php echo $_SESSION["numero_maximo2"]
                //muestra un mensaje que va cambiando
                ?>:</p>
<input type="number" name="form_dificilnum" min="1" max="100"required/>
<br>
<input type="submit" value="Probar"/>

 </form>
</center>

</body>


</html>








