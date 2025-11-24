<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONSULTAS</title>
</head>
<body>
    <div id="search">
    <form action= "index.php"> 
      <input type="text"  name="search" placeholder="escribe una palabra">
    <input type="submit" value="Buscar">
    </form>
</div>
<div id="results">

     <?php 

    include "conectar.php";
    $filter= "";
    $search= (isset($_GET['search'])) ? $_GET['search'] : "" ;
    //si (isset($_GET['search'])) esta definido obten $_GET['search'] 
    //: representan un ELSE 
    //y si no esta ponlo vacio


    if (isset($search)&&strlen($search)>0){ 
        //si hubo una busqueda y  
        //si el texto que tiene get es mayor a 0
        //strlen cuenta el tamaño de la cadena 
        $filter= " WHERE name_first LIKE '%$search%'"; 
        $consulta=$consulta . $filter;
    }
    

$sql= "SELECT * FROM users";
//selecciona los registros de users
//devuelve un conjunto de datos

$resultado =$conn->query($sql);
//trae todos los registros 
//ejecuta la instruccion de arriba


if($resultado->num_rows>0){
    //si tiene informacion /algoo mas de 0
    echo "Registros encontrados </br>";
    while ($fila=$resultado->fetch_assoc()){
       // print_r($fila);
       echo "Nombre: " . $fila['name_first']. "\n".$fila['name_last']." Correo: ". $fila['email']. "</br>";
    }
} else {
    echo "no hay registros";
    }

?>
    
</body>
</html>