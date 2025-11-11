<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>conexion sql</title>
</head>
<body>
    

<?php

//programa para verificar si se establace una base de datos de SQL a PHP
$enlace = mysqli_connect("localhost", "root", "", "empresa");

if(!$enlace) {
    die ("no puedo conectarse con la base de datos" . mysqli_error());
}
echo "conexion establecida"; //se establecio conexion, si aparece en la pagina 
mysqli_close($enlace);
?>



</body>
</html>