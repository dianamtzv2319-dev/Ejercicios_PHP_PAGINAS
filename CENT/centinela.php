<?php
namespace Controllers\Api\Internal;

//programa de centinela, aprte 1
//este programa e suna parte de CENTINELA
//cuenta con algunas cosas automtizadas como envio de correo, mensajes y demas
//más adelante se explica que hace o para que sirve cada cosa 




/*
header('Content-Type: application/json'); //PARA CONVERTIRLO EN TIPO JSON
    //PARA FORMAR UN JSON
    echo json_encode($f_array, JSON_PRETTY_PRINT); //PARA?
    //json_encode(...): LO CONVIERTE EN formato JSON
    //JSON_PRETTY_PRINT: 
    // Sin esto, el JSON saldría todo pegado en una sola línea. Con esto, sale ordenado, con espacios y saltos de línea para que sea fácil de leer por humanos.
*/


require_once __DIR__ . '/../../../Helpers/common_functions.php';
//el archivo de functions
//QUERY: INSRTAR, LEER, ACTUALLIZAR 
//SANITIZACIÓN FILTRO PARA NO ROMPER EL CODIGO, SOLO TEXTO Y NO COMANDOS DESTRUCTIVOS 
class ServersApiController {
  public function node_stats_logger() {
  //public: puede ser llamada desde cualquier parte
  //function node_stats_logger (): declara el nombre de la función por el nombre:
  //node_stats_logger: nombre de la clase
  

    if (!isset($_POST['token']) ){ //!isset!= NO ---> SI NO EXISTE 
      //busca el dato 'token' enviado por método post
      echo "ERROR no token received.";
      exit; //Si no existe no ejecuta
    }

    if (!isset($_POST['json'])){ //!isset!= NO ---> SI NO EXISTE
      //busca el dato de 'json'
      echo "ERROR. No se encuentra el campo JSON.";
      exit; //si no existe no lo ejecuta
    }

    $token = $_POST['token'];//valido el token
    //guarda el valor de 'token' 
    if ($token!= "12kJp3QyL9FmonNXDzihjK3ZU8rDwmU"){
    //!= diferente de...
    //compara si el valor de TOKEN es como el otro, si no es identico, lo detiene
      echo "ERROR. Token incorrecto."; //en caso de no ser correcto
      exit; //detiene 
    }

    $json_text = $_POST['json']; 
    //Toma lo que fue enviado al JSON mediante el metodo POST
    //y lo guarda en la variable $json_text
    $json = json_decode($json_text); 
    //json_decode: funcion que toma la cadena y la convierte en objeto de PHP
    /* al convertirlo en objeto, tiene propiedades como:
    fecha, host, cpu
    */   
    //decodificar la variable


    $conn = conect2db("sbi_admin_data"); //hace la conexion 
    //se conecta a: "sbi_admin_data" ¿Es el archivo de conexion?
    $hora = $json->fecha; //fecha del JSON
    //-> = acceder propiedades de un objeto
    //busca la propiedad dentro de JSON y la guarda en la variable $hora
    $host = $json->host; //el host del JSON
    //busca la propiedad dentro de JSON y la guarda en la variable $host
    //¿puede ser IP o nomre?
    $max_processors = $json->max_processors;
    //dato de cuantos proesadores tiene y guarda el dato
    $cpu_use = $json->cpu_use;
    //saca dato de uso de CPU y lo gurda
    $node_temp = $json->node_temp;
    //*******
    $storage_units = $json->storage_units;
    //unidades de almacenamiento
    $total_storage = $json->total_hdd;
    //almacenamiento total
    $used_storage = $json->used_hdd;
    //almacenamiento usado
    $ram_available = $json->ram_disp;
    //Ram disponible
    $avg_load = $json->avg_load;
    //*********
    $errors = "";
    // Proceso RAID si existe
        //¿es el proceso de los discos?

    if (property_exists($json, "raids")) {
    //Si existe la propiedad, ejecuta lo siguiente
    //hay algunos que no los tiene, por eso el : if (property_exists)
      $raid_data = $json->raids;
    // ---------------------------------------------------------------------------------------
      foreach ($raid_data as $raid) {
        $raid_id = $raid->raid_id;
        //del $ saca= -> raid_id
        // aqui se guarda= ---- toma lo que encontraste y guarda
        $raid_status = $raid->raid_status;
        //-> = busca la propiedad
        $raid_type = $raid->raid_type;
        //TIPO DE RAID
        $raid_cache = $raid->raid_cache;
        //CACHE
        $raid_size = $raid->raid_size;
        //TAMAÑO DEL RAID
        $raid_cachecade = $raid->raid_cachecade;
        
        $raid_scheduled = $raid->raid_scheduled;

        $raid_access = $raid->raid_access;
        //ACCESO DEL RAID
        $raid_date_updated = $raid->raid_date_updated;
        //ACTUALIZACION DEL RAID
        $raid_consistency = $raid->raid_consistency;
        //CONSISTENCIA DEL RAID



        // checa si ya está dado de alta
        $sql0 = "SELECT * FROM raid_stats WHERE raid_node = '$host' AND raid_id = '$raid_id'";
        //seleccionar de la DB
        $result_r = $conn->query($sql0);
        //conexion a sql 
        
        
         

        if ($result_r->num_rows == 0) {
          //numero de columnas 
          $sql = "INSERT INTO raid_stats (raid_node, raid_id, raid_status, raid_type, raid_cache, raid_size, raid_cachecade, raid_scheduled, raid_access, raid_date_updated, raid_consistency) VALUES ('$host', '$raid_id', '$raid_status', '$raid_type', '$raid_cache', '$raid_size', '$raid_cachecade', '$raid_scheduled', '$raid_access', '$raid_date_updated', '$raid_consistency'); ";
        } //se insertan los datos?
        else{
          $sql = "UPDATE raid_stats set raid_status = '$raid_status', raid_type = '$raid_type', raid_cache = '$raid_cache', raid_size = '$raid_size', raid_cachecade = '$raid_cachecade', raid_scheduled = '$raid_scheduled', raid_access = '$raid_access', raid_date_updated = '$raid_date_updated', raid_consistency = '$raid_consistency' where raid_id = '$raid_id' and raid_node = '$host' ";
        } //actualiza los datos 
        if (!$conn->query($sql)) {
          //en caso de error en la conexion, mandar mensaje de error
          $errors.= "Error: ". $sql. "<br>". $conn->error;
        }
      }
    }


    //subo la carga promedio
    $sql = "INSERT INTO avg_load (nodo, fecha, avg_load) VALUES ('$host', '$hora', '$avg_load')";
    //inserta en el archivo los datos
    if (!$conn->query($sql)) { //en caso de un error en la conexión
      $errors.= "Error: ". $sql. "<br>". $conn->error;
    }
    
    //-------------------------------------------------------------------------------------------------
    // subo las estadísticas a la base de nodos
    //busco si el registro existe en la base de nodos
    $sql = "SELECT * FROM nodes_stats WHERE host = '$host'";
    $result = $conn->query($sql);
    //conexion con los nodos


    //filas
    if ($result->num_rows > 0) { //si $ es mayo a 0 actualizar
      //si $resultado es mayor a 0, el registro ya existe 
      // update record 
      $sql = "UPDATE nodes_stats SET fecha = '$hora', temp = '$node_temp', storage_units = '$storage_units', total_storage = '$total_storage', used_storage = '$used_storage', ram_available = '$ram_available' WHERE host = '$host'";
      //si la condición anterior se cumple entonces ejecuta lo siguiente:
      //actualizar los datos sig:
      if (!$conn->query($sql)) {//en caso de un error en la conexión
        $errors.= "Error: ". $sql. "<br>". $conn->error;
      }
    } else { //en casp de que el registro no exista se salta lo anterior y ejeuta lo siguiente:
      // insert new record
      $sql = "INSERT INTO nodes_stats (fecha, host, temp, storage_units, total_storage, used_storage, ram_available) VALUES ('$hora', '$host', '$node_temp', '$storage_units', '$total_storage', '$used_storage', '$ram_available')";
      if (!$conn->query($sql)) {//en caso de un error en la conexión
        $errors.= "Error: ". $sql. "<br>". $conn->error;
      }//intenta ejecutar sql
      //en caso de que no agrega el mismo detalle de error
    }
    

    // subo los logs de procesos

    $pid_to_stop = array();

        // Subo los registros de usuarios a la base
    foreach ($cpu_use as $user => $proc_use){
      //cpu_use : la lista donde estan los datos de los usuarios
      $sql = "INSERT INTO user_behav (fecha, user_id, cpu_use, host) VALUES ('$hora', '$user', '$proc_use', '$host')"; //inserta estos datoos
      if (!$conn->query($sql)) { //en caso de un error en la conexión
        $errors .= "Error: ". $sql. "<br>". $conn->error;
      }
    }
    $mensaje = "";


    // checamos los ofensores
    $ofensores = $json->abusers; //lista de ofensores 
    foreach ($ofensores as $ofensor => $pids_array){
      //traigo el nombre y mail de la base de datos
      $nombre = $email = "";
    /* LO MISMO PERO MÁS CORTO 
    $email = "";
    $nombre = "";
      */
      $sql = "SELECT user_fullname, user_mail FROM usuarios WHERE user_name = '$ofensor'";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) { //SI EL RESULTADO ES MAYOR A 0 EJECUTA LOS SIGUIENTE 
        // output data of each row
        while($row = $result->fetch_assoc()) {
          //FETCH_ASSOC: LO HACE COMO EN FORMATO DE  NOMBRE = JUAN          EMAIL=JUAN.EMAIL.COM
          $nombre = $row["user_fullname"];
          $email = $row["user_mail"];
        }
      } 
      else{ //EN CASO DE QUE NO EXISTA ALGUN OFENSOR 
        $mensaje .= "No se encontró al usuario $ofensor en la base de datos. Aguas";
      }

      //subo los pids
      $this_ofensor_str = "";
      
      $pid_id = $pid_name = 0;
      foreach ($pids_array as $key => $value ){
        $pid_id = $key;
        $pid_name = $value;
      }



      //debe ser por aqui lo de teelgram
      //el error es de 6-9 am
      //gts git no toarlos como aletarta lo manda todos los dias 
      $this_ofensor_str .= "$pid_id: $pid_name<br>";
      $pid_name = substr($pid_name, 0, 499); #por si es muy largototote
      //checo si ya existe ese pid y host en la base de datos
      $sql = "SELECT * FROM avisos_uso_excesivo WHERE host = '$host' AND pid_ofensor = '$pid_id' and user_id ='$ofensor' and pid_desc = '$pid_name' order by fecha ASC LIMIT 1;";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) { 
        // si ya existe, verifico la hora
        while($row = $result->fetch_assoc()) {//TIPO DE LISTA 
          $fecha_base = $row["fecha"]; 
          $avisos = $row['msjs_enviados'];
          $diff = strtotime($hora) - strtotime($fecha_base); //CONVIERT LA FECH EN FORMATO: "2023-10-25 14:00"    RESTA LA HORA ACTUAL CON LA FECHA BASE (INICIO DE PROCESO)- DIFERENCIA DE SEGUNDOS
          if ($diff > 60*60*3 && $avisos <= 3) { //6O (S)=1MIN * 60 MIN (1H) * 3 (HORAS) = 3 HORAS A 3 SEGUNDOS 
            //SI EL PROCESO LLEVA MAS DE 3 HORAS CORRIENDO Y SE ENVIARON 3 AVISOS O MENOS...
          // if (1==1){

            // si la diferencia es mayor a 3 horas, marco este PID para ser detenidos
            array_push($pid_to_stop, $pid_id); //METE EL ID EN LISTA PARA DETENERLO
            $sql = "UPDATE avisos_uso_excesivo SET msjs_enviados = msjs_enviados + 1, sended_to_stop= NOW() WHERE host = '$host' AND pid_ofensor = '$pid_id' and user_id ='$ofensor' and pid_desc = '$pid_name' order by fecha ASC LIMIT 1;";
           //SIGUE AUMENTANDO LOS MENSAJES DEL USO EXCESIVO 
            if (!$conn->query($sql)) {
              $errors.= "Error: ". $sql. "<br>". $conn->error; //ERROR CON LA CONEXION
            }
            $asunto = "Avisos Centinela\nPrioridad: Media\nEl proceso $pid_id: $pid_name, lanzado por $ofensor, fue detenido en el nodo $host después de 3 horas de uso desmedido de recursos";
           //ASSUNTO ES AUTOMATIZADO QUE LA PERSONA FUE DETENIDA PORQUE USO DESMEDIDAMENTE RECURSOS 
            $mail_asunto_m = "Proceso detenido por consumo desmedido de recursos por más de 3 horas";
            //ASUNTO DEL MAIL: MENSAJE 
            $modo = "stop";
            $this->sendTelegram($asunto); //ENVIO A TELEGRAM
            $this->enviamail($mail_asunto_m,$modo, $ofensor, $this_ofensor_str, $max_processors, $host, $nombre, $email);//ENVIA AL MAIL
          }
        }
      } 
      else{
        //SUBE LOS DATOS DEL OFENSOR 
        //lo subo
        $sql = "INSERT INTO avisos_uso_excesivo (host, pid_ofensor, user_id, pid_desc, msjs_enviados, fecha) VALUES ('$host', '$pid_id', '$ofensor', '$pid_name', 1, '$hora')";
        if (!$conn->query($sql)) { //ERROR DE CONEXION 
          $errors.= "Error: ". $sql. "<br>". $conn->error;
        } //PARA PRIORIDAD BAJA EN EL USO DE RECURSOS 
        $asunto = "Avisos Centinela\nPrioridad: Baja\nNotificación automática de uso desmedido de recursos:\n $host => $pid_id: $pid_name generada por $ofensor";
        $mail_asunto_m = "notificación automática de uso desmedido de recursos"; //ASUNTO DE MAIL
        $modo = "append";
        $this->sendTelegram($asunto); //MENSAJE DE TELEGRAM
        $this->enviamail($mail_asunto_m,$modo, $ofensor, $this_ofensor_str, $max_processors, $host, $nombre, $email); //MAIL
        
      }
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    if ($errors != ""){ //***RESUMEN DE LOS ERRORES?
      $asunto = "ERRORES al procesar los datos de comprotamiento de los nodos $host";
      $modo = "error";
      $ofensor = $this_ofensor_str = $max_processors = "";
      $this->sendTelegram($asunto);
      $this->enviamail($asunto,$modo, $ofensor, $this_ofensor_str, $max_processors, $errors, "j", $email);
    }

    $f_array = array(); //CREA UN ARRAY
    //EN CASO DE NO ESTAR VACIA, ALGO FALLO 
    //SI ESTA VACIA TODO SALIO BIEN
    if ($errors != ""){ 
      $f_array['error'] = $errors; //SI HUBO ERRORES LOS GUARDA AQUI 
      $f_array['status'] = 'error';//ALGO COMO ERROR 
    }
    else{
      $f_array['status'] = 'success'; //EN CASO DE QUE NO SEA NADA DE LO ANTERIOR, ESYA BIEN 
    }

    if (sizeof($pid_to_stop) > 0){ //CUENTA LOS ELEMENTOS 
      $f_array['pids_to_stop'] = $pid_to_stop;
    }

    $conn->close(); //CERRAR LA CONEXION
    header('Content-Type: application/json'); //PARA CONVERTIRLO EN TIPO JSON
    //PARA FORMAR UN JSON
    echo json_encode($f_array, JSON_PRETTY_PRINT); //PARA?
    //json_encode(...): LO CONVIERTE EN formato JSON
    //JSON_PRETTY_PRINT: 
    // Sin esto, el JSON saldría todo pegado en una sola línea. Con esto, sale ordenado, con espacios y saltos de línea para que sea fácil de leer por humanos.
  }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

  public function cpu_snap (){ //FUNCION PUBLICA
    //ver si me pasaron input de nodo
    $infraestructura_sol = "fenix";

    if (isset($_GET['infraestructura'])){
      $infraestructura_sol = $_GET['infraestructura'];
    }

    $conn = conect2db("sbi_admin_data"); //CONEXION CON LA BASE DE DATOS
    $f_array = array();
    $now =  strtotime(date("Y/m/d H:i:s")) ; //FECHA YEAR/MONTH/DAY
    $yesterday = $now - 86400; // Restar 86400 segundos (1 día)       PARA ESTABLECER LA FECHA ANTERIOR
    $yesterdayStr = date("Y-m-d H:i:s", $yesterday); //FECHA DE AYER 

    //DELCARA TODO LO QUE QUIERE LLAMAR 
    $query = "SELECT nodo, fecha, avg_load, infraestructura  
    /*QUE TOENE QUE SELECCIONAR */
    from avg_load  /* dE DONDE LO TIENE QUE SELECCIONAR */
    LEFT JOIN  servers_sbi /* ¿SI ENCUENTRA UN MATCH? */
    ON avg_load.nodo = servers_sbi.nodo_id
    WHERE fecha >= '$yesterdayStr'"; //FECHA DE AYER 


    $result = $conn->query($query); //CONEXIONESS

    if ($result->num_rows > 0) { //SI CUENTA CON MAS DE 0 
      while($row = $result->fetch_assoc()) {
        $infraestructura = $row['infraestructura'];
        if ($infraestructura != $infraestructura_sol){ //SI NO ES IGUAL
          continue; //CONTINUA 
        }
        $nodo = $row['nodo'];
        $fecha = $row['fecha'];
        $cpu_use [$nodo][$fecha] = $row['avg_load'];
      }
    }

    $query_hilos = "SELECT nodo_id, hilos FROM servers_sbi where infraestructura = '$infraestructura_sol';";//SELECCIONAR COSAS Y PONERLAS EN LA VARIBLE?
    $result_hilos = $conn->query($query_hilos); //

    $hilos = [];

    if ($result_hilos->num_rows > 0) {
      while($row = $result_hilos->fetch_assoc()) {
        $hilos[$row['nodo_id']] = $row['hilos'];
      }
    }

    // print_r($hilos);

    // //recorro e imprimo el arreglo hilos
    // foreach ($hilos as $nodo => $hilo) {
    //   echo "Nodo: $nodo, Hilos: $hilo\n";
    // }

    // exit;


    $restructured = [];  //JSON RESTRUCTURADO
    //JSON VIEWER

    foreach ($cpu_use as $node => $dates) {
        $fechas = array_keys($dates);  // Obtener las fechas
        $raw = array_values($dates);
        $valores = array_values($dates);  // Obtener los valores
        $valores = array_map(function ($valor) use ($hilos, $node) {
            return isset($hilos[$node]) ? round($valor / $hilos[$node] * 100, 2) : 0;
        }, $valores);
        $restructured[] = [
            'node' => $node,
            'fechas' => $fechas,
            'valores' => $valores,
            'raw_values' => $raw,
        ];
    }
    $json = json_encode($restructured); //JSON RESTRCTURADO

    header('Content-Type: application/json'); // JSON 
    echo $json; //IMPRIMIRLO


  }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
  public function log_usuarios (){
    if ($_POST["token"] == ""){ //SI NO HAY TOKEN
      echo "Token vacío... Abortando";
    }
      
    if ($_POST["token"] != "c3VwZXJjb21wdXRvQWx0ZXJPTg=="){ //SI EL TOKEN ES == es INCORRECTO
      echo "Token incorrecto... Abortando";
      exit (0);
    }

    if (!isset($_POST['agent']) || !isset($_POST['method']) || !isset($_POST['task']) || !isset($_POST['node']) ){ //SI NO TIENEN VALORES 
      echo "Los campos agent, method, task y node son obligatorios... Abortando";
      exit(1);
    }

    // Recibir los parámetros
    $agent = $_POST['agent'];
    $method = $_POST['method'];
    $task = $_POST['task'];
    $node = $_POST['node'];

    // Validar que el agente y el método estén permitidos
    $allowedAgents = ['sbiadmin', 'wsantos', 'lgomez']; //los permitidos 
    $allowedMethods = ['manual', 'automatico']; //metodos permitidos 

    if (!in_array($agent, $allowedAgents)) { //si no es igual a cualquiera de estos
      echo "Agente no permitido... Abortando"; //no permite la entrada
      exit(1);
    }

    if (!in_array($method, $allowedMethods)) {
      echo "Método no permitido... Abortando";
      exit(1);
    }

    $conn = conect2db("sbi_admin_data");
    // Validar que el nodo esté permitido para el agente y el método
    $nodes = array();
    $nodes_query = "SELECT nodo_id FROM servers_sbi"; //selecciona desde la BD
    $nodes_result = $conn->query($nodes_query);

    if ($nodes_result->num_rows > 0) {
      while($row = $nodes_result->fetch_assoc()) {
        array_push($nodes, $row['nodo_id']);
      }
    }

    array_push($nodes,"all");

    if (!in_array($node, $nodes)) {//si NO 
      echo "Nodo $node no permitido para el agente y el método... Abortando";//si no es igual no está permitido
      exit(1);
    }

    //declarando las variables opcionales
    $date = isset($_POST['date']) ? intval($_POST['date']) : date('Y-m-d H:i:s'); //fecha
    $command = isset($_POST['command']) ? $_POST['command'] : ""; //comando
    $comment = isset($_POST['comment'])? $_POST['comment'] : ""; //comentario

    //preprarar la consulta de insert de la base de datos
    $query = "INSERT INTO bitacora_usuarios (fecha, agente, metodo, nodo, cambio, comando, comentario) VALUES ('$date', '$agent', '$method', '$node', '$task', '$command', '$comment')";


    //ejecutar la consulta
    if ($conn->query($query)) {
      echo "Tarea actualizada correctamente ";
    } else {//en caso de que no, se hace lo siguiente:
      echo "Error: ". $query. "<br>". $conn->error;
    }
  }

  public function aviso_cuenta_fenix(){
    $token = $_POST["token"];
    if ($token == ""){
      echo "Error: No recibí el token de autorización... Abortando";
      exit (0);
    }
    if ($token != "c3VwZXJjb21wdXRvQWx0ZXJPTg=="){
      echo "Token incorrecto... Abortando";
      exit (0);
    }

    if (!isset($_POST["user_name"]) || !isset($_POST["docker"])  || !isset($_POST["user_fullname"]) || !isset($_POST["user_group"])  || !isset($_POST["upa"]) || !isset($_POST["user_mail"]) || !isset($_POST["node"]) || !isset($_POST["vigencia"])  ){
      echo "campos incompletos... Abortando"; //si no se llenan
      exit (0);
    } 


    $user_id = $_POST['user_name']; //nombre del usuario
    $user_name = $_POST['user_fullname']; //nombre completo del usuario
    $user_mail = $_POST['user_mail']; //mail
    $user_group = $_POST['user_group']; //grupo
    $user_nodo = $_POST['node']; //noda
    $user_vigencia = $_POST['vigencia']; //vigencia
    $nodo_activo = $_POST["node"]; //¿si está activo?
    $upa = $_POST["upa"]; ????
    $docker = $_POST["docker"]; //???  ETERTTTHGBFHSRTJTYJYTJTYTUTDY&Y&$HY$H&%H&HYTYNJJJ

    $user_docker_string = "";
    $resp_docker_string = "";

    if ($docker == "yes"){ //si tiene un si:
      $user_docker_string = "Tu usuario también fue agregado al grupo Docker";
      $resp_docker_string = "Se agregó al usuario al grupo Docker.";
    }


    $user_name = str_replace("\"","", $user_name);

    // variables globales
    $nodo_name = ""; //nombre del nodo
    $ip_nodo = ""; //ip del nodo
    $nodo_puerto = ""; //puerto del nodo
    $responsable_name = ""; //nombre del responsable
    $gruoup_longname = ""; //grupo 
    $responsable_mail = ""; //mail del responsable
    $responsable_user = ""; //usuario responsable

    $conn = conect2db("sbi_admin_data"); //conexion
    $sql1 = "SELECT ip, puerto_externo, nodo_nombre FROM servers_sbi WHERE nodo_id = \"$nodo_activo\""; //de donde tener informacion
    $result1 = $conn->query($sql1);

    if ($result1->num_rows > 0) { //numero 
      while($row = $result1->fetch_assoc()) {
        $nodo_name = $row["nodo_nombre"];
        $ip_nodo = $row["ip"];
        $nodo_puerto = $row["puerto_externo"];
      }
    } 
    else {
      echo "0 results"; //en caso de que no, no se imprime nada
    }

    $sql2 = "SELECT 
    group_longname, user_fullname, user_mail, user_name
    from grupos
    left join usuarios
    on grupos.user_resp_id = usuarios.user_name
    where group_name = \"$user_group\""; //lo que tiene que seleccionar

    $result2 = $conn->query($sql2); //conexiones 

    if ($result1->num_rows > 0) {
      // output data of each row
      while($row = $result2->fetch_assoc()) {
        $gruoup_longname = $row["group_longname"]; // definir 
        $responsable_name = $row["user_fullname"];//
        $responsable_mail = $row["user_mail"];//
        $responsable_user = $row["user_name"];//
      }
    } 
    else {
      echo "0 results"; //en caso de no tener resultados
    }

    $headers  = 'MIME-Version: 1.0' . "\r\n"; 
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: "Bot de Supercómputo" ' . "\r\n";
    $headers .= "Reply-To: whernandez@inmegen.gob.mx \r\n"; //responder a 
    $email_subject = "Cuenta Creada en Sistema Fenix"; //
    $mail_body = $this->creaMailUser($nodo_name, $user_id, $upa, $ip_nodo, $nodo_puerto, $user_group, $user_name, $gruoup_longname, $responsable_name, $user_vigencia, $user_docker_string); //cuerpo del email

    $to = $user_mail;
    //$to = 'wsantos@ibt.unam.mx'. ', ';//<== update the email address

    if (mail($to,$email_subject,$mail_body,$headers)){
      if ($responsable_user != $user_name){
        // El responsable del grupo no coincide con el usuario que se está creando... Hay que mandar dos mails
        $email_subject = "Cuenta Creada en Sistema Fenix: Aviso para responsables";
        $resp_mail =  $this->creaMailResp($nodo_name, $user_id, $upa, $ip_nodo, $nodo_puerto, $user_group, $user_name, $gruoup_longname, $responsable_name, $user_vigencia, $user_mail, $resp_docker_string);
        if (mail($responsable_mail,$email_subject,$resp_mail,$headers)){
          echo "El mail de responsable y el mail del usuario se enviaron correctamente."; //imprimir que si se envio
        }
        else{
          echo "El mail del responsable no se pudo enviar correctamente."; //en caso de error
        }
      }
    }
    else{
      echo "ERROR: reintentar"; //error
      exit;
    }
  }

  public function node_detail(){
    if (!isset($_POST['nodo_name'])) {
      echo "No data, exit"; //no existe en caso de error
      exit;
    }
    $nodo_name = $_POST['nodo_name'];

    $f_array = array();
    $conn = conect2db("sbi_admin_data"); //conexiones
    $sql = "SELECT * FROM servers_sbi WHERE nodo_id = '$nodo_name'"; //sql
    $result = $conn->query($sql);

    $error_content = $status = "";

    if ($result->num_rows > 0) {
      $node_data = array();
      $status = "success"; //exito
      while ($row = $result->fetch_assoc()) {
        $node_data['node_name'] = $row["nodo_nombre"];
        $node_data['node_ip'] = $row['ip'];
        if ($row['puerto_externo'] != 0) { //puerto externo 
          $node_data['node_external_port'] = $row['puerto_externo'];
        } else {
          $node_data['node_external_port'] = "Sin puerto externo"; //sin puerto externo
        }
        if ($row['dominio'] != 'no') {//diferente de no?
          $node_data['node_domain'] = $row['dominio'] . ".inmegen.gob.mx";
        } else {
          $node_data['node_domain'] = "Sin dominio"; //sin dominio
        }
        if ($row['infraestructura'] != "NO") {
          $node_data['node_infrastructure'] = "Pertenece a la infraestructura " . strtoupper($row['infraestructura']);
        } else {
          $node_data['node_infrastructure'] = "Nodo independiente";
        }
        if ($row['is_virtual'] != "NO") {
          $node_data['node_virtual'] = "Es virtual";
        } else {
          $node_data['node_virtual'] = "No es virtual";
        }
        $node_data['node_storage'] = number_format($row['tb_almac']) . " TB"; //almacenamiento
        $node_data['node_processors'] = $row['hilos'];
        $node_data['node_memory'] = number_format($row['RAM']) . " GB"; //memoria
        $node_data['node_model'] = $row['modelo']; //modelo
        $node_data['node_brand'] = $row['marca']; //marca
        $node_data['node_serial'] = $row['serie'];//num serial
        $node_data['node_inventario'] = $row['no_inventario']; //nomero de inventario
        $node_data['node_location'] = $row['ubicacion']; //ubi
      }
      $f_array['data'] = $node_data;
    } else {
      $status = "error";
      $error_content = "No data";
    }


    $conn->close(); //se cierra la cnexion

    $f_array['status'] = $status;
    if ($error_content != "") {
      $f_array['error_content'] = $error_content;
    }

    // print array as json 
    $json = json_encode($f_array);

    header('Content-Type: application/json'); //json
    echo $json;
  }
  

  private function creaMailUser($nodo_name, $user_id, $upa, $ip_nodo, $nodo_puerto, $user_group, $user_name, $gruoup_longname, $responsable_name, $user_vigencia, $user_docker_string){
  $ruta = __DIR__ . '/../../../../storage/data/mail_template_styles/mail_class.css';
  $contenido = "";
  if (file_exists($ruta)) {
    $contenido = file_get_contents($ruta);
  } 
  else {
    $contenido = "";
  } //html


  //PARA AVISO DE  CUENTA CREADA
  $mail_usuario = <<<EOT              



<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>  
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
      rel="stylesheet">
    <!-- <link rel="stylesheet" href="/alertas/template/new_alert_style.css"> -->

    <title>Aviso para nuevos usuarios del Sistema Fenix</title>

    <style type="text/css">$contenido</style>
  </head>

  <body>

    <table  cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div class="head">
            <div class="logo">
              <div class="image">
                <img src="https://www.inmegen.gob.mx/static/zinnia/inmegen_logo_sl.png" alt="ban">
              </div>
            </div>
            

            </div>
            <div class="mail-body">
              <div class="greycard">
                <p class="head1">Cuenta creada en Sistema Fenix INMEGEN</p>
                <p class="spaced_up">Estimado(a) <span class="destacado">$user_name </span></p>
                <p>A partir de este momento cuentas con acceso al Sistema <b>Fenix</b>: nodo <b>$nodo_name</b> del Instituto Nacional de Medicina Genómica (INMEGEN) </a></p>
                <p>Tus credenciales de acceso son los siguientes:<br>
                   Tu usuario es: <span class="monospace">$user_id</span><br> 
                   Tu contraseña es: <span class="monospace">$upa</span><br>
                
                Esta contraseña es temporal, ya que el sistema te pedirá que la cambies en el primer acceso.</p>

                <p>Para acceder al Cluster considera lo siguiente: <br>
                  <ul>
                    <li>
                      Si te encuentras en la red del INMEGEN usa el comando: <br>
                      <span class="monospace">ssh <span class="destacado">$user_id</span>@$ip_nodo</span>
                    </li>
                    <li>
                      Si accedes desde el exterior del INMEGEN deberás usar el comando: <br>
                      <span class="monospace">ssh -p $nodo_puerto <span class="destacado">$user_id</span>@fenix.inmegen.gob.mx</span>
                    </li>
                  </ul>  
               </p>

               <p>
                Finalmente, te compartimos información relativa a tu uso:<br>
                Tu usuario pertenece al grupo <span class="destacado">$user_group ($gruoup_longname)</span>, cuyo responsable 
                es $responsable_name y quien será notificado(a) de la creación de tu cuenta.
                <br><br>
                Tu cuenta estará activa hasta el <span class="destacado">$user_vigencia</span>. Después de esa fecha, no podrás acceder al sistema a menos que tu responsable solicite la extensión de la misma. $user_docker_string
                <br><br> 
                 Como miembro del grupo $user_group tienes acceso al directorio compartido <b>/STORAGE/$user_group/</b>. 
                 Este directorio comparte espacio físico con toda la comunidad de investigación y cuenta con sistemas de redundancia que protegen la información a mediano plazo
                 en caso de alguna falla de disco. <br><br>
                 Te pedimos manejar el procesamiento de datos y almacenamiento mínimo desde <b>/home/$user_id/</b>, y mover los datos y resultados importantes a <b>/STORAGE/$user_group</b>/
               </p>

               <p>
                Es muy importante que leas y practiques las instrucciones contenidas en el 
                <b>"Manual de usuarios de la Infraestructura de supercómputo del INMEGEN"</b>
                puedes verlo haciendo click <a target="_blank" href="https://supercomputo.inmegen.gob.mx/docs/manualSupercomputo">aquí</a>
                <br><br>

              Si tienes alguna duda adicional, no dudes en contactarme en la dirección electrónica que aparece más abajo. <br>
              <span class="warning">Por favor, no contestes a este correo ya que fue enviado de forma automática y no es revisado regularmente. </span>
               </p>

               <p class="grey_text">
                Atentamente<br>
                Walter Santos<br>
                Jefatura de Supercómputo<br>
                Subdirección de Bioinformática<br>
                <a href="mailto:whernandez@inmegen.gob.mx">whernandez@inmegen.gob.mx</a><br>
                55 5350 1900 ext. 1951 <br><br>
                <img style="width: 120px" src="http://supercomputo.inmegen.gob.mx/assets/images/logo_small.png">
               </p>
                
              </div>

              
              <p class="note">
                AVISO DE CONFIDENCIALIDAD: De conformidad con lo establecido en el inciso a del artículo 57 del “Acuerdo por el que se
                emiten las políticas y disposiciones para impulsar el uso y aprovechamiento de la informática, el gobierno digital, las
                tecnologías de la información y comunicación, y la seguridad de la información en la Administración Pública Federal”, se
                informa que este mensaje y los datos adjuntos son para uso exclusivo de la persona o entidad a la que expresamente ha
                sido enviada, el cual puede contener información que por su naturaleza deba ser considerada como CONFIDENCIAL o
                RESERVADA, en términos de lo dispuesto por las Leyes General y Federal de Transparencia y Acceso a la Información
                Pública y General de Protección de Datos Personales en Posesión de Sujetos Obligados. Si por error ha recibido esta
                comunicación: 1) queda estrictamente prohibido la revelación, retransmisión, difusión, copia, impresión, modificación,
                alteración en toda o en alguna de sus partes o el uso de la información contenida en el archivo electrónico sea diverso
                al objeto por el cual se emitió; 2) notifíquelo al remitente; y 3) bórrelo de inmediato y en forma permanente, junto con
                cualquier copia digital o impresa, así como cualquier archivo anexo al mismo..<br><br>
              </p>
            </div>

          </div>
          <!-- <div class="grey_text">
            <p>
              &copy; 2024 Instituto Nacional de Medicina Genómica (INMEGEN)  <br><br>


              Periferico Sur 4809, Arenal Tepepan, Tlalpan, 14610 Ciudad de México, CDMX <br>
            </p>
            <div class="social-logos">
              <ul>
                <li>
                  <a href="https://www.facebook.com/biotecmov"><ion-icon name="logo-facebook"
                      class="social"></ion-icon></a>
                </li>
                <li>
                  <a href="https://instagram.com/biotecmov"><ion-icon name="logo-instagram"
                      class="social"></ion-icon></a>
                </li>
                <li>
                  <a href="https://twitter.com/ibt_unam"><ion-icon name="logo-twitter" class="social"></ion-icon></a>
                </li>

              </ul>
            </div>
            <div class="hgv">
              <p>
                Diseño / tecnología by Walter Santos (webmaster)</a>
              </p>
            </div>
          </div> -->
        </td>
      </tr>
    </table>
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
  </body>

</html>
EOT; 
return $mail_usuario;
} //para respues?

  private function creaMailResp($nodo_name, $user_id, $upa, $ip_nodo, $nodo_puerto, $user_group, $user_name, $gruoup_longname, $responsable_name, $user_vigencia, $user_mail, $resp_docker_string){
    $ruta = __DIR__ . '/../../../../storage/data/mail_template_styles/mail_class.css';
    $contenido = "";
    if (file_exists($ruta)) {
      $contenido = file_get_contents($ruta);
    } 
    else {
      $contenido = "";
    } //PARA FENIXXXXXXXXX
$mail_usuario = <<<EOT    


<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
      rel="stylesheet">
    <!-- <link rel="stylesheet" href="/alertas/template/new_alert_style.css"> -->

    <title>Aviso para responsables de nuevos usuarios del Sistema Fenix</title>

    <style type="text/css">$contenido</style>
  </head>

  <body>

    <table  cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div class="head">
            <div class="logo">
              <div class="image">
                <img src="https://www.inmegen.gob.mx/static/zinnia/inmegen_logo_sl.png" alt="ban">
              </div>
            </div>
            

            </div>
            <div class="mail-body">
              <div class="greycard">
                <p class="head1">Cuenta creada en Sistema Fenix INMEGEN</p>
                <p class="spaced_up">Estimado(a) <span class="destacado">$responsable_name </span></p>
                <p>
                Le informamos que hemos creado exitosamente la cuenta de acceso al sistema <b>Fenix</b> (nodo <b>$nodo_name</b>)
                para <span class="destacado">$user_name</span> de quien usted aparece como responsable en nuestros sistemas. <br><br>
                
                $user_name se ha añadido a su grupo de usuarios 
                <span class="destacado">$user_group</span> y se podrá conectar bajo el id de usuario <b>$user_id</b> al nodo $nodo_name hasta el <b>$user_vigencia</b>, 
                si es de su deseo cambiar esta caducidad, favor de comunicármelo a la dirección electrónica que aparece más abajo en este mensaje. $resp_docker_string <br><br>
                Hemos enviado un correo a la/el usuario con las instrucciones de la conexión y sus credenciales en su dirección $user_mail,
                así como el <b>"Manual de usuarios de la Infraestructura de supercómputo del INMEGEN"</b>
                que puede ver haciendo click <a target="_blank" href="https://supercomputo.inmegen.gob.mx/docs/manualSupercomputo">aquí</a>. <br>
              </p>

              <p>
             Para cualquier duda o aclaración, no dude en escribirnos a la dirección que aparece más abajo.<br>
             <span class="warning">
                Por favor, no conteste a este mail ya que es enviado de forma automática y no es revisado regularmente. 
              </span>
              </p>

              

               <p class="grey_text">
                Atentamente<br>
                Walter Santos<br>
                Jefatura de Supercómputo<br>
                Subdirección de Bioinformática<br>
                <a href="mailto:whernandez@inmegen.gob.mx">whernandez@inmegen.gob.mx</a><br>
                55 5350 1900 ext. 1951<br><br>
                <img style="width: 120px" src="http://supercomputo.inmegen.gob.mx/assets/images/logo_small.png">
               </p>
               
                
              </div>

              
              <p class="note">
                AVISO DE CONFIDENCIALIDAD: De conformidad con lo establecido en el inciso a del artículo 57 del “Acuerdo por el que se
                emiten las políticas y disposiciones para impulsar el uso y aprovechamiento de la informática, el gobierno digital, las
                tecnologías de la información y comunicación, y la seguridad de la información en la Administración Pública Federal”, se
                informa que este mensaje y los datos adjuntos son para uso exclusivo de la persona o entidad a la que expresamente ha
                sido enviada, el cual puede contener información que por su naturaleza deba ser considerada como CONFIDENCIAL o
                RESERVADA, en términos de lo dispuesto por las Leyes General y Federal de Transparencia y Acceso a la Información
                Pública y General de Protección de Datos Personales en Posesión de Sujetos Obligados. Si por error ha recibido esta
                comunicación: 1) queda estrictamente prohibido la revelación, retransmisión, difusión, copia, impresión, modificación,
                alteración en toda o en alguna de sus partes o el uso de la información contenida en el archivo electrónico sea diverso
                al objeto por el cual se emitió; 2) notifíquelo al remitente; y 3) bórrelo de inmediato y en forma permanente, junto con
                cualquier copia digital o impresa, así como cualquier archivo anexo al mismo..<br><br>
              </p>
            </div>

          </div>
          <!-- <div class="grey_text">
            <p>
              &copy; 2024 Instituto Nacional de Medicina Genómica (INMEGEN)  <br><br>


              Periferico Sur 4809, Arenal Tepepan, Tlalpan, 14610 Ciudad de México, CDMX <br>
            </p>
            <div class="social-logos">
              <ul>
                <li>
                  <a href="https://www.facebook.com/biotecmov"><ion-icon name="logo-facebook"
                      class="social"></ion-icon></a>
                </li>
                <li>
                  <a href="https://instagram.com/biotecmov"><ion-icon name="logo-instagram"
                      class="social"></ion-icon></a>
                </li>
                <li>
                  <a href="https://twitter.com/ibt_unam"><ion-icon name="logo-twitter" class="social"></ion-icon></a>
                </li>

              </ul>
            </div>
            <div class="hgv">
              <p>
                Diseño / tecnología by Walter Santos (webmaster)</a>
              </p>
            </div>
          </div> -->
        </td>
      </tr>
    </table>
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
  </body>

</html>
EOT; 

return $mail_usuario;
}//para telegram

  private function sendTelegram($message){ //mandar mensaje de telegram
    global $errors;
    global $email;

    $message .= " $email";

    $token = "8116994696:AAEUFcnQ6eRucLWmGJbVbD8WceHqwapLy8I"; //token
    // $chat_id = "1374656926";
    $chat_id = "-4556355731";
    $url = "https://api.telegram.org/bot$token/sendMessage"; //telegram
    $data = [
      'chat_id' => $chat_id,
      'text' => $message,
      'parse_mode' => 'Markdown'  // Opcional: puedes usar Markdown o HTML
    ];
    // Usamos cURL para enviar el mensaje
    //cURL es una librería que permite a PHP "navegar" por internet y comunicarse con otros servidores.
    $ch = curl_init(); //se abre la comunicacion 
    curl_setopt($ch, CURLOPT_URL, $url); //direccion para enviar la info
    curl_setopt($ch, CURLOPT_POST, true);// peticion POST, enviar datos
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Configura cURL para que, cuando el servidor responda, guarde esa respuesta en una variable en lugar de mostrarla directamente en la pantalla.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//Aquí es donde se adjuntan los datos que definimos 

    $response = curl_exec($ch); //es el boton de "enviar"
    curl_close($ch); //se cierra la conexion 
  }

  private function enviamail($asunto,$modo, $ofensor, $this_ofensor_str, $max_processors, $host, $nombre, $email){ //datos del mail
  global $errors;
  $to = "whernandez@inmegen.gob.mx"; //correo
  if ($email != "" and $email != "whernandez@inmegen.gob"){ //email 
    $to .= ", $email";
  }

  $msj1 = $msj2 = $msj_titulo = "";
  $x = 0;

  $subject = $asunto; //asunto
  if ($modo == "stop"){//si está en modo stop
    $msj_titulo = "Proceso detenido"; //mensaje
    $msj1 = "Apreciable usuario o usuaria <b>$nombre</b> ($ofensor). <br> Los sistemas de vigilancia de la infraestructura Fenix han DETENIDO que tu(s) proceso(s): <br><br>"; //mensaje1
    $msj2 = "Por utilizar más de los $max_processors nodos por proceso recomendados en el nodo $host por más de 3 horas.
    
    Te recomiendo mandar tu proceso nuevamente con los ajustes necesarios para que no sea detenido nuevamente. Si tienes dudas, por favor no dudes en comunicarte. <br><br><br>";//mensaje 2
  }
  elseif ($modo == "append"){ //¿añadir?
    $msj_titulo = "Notificación automática de uso desmedido de recursos de procesamiento"; //titulo del mensaje
    $msj1 = "Apreciable usuario o usuaria  <b>$nombre</b> ($ofensor) <br> Los sistemas de vigilancia de la infraestructura Fenix han detectado que tu(s) proceso(s): <br><br>";
    $msj2 = "Está(n) utilizando más de los $max_processors nodos por proceso recomendados en el nodo $host. Si este es un consumo temporal, no te preocupes, es aceptable. <br><br>
    Sin embargo, de continuar así por más de 3 horas será(n) detenido automáticamente (se te notificará de ello). <br><br><br>"; //msg2
  }
  else{
    $mensage = "erorr: $host"; //mensaje de error
    $to = "whernandez@inmegen.gob.mx";//el mensaje va para:
    $x = 1;
  }

  $ruta = __DIR__ . '/../../../../storage/data/mail_template_styles/mail_class.css'; //esto es para el formato?
  $contenido = "";
  if (file_exists($ruta)) {
    $contenido = file_get_contents($ruta);
  } 
  else {
    $contenido = ""; //contenido vacio
  }
  //contenido del mensaje
  $mensage = '<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap"
      rel="stylesheet">
    <!-- <link rel="stylesheet" href="/alertas/template/new_alert_style.css"> -->

    <title>Aviso de caducidad de cuentas en sistemas de supercómputo</title>

    <style type="text/css">'.$contenido.'</style>
  </head>

  <body>

    <table  cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div class="head">
           
            

            </div>
            <div class="mail-body">
              <div class="greycard">
                <p class="head1">'.$msj_titulo.'</p>
                <div class="card_body">
                  <p>'.$msj1.'</p>
                  
                  <span class="destacado">'.$this_ofensor_str.'</span>
                  <p>'.$msj2.'
                    Si tienes alguna duda adicional, no dudes en contactarme en la dirección electrónica que aparece más abajo. <br><br>
                    <span class="warning">Por favor, no contestes a este correo ya que fue enviado de forma automática desde una dirección
                      que no es revisada regularmente. </span><br><br>
                  </p>
                  
                  <p class="grey_text">
                    Atentamente<br>
                    Walter Santos<br>
                    Jefatura de Supercómputo<br>
                    Subdirección de Bioinformática<br>
                    <a href="mailto:whernandez@inmegen.gob.mx">whernandez@inmegen.gob.mx</a><br>
                    55 5350 1900 ext. 1951<br><br>
                    <img style="width: 120px" src="http://supercomputo.inmegen.gob.mx/assets/images/logo_small.png">
                  </p>
                </div>
                
              </div>

              
              <p class="note">
                AVISO DE CONFIDENCIALIDAD: De conformidad con lo establecido en el inciso a del artículo 57 del “Acuerdo por el que se
                emiten las políticas y disposiciones para impulsar el uso y aprovechamiento de la informática, el gobierno digital, las
                tecnologías de la información y comunicación, y la seguridad de la información en la Administración Pública Federal”, se
                informa que este mensaje y los datos adjuntos son para uso exclusivo de la persona o entidad a la que expresamente ha
                sido enviada, el cual puede contener información que por su naturaleza deba ser considerada como CONFIDENCIAL o
                RESERVADA, en términos de lo dispuesto por las Leyes General y Federal de Transparencia y Acceso a la Información
                Pública y General de Protección de Datos Personales en Posesión de Sujetos Obligados. Si por error ha recibido esta
                comunicación: 1) queda estrictamente prohibido la revelación, retransmisión, difusión, copia, impresión, modificación,
                alteración en toda o en alguna de sus partes o el uso de la información contenida en el archivo electrónico sea diverso
                al objeto por el cual se emitió; 2) notifíquelo al remitente; y 3) bórrelo de inmediato y en forma permanente, junto con
                cualquier copia digital o impresa, así como cualquier archivo anexo al mismo.<br><br>
              </p>
            </div>

          </div>
        </td>
      </tr>
    </table>
  </body>

</html>';
  
  
  
  $headers = "From:  supercomputo@inmegen.edu.mx\r\n"; //de quien
  $headers.= "Content-type: text/html\r\n"; //contenido
  if (!mail($to, $subject, $mensage, $headers)){
    $errors.= "No se pudo enviar el mail al usuario $ofensor. Aguas"; //en caso de error e el envio 
  }
  }
}

