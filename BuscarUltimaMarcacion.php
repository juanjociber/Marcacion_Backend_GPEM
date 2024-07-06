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
	$msg='Error consultado la Marcación.';
    $data=array(
            'id'=>0,
            'fecha'=>'',
            'turno'=>'',
            'programacion1'=>'',
            'programacion2'=>'',
            'asistencia1'=>'',
            'asistencia2'=>'',
            'refrigerio1'=>'',
            'refrigerio2'=>'',
            'tecnicosino'=>0
        );

    include($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGpemDb.php');
    require_once 'MarcacionApi.php';

    try {
        if(empty($_POST['id'])){throw new Exception("No se reconoce el ID del Personal.");}

        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $marcacion=FnBuscarUltimaMarcacion($conmy, $_POST['id']);
        if(!empty($marcacion->id)){
            $data['id']=(int)$marcacion->id;
            $data['fecha']=$marcacion->fecha;
            $data['turno']=$marcacion->turno;
            $data['programacion1']=$marcacion->programacion1;
            $data['programacion2']=$marcacion->programacion2;
            $data['asistencia1']=$marcacion->asistencia1;
            $data['asistencia2']=$marcacion->asistencia2;
            $data['refrigerio1']=$marcacion->refrigerio1;
            $data['refrigerio2']=$marcacion->refrigerio2;
            $data['tecnicosino']=(int)$marcacion->tecnicosino;
            $res = true;
            $msg="Ok.";
        }else{
            $msg='No se encontró resultados.';
        }
    } catch(PDOException $e){
        $msg=$e->getMessage();
    } catch (Exception $ex) {
        $msg=$ex->getMessage();
    }

    echo json_encode(array('res'=>$res, 'data'=>$data, 'msg'=>$msg));
?>