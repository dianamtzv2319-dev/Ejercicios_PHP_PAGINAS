<?php
// Inicializar variable
$password = "";
$mensaje = "";
// Validar si se envió el formulario
 
if (isset($_POSt['txtpassword']))
    {
    $password = $_POST['txtpassword'];
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar contraseña</title>
</head>
<body>

    <form action="login.php" method="post">
     
        Contraseña:
        <input type="text" name="txtpassword" id="txtpassword"> <! --espacio para escribir --/>
        <br/>
        <input type="submit" value="Enviar">  <! --boton de enviar --/>
    </form>

</body>
</html>

