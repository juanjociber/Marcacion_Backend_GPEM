<?php 
    // Configura las cabeceras HTTP para permitir el acceso desde cualquier origen (CORS)
    header("Access-Control-Allow-Origin: *");

    // Permite que las siguientes cabeceras sean usadas en la solicitud HTTP
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Authorization, Content-Type, Accept");

    // Permite que solo se use el método POST en las solicitudes HTTP
    header("Access-Control-Allow-Methods: POST");

    // Especifica que el contenido de la respuesta será en formato JSON
    header("Content-Type: application/json");

    // Si la solicitud es de tipo OPTIONS, envía un código de respuesta 200 y termina la ejecución
    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {    
        http_response_code(200);
        exit();
    }

    // Incluye el archivo de conexión a la base de datos
    include($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGpemDb.php');

    // Inicializa un array vacío para almacenar los datos de la respuesta
    $data   =   array();
    
    // Inicializa la variable de resultado como false
    $res    =   false;
    
    // Mensaje por defecto en caso de error
    $msg    =   'Error consultado el DNI.';

    // Verifica si se ha enviado un DNI en la solicitud POST
    if(!empty($_POST['dni'])){
        // Establece el modo de error de PDO a excepción
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara una consulta SQL para buscar los datos del personal por DNI
        $stmt   = $conmy->prepare("SELECT p.pers_codigo, p.pers_nombres, p.pers_apellidos, p.pers_dni, c.cargo 
                                FROM tblpersonal p 
                                INNER JOIN rh_cargos c ON p.idcargo = c.idcargo 
                                WHERE p.pers_dni = :Dni AND p.pers_estado = 1;");

        // Ejecuta la consulta con el DNI proporcionado
        $stmt->execute(array(':Dni' => $_POST['dni']));

        // Recupera la fila resultante de la consulta
        $row    = $stmt->fetch();

        // Si se encuentra una fila, asigna los datos al array y actualiza las variables de resultado y mensaje
        if($row){
            $data['id']         = $row['pers_codigo'];
            $data['nombres']    = $row['pers_nombres'];
            $data['apellidos']  = $row['pers_apellidos'];
            $data['dni']        = $row['pers_dni'];
            $data['cargo']      = $row['cargo'];

            $res = true;
            $msg = "Ok.";
        } else {
            // Si no se encuentra ninguna fila, actualiza el mensaje de error
            $msg = "No se encontró resultados.";
        }
        
        // Libera la sentencia
        $stmt = null;
    } else {
        // Si no se ha enviado el DNI, actualiza el mensaje de error
        $msg = 'La información esta incompleta.';
    }    

    // Codifica la respuesta en formato JSON y la envía al cliente
    echo json_encode(array('res' => $res, 'data' => $data, 'msg' => $msg));
?>
