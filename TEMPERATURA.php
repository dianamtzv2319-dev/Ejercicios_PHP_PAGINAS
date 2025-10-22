<?php

//ruta del archivo
$rutaArchivo = "temperatura.csv";


//variables para promedio inciadas en 0
$total = 0;
$cuenta = 0;


//el encabezado es "Fecha,temperatura,humedad", la temperatura es la columna 1.
$columna_temp = 1; // 0="Fecha", 1="temperatura"

if(($archivo = fopen($rutaArchivo, "r")) !== false) {
                                        //'r' de solo lectura

    echo "<table>"; //contenido de la tabla

    $encabezado = fgetcsv($archivo, 1000, ";");
    //para que el programa empiece a leer desde la segunda linea
    //ya que el programa cuenta con encabezado:  Fecha,temperatura, humedad

    while (($datos = fgetcsv($archivo, 1000,";")) !== false){
    /* arreglo de cada uno de los datos    $leer archivo, num de caracteres por linea, delimitador (;)
    */
            
        echo "<tr>"; //filas de una tabla

        foreach ($datos as $key => $celda){ //bucle
            
            echo "<td>$celda</td>";  //dato para columnas

            
            //verificacion de que la columna a promediar sean numeros
            if ($key == $columna_temp && is_numeric($celda)){
                $total += $celda; //se suman los valores
                $cuenta++; //incrementa
            }
    
        }
        echo "</tr>"; 

    }
    fclose($archivo);  //SE CIERRA EL ARCHIVO
    echo "</table>"; //SE CIERRA LA TABLA
    
    //para mostrar el promedio:
    echo "<hr>"; //linea
    if ($cuenta > 0) {
        $promedio = $total / $cuenta;

        // CORREGIDO: Faltaba '<' en <h2>
        echo "<h2> Promedio de temperatura:" . number_format($promedio, 2) . "</h2>";
                                        //redondeo a dos decimales
    } else {
        echo "No se encontratron valores";
    }


} else { //en caso de que el if sea falso
    echo "no se ha podido abrir el archivo";
}

?>