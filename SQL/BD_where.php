
     <?php 

    include "conectar.php";
   
    //DELETE FROM ---BORRAR
    //UPDATE base SET variable=cambio-----actualizar
$sql= "SELECT * FROM users WHERE email= 'aurelio.moya@example.com'";
//selecciona los registros de users
//devuelve un conjunto de datos
                           //WHERE fILTRO PARA ALGUNA VARIABLE
//filtrado con condiciones
                            //ORDER BY variable ASC/DESC

 
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
    
