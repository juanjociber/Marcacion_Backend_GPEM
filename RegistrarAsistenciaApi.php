<?php 
    header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Authorization, Content-Type, Accept");
    header("Access-Control-Allow-Methods: POST");
	header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {    
		http_response_code(200);
		exit();
	}

    include($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGpemDb.php');

	$data=array();
	$res=false;
	$msg='Error registrando la Marcación.';

    try {
        if(empty($_POST['tipo']) || empty($_POST['forma'])){throw new Exception("Tipo de marcación desconocida.");}
        if(empty($_POST['id'])){throw new Exception("La información del Personal esta incompleta.");}
        if(empty($_POST['latitud']) || empty($_POST['longitud']) || empty($_POST['direccion'])){throw new Exception("La informacion de la Ubicación esta incompleta.");} 
        
        if($_POST['tipo']=='asistencia'){
            if($_POST['forma']=='ingreso'){
                $msg='Asistencia Ingreso.';
            }else if($_POST['forma']=='salida'){
                $msg="Asistencia Salida.";
            }else{
                throw new Exception("No se reconoce el tipo de Marcación."); 
            }
        }else if($_POST['tipo']=='refrigerio'){

        }else{
            throw new Exception("No se reconoce el tipo de Marcación.");            
        }        
    } catch (Exception $ex) {
        
    }


    if(!empty($_POST['dni'])){
       
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt=$conmy->prepare("select p.pers_codigo, p.pers_nombres, p.pers_apellidos, p.pers_dni, c.cargo from tblpersonal p inner join rh_cargos c on p.idcargo=c.idcargo where p.pers_dni=:Dni and p.pers_estado=1;");
		$stmt->execute(array(':Dni'=>$_POST['dni']));
        $row=$stmt->fetch();
        if($row){
            $data['id']=$row['pers_codigo'];
            $data['nombres']=$row['pers_nombres'];
            $data['apellidos']=$row['pers_apellidos'];
            $data['dni']=$row['pers_dni'];
            $data['cargo']=$row['cargo'];

            $res = true;
            $msg="Ok.";
        }else{
            $msg = "No se encontró resultados.";
        }
        
        $stmt=null;
    }else{
        $msg='La información esta incompleta.';
    }    

    echo json_encode(array('res'=>$res, 'data'=>$data, 'msg'=>$msg));


?>