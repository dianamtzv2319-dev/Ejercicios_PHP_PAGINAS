<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador</title>
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

    //Se conecto la base de datos de USERS
    //la conexion está dentro del archivo "conectar.php"
    require_once 'conectar.php'; 
    $consulta="SELECT * FROM users" ;  //la consulta se hace a la BD de "users" en PHPmyadmin
    $filter= "";
    $search= (isset($_GET['search'])) ? $_GET['search'] : "" ;
    //si (isset($_GET['search'])) esta definido obten $_GET['search'] 
    //: representan un ELSE 
    //y si no esta ponlo basio


    if (isset($search)&&strlen($search)>0){ 
        //si hubo una busqueda y  
        //si el texto que tiene get es mayor a 0
        //strlen cuenta el tamaño de la cadena 
        $filter= " WHERE name_first LIKE '%$search%'"; 
        $consulta=$consulta . $filter;
    }



    $results= mysqli_query($conn, $consulta);
  
      while($row=mysqli_fetch_array($results)){
      ?>
        <div class="items">
        <h2>
            <?php //primero que aparezca el nombre en una sola linea
            echo $row ['name_first'] ;  //nombre del titulo que se imprimira
            echo "\n"; //espacio 
            echo $row ['name_last'] ; ?>
        </h2>

     <h4>
     <?php //informacion personal
     echo "Informacion personal:";    //subtitulo de info personal ?>
    </h4>

            <p> <?php echo "Género: " ;
            echo $row ['gender'] ; //genero 
                 ?> </P> 

            <p> <?php echo "Nacionalidad: "; 
                    echo $row ['nat'] ;  //nacionalidad
                ?>  </p>

            <p> <?php echo "Fecha de nacimiento: ";  
                    echo $row ['dob_date'] ; //fecha de nacimiento
                ?>  </p>
            
            <p> <?php echo "Edad: ";  
                    echo $row ['dob_age'] ; //fecha de nacimiento
                ?>  </p>

            

     <h4>
    <?php //informacion personal
    echo "Domicilio";    //subtitulo de domicilio?>
    </h4>

             <p>
                <?php // la dirección
            echo "Número de Calle: ";
            echo $row ['location_street_number'] ; 
            echo ', Calle: ';
            echo $row ['location_street_name'] ;
            echo ', País: ';
            echo $row ['location_country'] ; 
            echo ', Estado: ';
            echo $row ['location_state'] ; 
            echo ', Ciudad: ';
            echo $row ['location_city'] ; 
            echo ', Código Póstal: ';
            echo $row ['location_postcode'] ; 
            ?> </p>

        <p>
            <?php 
             echo "Coordenadas geográficas: "; 
            echo $row ['location_coordinates_latitude'] ; 
            echo ', ';
            echo $row ['location_coordinates_longitude'] ; 
            echo ', ';
            ?> </p>

        <p>
            <?php 
            echo "Descripcion de la zona: ";
            echo $row ['location_timezone_description'] ; 
            ?> </p>
    

        <p>
            <?php 
            echo "Horas de offset: ";
            echo $row ['location_timezone_offset'] ;  
            ?> </p>
        
    <h4>
    <?php  //contacto
    echo "Contacto: ";    //subtitulo de contacto?>
    </h4>   

            <p>
                <?php 
                echo "Email: "; //EMAIL 
                echo $row ['email'] ;
                ?> </p>


            <p>
                <?php 
                echo "Teléfono: ";
                echo $row ['cell'] ;
            ?> </p>


            <p>
                <?php 
                echo "Celular: ";
                echo $row ['cell'] ;
            ?> </p>

           
    <h4>
    <?php  //USER
    echo "Datos de usuario: ";    //subtitulo de contacto?>
    </h4>   

        <p>
                <?php //USER
                echo "Login UUID: ";
                echo $row ['login_uuid'] ;
            ?> </p>

        <p>
                <?php //USERNAME
                echo "USERNAME: ";
                echo $row ['login_username'] ;?> </p>
        <p>
                <?php //PASSWORD
                echo "PASSWORD: ";
                echo $row ['login_password'] ;?> </p>

        <p>
                <?php //LOGIN SALT
                echo "LOGIN SALT: ";
                echo $row ['login_salt'] ;?> </p>

        <p>
                <?php //LOGIN MD5
                echo "LOGIN MD5: " ;
                echo $row ['login_md5'] ;?> </p>
        
        <p>
                <?php //LOGIN SHA1
                echo "LOGIN SHA1: ";
                echo $row ['login_sha1'] ;?> </p>
        <p>
                <?php //LOGIN 256
                echo "LOGIN 256: ";
                echo $row ['login_sha256'] ;?> </p>
          
        <p>
                <?php //
                echo "Dia de registro: ";
                echo $row ['registered_date'] ;?> </p>
        
         <p>
                <?php //
                echo "ID NAME: ";
                echo $row ['id_name'] ;?> </p>

        <p>
                <?php //
                echo "ID VALUE: "; 
                echo $row ['id_value'] ;?> </p>

        <p>
                <?php //
                echo "FOTO DE LARGO: ";
                echo $row ['picture_large'] ;?> </p>

        <p>
                <?php //
                echo "FOTO MEDIA: ";
                echo $row ['picture_medium'] ;?> </p>

        <p>
                <?php //
                echo "FOTO: ";
                echo $row ['picture_thumbnail'] ;?> </p>

    

       <?php 
   
    }
    ?>


    
</body>
</html>