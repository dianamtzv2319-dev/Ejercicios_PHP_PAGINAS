<?php

//ruta del archivo
$rutaArchivo = "temperatura.csv";


//variables para promedio inciadas en 0
$total = 0;
$cuenta = 0;

// Variables para el día más caluroso 
$temp_maxima = -100; // Un número muy bajo para empezar
//-100 para que cuando lea una temperatura se convierta en la maxima
$dia_caluroso = "";  // Para guardar la fecha


//columna 1 es '0'  FECHA
//columna 2 es '1' TEMPERATURA
$columna_temp = 1; 


//si el archivo no existe el codigo va al final del codigo
if(($archivo = fopen($rutaArchivo, "r")) !== false) {
                                        //'r' de solo lectura
//si logra la conexion con el archivo, lo convierte en variable
    
    echo "<table>"; //imprime el contenido de la tabla



        while (($datos = fgetcsv($archivo, 1000,",")) !== false){
    /* arreglo de cada uno de los datos  
                                 (leer archivo, num de caracteres por linea), delimitador (,)
    $datos = convierte cada linea en array
     */
            
        echo "<tr>"; //filas de una tabla

        foreach ($datos as $key => $celda){ //bucle
            //foreach procesa cada linea
            echo "<td>$celda</td>";  //dato para columnas

            
            //verificacion de que la columna a promediar sean numeros
            if ($key == $columna_temp && is_numeric($celda)){
                //comprueba si el valor es numerico
                // Lógica de promedio
                $total += $celda; //se suman los valores
                $cuenta++; //incrementa 1 al contador 
                
                // Lógica para encontrar el máximo 
                // Comparamos la $celda (temp. actual) con la máxima guardada
                if ($celda > $temp_maxima) { //actualiza la mas alta
                    $temp_maxima = $celda; // Guardamos la nueva temp máxima
                    // Guardamos la fecha (que está en la columna 0 del array $datos)
                    $dia_caluroso = $datos[0]; //guaaarda la fecha de la temp mas alta
                }
            }
    
        }
        echo "</tr>"; 

    }
    fclose($archivo);  //SE CIERRA EL ARCHIVO, cierra la conexion
    echo "</table>"; //SE CIERRA LA TABLA
    
    //para mostrar el promedio:
    echo "<hr>"; //linea
    if ($cuenta > 0) {
        $promedio = $total / $cuenta; //realiza el promedio

    
        echo "<h2> Promedio de temperatura:" . number_format($promedio, 2) . "</h2>";
                                        //redondeo a dos decimales
    } else {
        // (Corregí un error de tipeo aquí, decía "encontratron")
        echo "<h2>No se encontraron valores</h2>"; 
    }

    // PARA MOSTRAR EL DIA MAS CALUROS
    // Se muestra solo si encontramos al menos un día
    if ($dia_caluroso !== "") { //comprueba que la variable $dia_caluroso ya tiene elementos 
        echo "<h2>El día más caluroso fue: $dia_caluroso (con $temp_maxima grados)</h2>";
    }

//se pone esta linea en caso de que no se pueda abrir o hacer conexion con el archivo
} else { //en caso de que el if sea falso
    echo "no se ha podido abrir el archivo";
}