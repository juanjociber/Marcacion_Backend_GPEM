<?php 
    function FnRegistrarAsistencia($conmy, $eveid, $perid, $latitud, $longitud, $direccion) {
        try {
            $stmt = $conmy->prepare("CALL spghu_registrarasistencia(:_eveid, :_perid, :_latitud, :_longitud, :_direccion, @_retorno)");
            $stmt->bindParam(':_eveid', $eveid, PDO::PARAM_INT);
            $stmt->bindParam(':_perid', $perid, PDO::PARAM_INT);
            $stmt->bindParam(':_latitud', $latitud, PDO::PARAM_STR);
            $stmt->bindParam(':_longitud', $longitud, PDO::PARAM_STR);
            $stmt->bindParam(':_direccion', $direccion, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $conmy->query("SELECT @_retorno as retorno");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $retorno = $row['retorno'];           

            return $retorno;
            
        } catch (PDOException $e) {
            throw new Exception("Error en la Marcación: ".$e->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }

    function FnRegistrarRefrigerio($conmy, $eveid, $perid) {
        try {
            $stmt = $conmy->prepare("CALL spghu_registrarrefrigerio(:_eveid, :_perid, @_retorno)");
            $stmt->bindParam(':_eveid', $eveid, PDO::PARAM_INT);
            $stmt->bindParam(':_perid', $perid, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conmy->query("SELECT @_retorno as retorno");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $retorno = $row['retorno'];           

            return $retorno;
            
        } catch (PDOException $e) {
            throw new Exception("Error en la Marcación: ".$e->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }


    // Buscar la última marcacion en proceso, puede ser proceso o cerrada
    function FnBuscarUltimaMarcacion($conmy, $perid) {
        try {
            $stmt = $conmy->prepare("select idmarcacion, fecha, turnofinal, programacion1, programacion2, asistencia1, asistencia2, refrigerio1, refrigerio2, tecnicosino from rh_marcaciones where idpersonal=:PerId and tecnicosino>0 order by fecha desc limit 1;");    
            $stmt->execute(array(':PerId'=>$perid));
            $marcacion = new stdClass();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $marcacion->id = $row['idmarcacion'];
                $marcacion->fecha = $row['fecha'];
                $marcacion->turno = $row['turnofinal'];
                $marcacion->programacion1 = $row['programacion1'];
                $marcacion->programacion2 = $row['programacion2'];
                $marcacion->asistencia1 = $row['asistencia1'];
                $marcacion->asistencia2 = $row['asistencia2'];
                $marcacion->refrigerio1 = $row['refrigerio1'];
                $marcacion->refrigerio2 = $row['refrigerio2'];
                $marcacion->tecnicosino = $row['tecnicosino'];
            }
            return $marcacion;
        } catch (PDOException $e) {
            throw new Exception("Error en la Marcación: ".$e->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }

    // Buscar la última programacion abierta estado:0
    function FnBuscarProgramacionAbierta($conmy, $perid) {
        try {
            $stmt = $conmy->prepare("select idmarcacion, fecha, turnofinal, programacion1, programacion2, asistencia1, asistencia2, refrigerio1, refrigerio2, tecnicosino from rh_marcaciones where idpersonal=:PerId and fecha=:Fecha and tecnicosino=0 limit 1;");    
            $stmt->execute(array(':PerId'=>$perid, ':Fecha'=>date('Y-m-d')));
            $programacion = new stdClass();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $programacion->id = $row['idmarcacion'];
                $programacion->fecha = $row['fecha'];
                $programacion->turno = $row['turnofinal'];
                $programacion->programacion1 = $row['programacion1'];
                $programacion->programacion2 = $row['programacion2'];
                $programacion->asistencia1 = $row['asistencia1'];
                $programacion->asistencia2 = $row['asistencia2'];
                $programacion->refrigerio1 = $row['refrigerio1'];
                $programacion->refrigerio2 = $row['refrigerio2'];
                $programacion->tecnicosino = $row['tecnicosino'];
            }
            return $programacion;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }

    // Buscar la última programacion abierta estado:0
    function FnBuscarPersonal($conmy, $dni) {
        try {
            $stmt = $conmy->prepare("select p.pers_codigo, p.pers_nombres, p.pers_apellidos, p.pers_dni, c.cargo from tblpersonal p inner join rh_cargos c on p.idcargo=c.idcargo where p.pers_dni=:Dni and p.pers_estado=1;");    
            $stmt->execute(array(':Dni'=>$dni));
            $personal = new stdClass();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $personal->id = $row['pers_codigo'];
                $personal->nombres = $row['pers_nombres'];
                $personal->apellidos = $row['pers_apellidos'];
                $personal->dni = $row['pers_dni'];
                $personal->cargo = $row['cargo'];
            }
            return $personal;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }

?>