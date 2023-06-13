<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
class muttusApiModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function consultaMundoTerra($numCliente){
        try {
            $bdProventel = $this->load->database('default', TRUE);
            $query="SELECT servicio, tipo, nombre, aPaterno, aMaterno, localidad, estado, fechaNac, numCliente, iniVig, finVig 
            FROM clientes_mundo_terra WHERE numCliente= '{$numCliente}'";
            $data = $bdProventel->query($query);
            $dataServicio= $data->result_array();
            if(is_array($dataServicio) and count($dataServicio)>0){

                $resp = array('status' => 1, 'msg'=>'Consulta correcta', 'response' => $dataServicio[0]);
                return  $resp;
            }else{
                $updateData = array("numCliente" => $numCliente);
                $resp = array('status' => 0,  'msg'=>'No existe el cliente',  'response' => $updateData);
                return  $resp;
            }
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }
    
    public function insertarCliente($dataInsert){
        try {
            $id_member=$dataInsert['id_member'];
            $bdProventel = $this->load->database('default', TRUE);
            $query="SELECT * FROM clientes_muttus WHERE id_member= '{$id_member}'";
            $data = $bdProventel->query($query);
            $dataServicio= $data->result_array();
            $response= $dataInsert;
            if(is_array($dataServicio) and count($dataServicio)>0){
                $resp = array('status' => 0, 'msg'=>'Cliente ya existe con ese numero de id_member', 'response' =>$id_member);
                return  $resp;
            }else{
                //Crear un registro con la fecha en formato YYYY/MM/DD
                $sql = "INSERT INTO clientes_muttus (id_member, nombre, aPaterno, aMaterno, correo, contrasenia, fecha_nacimiento, fecha_de_alta, fecha_de_baja, estatus) VALUES (?,?,?,?,?,?,?,?,?,?)";
                $values = $dataInsert;

                $bdProventel->query($sql, $values);

                // $response= $bdProventel->insert('clientes_mundo_terra', $dataInsert);

                /* $bdProventel->set('fechaAlta', '09/03/2022');
                $bdProventel->where('numCuenta', $numCuenta);
                $bdProventel->update('clientesHolding');*/
                $resp = array('status' => 1,  'msg'=>'Registros exitoso',  'response' => $dataInsert);
                return  $resp;
            }
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }

    public function actualizaCliente($dataUpdate){
        try {
            $id_member=$dataUpdate['id_member'];
            $bdProventel = $this->load->database('default', TRUE);
            $query="SELECT * FROM clientes_muttus WHERE id_member= '{$id_member}'";
            $data = $bdProventel->query($query);
            $dataServicio= $data->result_array();
            if(is_array($dataServicio) and count($dataServicio)>0){
                $updateData = array("id_member" => $id_member);
                if(!empty($dataUpdate['contrasenia']) || !isset($dataUpdate['contrasenia'])){
                        $datos = array(
                            'nombre' => $dataUpdate['nombre'],
                            'aPaterno' => $dataUpdate['aPaterno'],
                            'aMaterno' => $dataUpdate['aMaterno'],
                            'contrasenia' => password_hash($dataUpdate["contrasenia"], PASSWORD_DEFAULT),
                            'fecha_nacimiento' => $dataUpdate['fecha_nacimiento'],
                            'fecha_de_alta' => $dataUpdate['fecha_de_alta'],
                            'fecha_de_baja' => $dataUpdate['fecha_de_baja'],
                            'estatus' => $dataUpdate['estatus'],
                        );
                    } else {
                        $datos = array(
                            'nombre' => $dataUpdate['nombre'],
                            'aPaterno' => $dataUpdate['aPaterno'],
                            'aMaterno' => $dataUpdate['aMaterno'],
                            'fecha_nacimiento' => $dataUpdate['fecha_nacimiento'],
                            'fecha_de_alta' => $dataUpdate['fecha_de_alta'],
                            'fecha_de_baja' => $dataUpdate['fecha_de_baja'],
                            'estatus' => $dataUpdate['estatus'],
                        );
                    }
                $bdProventel->where('id_member', $id_member);
                // $bdProventel->set($datos);
                $bdProventel->update('clientes_muttus', $datos);
                $resp = array('status' => 1,  'msg'=>'Actualización exitosa',  'response' => $updateData);
                return  $resp;
            }else{
                $updateData = array("id_member" => $id_member);
                $resp = array('status' => 1,  'msg'=>'Cliente no existe',  'response' => $updateData);
                return  $resp;
            }
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }

    public function bajaCliente($numCliente){
        try {
            $bdProventel = $this->load->database('default', TRUE);
            $query="SELECT * FROM clientes_mundo_terra WHERE numCliente= '{$numCliente}'";
            $data = $bdProventel->query($query);
            $dataServicio= $data->result_array();
            if(is_array($dataServicio) and count($dataServicio)>0){
                $today = date('d-m-Y');
                // $bdProventel->set('status', 0);
                // $bdProventel->set('fechaCancelado', $today);
                // $bdProventel->set('fechaActualizacion', $today);

                $bdProventel->where('numCliente', $numCliente);
                $bdProventel->delete('clientes_mundo_terra');
                // $bdProventel->update('clientes_mundo_terra');
                // $updateData = array("numCliente" => $numCliente, "fechaCancelado" => $today);
                $updateData = array("numCliente" => $numCliente);
                $resp = array('status' => 1,  'msg'=>'Cancelación exitosa',  'response' => $updateData);
                return  $resp;
            }else{
            	$query="SELECT * FROM clientes_mundo_terra WHERE numCliente= '{$numCliente}'";
            	$data = $bdProventel->query($query);
            	$dataServicio= $data->result_array();
            	if (is_array($dataServicio) and count($dataServicio)>0) {
            		$updateData = array("numCliente" => $numCliente, "fechaCancelado" => $dataServicio[0]['fechaCancelado']);
                	$resp = array('status' => 0, 'msg'=>'Cliente cancelado con anterioridad', 'response' => $updateData);
                	return  $resp;		
            	}else{
            		$resp = array('status' => 0, 'msg'=>'Cliente no existe', 'response' => $numCliente);
                	return  $resp;
            	}
                
            }
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }

    public function statusCliente($dataUpdate){
        try {
            $numEmpleado=$dataUpdate['numEmpleado'];//arreglo de numEmpleadoS a actualizar
            $status = $dataUpdate['status'];//status a cambiar
            $activos = array();
            $cancelados = array();            
            $suspendidos = array();            
            $inexistentes = array();  
            $yaActivos = array();
            $yaSuspendidos = array();          
            //$numEmpleadoEXITOSOS = 0;
            //$numEmpleadoINEXISTENTES = 0;
            //$numEmpleadoCANCELADOS = 0;
            //$numEmpleadoSUSPENDIDOS = 0;

            $bdProventel = $this->load->database('default', TRUE);
            foreach ($numEmpleado as $key => $value) {
                $query="SELECT numEmpleado, status FROM clientesHolding WHERE numEmpleado= '{$value}'";
                $data = $bdProventel->query($query);
                $dataServicio= $data->result_array();
                if(is_array($dataServicio) and count($dataServicio)>0){
                    if ($dataServicio[0]['status'] == 1) {
                        if ($status == 1) {
                            array_push($yaActivos, $value);
                        }
                        if ($status == 2) {
                        $today = date('d-m-Y');
                        $bdProventel->set('status', 2);
                        $bdProventel->set('fechaActualizacion', $today);
                        $bdProventel->where('numEmpleado', $value);
                        $bdProventel->update('clientesHolding');
                        array_push($suspendidos, $value);
                        }
                    }
                    if ($dataServicio[0]['status'] == 2) {
                        if ($status == 2) {
                            array_push($yaSuspendidos, $value);
                        }
                        if ($status == 1) {
                        $today = date('d-m-Y');
                        $bdProventel->set('status', 1);
                        $bdProventel->set('fechaActualizacion', $today);
                        $bdProventel->where('numEmpleado', $value);
                        $bdProventel->update('clientesHolding');
                        array_push($activos, $value);
                        }   
                    }
                    if ($dataServicio[0]['status'] == 0) {
                        array_push($cancelados, $value);
                    }
            }else{
                array_push($inexistentes, $value);
            }

            /*
            
            }else{
                $query="SELECT * FROM clientesHolding WHERE numEmpleado= '{$numEmpleado}' and status=0";
                $data = $bdProventel->query($query);
                $dataServicio= $data->result_array();
                if (is_array($dataServicio) and count($dataServicio)>0) {
                    $updateData = array("numEmpleado" => $numEmpleado, "fechaCancelado" => $dataServicio[0]['fechaCancelado']);
                    $resp = array('status' => 0, 'msg'=>'Cliente cancelado con anterioridad', 'response' => $updateData);
                    return  $resp;      
                }else{
                    $resp = array('status' => 0, 'msg'=>'Cliente no existe', 'response' => $numEmpleado);
                    return  $resp;
                }
                
            }*/
            }
            if ($status == 1) {
                $msg = ["numEmpleado ya activos:"=>$yaActivos,"numEmpleado activados:"=>$activos,"numEmpleado cancelados:"=>$cancelados,"numEmpleado inexistentes:"=>$inexistentes];
            }
            if ($status == 2) {
                $msg = ["numEmpleado ya suspendidos:"=>$yaSuspendidos,"numEmpleado suspendidos:"=>$suspendidos,"numEmpleado cancelados:"=>$cancelados,"numEmpleado inexistentes:"=>$inexistentes];
            }
                $resp = array('status' => 1, 'msg'=>'PRUEBA DE DATOS', 'response' => $msg);
                    return  $resp;
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }

    public function loginCliente($numCliente, $fechaNac){
        try {
            $bdProventel = $this->load->database('default', TRUE);
            $query="SELECT * FROM clientes_mundo_terra WHERE numCliente= '{$numCliente}' AND fechaNac = '{$fechaNac}'";
            $data = $bdProventel->query($query);
            $dataServicio= $data->result_array();
            if(is_array($dataServicio) and count($dataServicio)>0){

                $resp = array('status' => 1, 'msg'=>'Cliente encontrado', 'response' => $dataServicio[0]);
                return  $resp;
            }else{
                $updateData = array("numCliente" => $numCliente);
                $resp = array('status' => 0,  'msg'=>'No existe el cliente',  'response' => $updateData);
                return  $resp;
            }
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }

    public function numCliente($numCliente, $fechaNac){
        try {
            $bdProventel = $this->load->database('default', TRUE);
            $query="SELECT * FROM clientes_mundo_terra WHERE numCliente= '{$numCliente}' AND fechaNac = '{$fechaNac}'";
            $data = $bdProventel->query($query);
            $dataServicio= $data->result_array();
            if(is_array($dataServicio) and count($dataServicio)>0){

                $resp = array('status' => 1, 'msg'=>'numCliente ENCONTRADO', 'response' => $dataServicio[0]);
                return  $resp;
            }else{
                // $updateData = array("numCliente" => $numCliente);
                $dataServicio['id'] = null;
                $dataServicio['nombre'] = null;
                $dataServicio['numCliente'] = null;
                $dataServicio['fechaNac'] = '0000-00-00 00:00:00.000';
                $resp = array('status' => 0,  'msg'=>'No existe el cliente',  'response' => $dataServicio);
                return  $resp;
            }
        } catch (Exception $ex) {
            $resp = array('status' => 0, 'msg'=>'Error en la consulta', 'response' => "");
            return $resp;
        }
    }
}
