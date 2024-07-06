<?php 
    header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Authorization, Content-Type, Accept");
    header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {    
		http_response_code(200);
		exit();
	}

	$data=array();
	$res=false;
	$msg='Error consultado el DNI.';
    $fechaActual = new DateTime();//$hora=(new DateTime())->format('Y-m-d H:i:s');
    $hora=$fechaActual->format('Y-m-d H:i:s');
    $data=array(
        'personal'=>array(
            'id'=>0,
            'nombres'=>'',
            'apellidos'=>'',
            'dni'=>'',
            'cargo'=>''
        ), 'marcacion'=>array(
            'id'=>0,
            'fecha'=>'',
            'turno'=>'',
            'programacion1'=>'',
            'programacion2'=>'',
            'asistencia1'=>'',
            'asistencia2'=>'',
            'refrigerio1'=>'',
            'refrigerio2'=>'',
            'tecnico'=>0
        ), 'programacion'=>array(
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
        ));

    include($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGpemDb.php');
    require_once 'MarcacionApi.php';

    try {
        if(empty($_POST['dni'])){throw new Exception("No se reconoce el DNI.");}

        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $personal = FnBuscarPersonal($conmy, $_POST['dni']);
        if(empty($personal->id)) {throw new Exception('No se encontró el DNI.');}

        $data['personal']['id']=(int)$personal->id;
        $data['personal']['nombres']=$personal->nombres;
        $data['personal']['apellidos']=$personal->apellidos;
        $data['personal']['dni']=$personal->dni;
        $data['personal']['cargo']=$personal->cargo;

        $marcacion=FnBuscarUltimaMarcacion($conmy, $personal->id);
        if(!empty($marcacion->id)){
            $data['marcacion']['id']=(int)$marcacion->id;
            $data['marcacion']['fecha']=$marcacion->fecha;
            $data['marcacion']['turno']=$marcacion->turno;
            $data['marcacion']['programacion1']=$marcacion->programacion1;
            $data['marcacion']['programacion2']=$marcacion->programacion2;
            $data['marcacion']['asistencia1']=$marcacion->asistencia1;
            $data['marcacion']['asistencia2']=$marcacion->asistencia2;
            $data['marcacion']['refrigerio1']=$marcacion->refrigerio1;
            $data['marcacion']['refrigerio2']=$marcacion->refrigerio2;
            $data['marcacion']['tecnicosino']=(int)$marcacion->tecnicosino;

            if($marcacion->tecnicosino==2){
                $programacion=FnBuscarProgramacionAbierta($conmy, $personal->id);
                if(!empty($programacion->id)){
                    $data['programacion']['id']=(int)$programacion->id;
                    $data['programacion']['fecha']=$programacion->fecha;
                    $data['programacion']['turno']=$programacion->turno;
                    $data['programacion']['programacion1']=$programacion->programacion1;
                    $data['programacion']['programacion2']=$programacion->programacion2;
                    $data['programacion']['asistencia1']=$programacion->asistencia1;
                    $data['programacion']['asistencia2']=$programacion->asistencia2;
                    $data['programacion']['refrigerio1']=$programacion->refrigerio1;
                    $data['programacion']['refrigerio2']=$programacion->refrigerio2;
                    $data['programacion']['tecnicosino']=(int)$programacion->tecnicosino;
                }else{
                    $data['programacion']['id']=(int)$marcacion->id;
                    $data['programacion']['fecha']=$marcacion->fecha;
                    $data['programacion']['turno']=$marcacion->turno;
                    $data['programacion']['programacion1']=$marcacion->programacion1;
                    $data['programacion']['programacion2']=$marcacion->programacion2;
                    $data['programacion']['asistencia1']=$marcacion->asistencia1;
                    $data['programacion']['asistencia2']=$marcacion->asistencia2;
                    $data['programacion']['refrigerio1']=$marcacion->refrigerio1;
                    $data['programacion']['refrigerio2']=$marcacion->refrigerio2;
                    $data['programacion']['tecnicosino']=(int)$marcacion->tecnicosino;
                }                
            }else{
                $data['programacion']['id']=(int)$marcacion->id;
                $data['programacion']['fecha']=$marcacion->fecha;
                $data['programacion']['turno']=$marcacion->turno;
                $data['programacion']['programacion1']=$marcacion->programacion1;
                $data['programacion']['programacion2']=$marcacion->programacion2;
                $data['programacion']['asistencia1']=$marcacion->asistencia1;
                $data['programacion']['asistencia2']=$marcacion->asistencia2;
                $data['programacion']['refrigerio1']=$marcacion->refrigerio1;
                $data['programacion']['refrigerio2']=$marcacion->refrigerio2;
                $data['programacion']['tecnicosino']=(int)$marcacion->tecnicosino;
            }
        }

        $res = true;
        $msg="Ok.";

    } catch(PDOException $e){
        $msg=$e->getMessage();
    } catch (Exception $ex) {
        $msg=$ex->getMessage();
    }

    echo json_encode(array('res'=>$res, 'hora'=>$hora, 'data'=>$data, 'msg'=>$msg));


?>