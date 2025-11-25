
     <?php 

    include "conectar.php";
        

$sql= "SELECT * FROM users";
//selecciona los registros de users
//devuelve un conjunto de datos
//filtrado

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
    
