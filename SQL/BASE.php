<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador</title>


</head>
<body>
    <h1> BUSCADOR DE USUARIOS  </h1>
<form action= "" method:"GET">
    <input type= "text" name="Nombre" placeholder="Ingrese el nombre">
    <input type="submit" value= "Buscar">

</form>
<?php
if (iseet($_GET["Nombre"]) && $_GET["Nombre"] != ''){
    //Conexion a BD

    $servername = "localhost";
    $username = "root";
    $password= "";
    $database= "users";
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->conect_error){
        die("Error en la conexión:" . $conn->conect_error);
    }

    $dato = $_GET['Nombre'];

    $SQL= "SELECT * FROM users WHERE nombre LIKE '%$dato'" ;
    $result  = $conn->query($SQL);
   if ($result->num_row >0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()){
        //10:30
    }
   }
    

}

?>




</body>
</html>