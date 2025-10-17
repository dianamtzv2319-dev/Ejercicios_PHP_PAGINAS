<?php

//ruta del archivo
$rutaArchivo = "temperatura.csv";


//fopen=retorna el contenido del archivo o falso cuando no sse pueda abrir
if(($archivo= fopen($rutaArchivo, "r")) ==! false) {
                                //'r' de solo lectura

    echo "<table>"; //contennido de la tabla

    while (($datos = fgetcsv($archivo, 1000,";"))!==false){
    /*        arreglo de cada uno de los datos    $leer archivo, num de caracteres por linea, delimitador (;)
        */
              
        echo "<tr>"; //filas de una tabla

        foreach ($datos as $key =>$datos){ //bucle
            
            echo "<td>$datos</td>";  //dato para columnas
    }
    echo "/tr"; 

    }
    fclose($archivo);  //SE CIERRA EL ARCHIVO
    
    echo "</table>"; //SE CIERRA LA TABLA

}else { //en caso de que el if sea falso
    echo "no se ha podido abrir el archivo";
}


?>