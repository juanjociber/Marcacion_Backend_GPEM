<?php 
    header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Authorization, Content-Type, Accept");
    header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {    
		http_response_code(200);
		exit();
	}

	$res=false;
	$msg='Error registrando la Marcación.';

    include($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGpemDb.php');
    require_once 'MarcacionApi.php';

    try {
        if(empty($_POST['tipo']) || empty($_POST['forma'])){throw new Exception("Tipo de marcación desconocida.");}
        if(empty($_POST['id'])){throw new Exception("La información del Personal esta incompleta.");}
        if(empty($_POST['latitud']) || empty($_POST['longitud']) || empty($_POST['direccion'])){throw new Exception("Las datos de ubicación estan incompletos.");} 
        
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if($_POST['tipo']=='asistencia'){
            if($_POST['forma']=='ingreso'){
                $resultado = FnRegistrarAsistencia($conmy, 1, $_POST['id'], $_POST['latitud'], $_POST['longitud'], $_POST['direccion']);
            }else if($_POST['forma']=='salida'){
                $resultado = FnRegistrarAsistencia($conmy, 2, $_POST['id'], $_POST['latitud'], $_POST['longitud'], $_POST['direccion']);
            }else{
                throw new Exception("No se reconoce el tipo de Marcación."); 
            }

            if($resultado==1){
                $res=true;
                $msg='Ok.';
            }else{
                throw new Exception("Error registrando la Asistencia.");  
            }
        }else if($_POST['tipo']=='refrigerio'){
            if($_POST['forma']=='ingreso'){
                $resultado = FnRegistrarRefrigerio($conmy, 1, $_POST['id']);
            }else if($_POST['forma']=='salida'){
                $resultado = FnRegistrarRefrigerio($conmy, 2, $_POST['id']);
            }else{
                throw new Exception("No se reconoce el tipo de Marcación."); 
            }

            if($resultado==1){
                $res=true;
                $msg='Ok.';
            }else{
                throw new Exception("Error registrando el Refrigerio.");  
            }
        }else{
            throw new Exception("No se reconoce el tipo de Marcación.");            
        }
    } catch(PDOException $ex){
        $msg=$ex->getMessage();
    } catch (Exception $ex) {
        $msg=$ex->getMessage();
    }finally{
        $conmy=null;
    }

    echo json_encode(array('res'=>$res, 'msg'=>$msg));
?>