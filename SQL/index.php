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
    require_once 'conectar.php'; 
    $consulta="SELECT * FROM users" ;
    $results= mysqli_query($conn, $consulta);
  
      while($row=mysqli_fetch_array($results)){
      ?>
        <div class="items">
            <h2><?php echo $row ['name_first'] ; ?></h2>
            <h3><?php echo $row ['location_street_name'] ; ?></h3>
            <h3><?php echo $row ['location_city'] ; ?></h3>
            <h3><?php echo $row ['location_state'] ; ?></h3>
            <p><?php echo $row ['email'] ; ?></p>
       <?php 
   
    }
    ?>


    
</body>
</html>