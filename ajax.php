<?php
	include_once('../lib/tbs_class.php');
	
	class Ajax {
		private $sql;
		private $datos;		
		
		public function __construct( $ac ) {			
			$this->$ac();
		}		
		private function conectar( $app="VALLE_IMPERIAL" ) {
			$params = parse_ini_file( "../config.ini", true );
			$data = array(
				"host" 	=> $params[$app]['host'],
				"user" 	=> $params[$app]['user'],
				"pwd" 	=> $params[$app]['pwd'],
				"db" 	=> $params[$app]['db']
			);
			$mysqli = new Mysqli( $data['host'], $data['user'], $data['pwd'], $data['db'] );
			return $mysqli;
		}
		private function eliminaGrupo() {
			$idGrupo = $_POST['idGrupo'];
			$json = array( "results" => "" );
			$mysqli = $this->conectar();
			$sql = "DELETE FROM menu_permisos WHERE id_grupo=".$idGrupo;	
			$mysqli->query( $sql ) or die( $mysqli->error );
			$sql = "DELETE FROM usuarios WHERE id_tipo=".$idGrupo;
			$mysqli->query( $sql ) or die( $mysqli->error );
			$sql = "DELETE FROM tipo_usuario WHERE id_grupo=".$idGrupo;
			$result = $mysqli->query( $sql ) or die( $mysqli->error );
			$mysqli->close();
			if( $result ) 
				$json['results'] = "grupoEliminado";
			echo json_encode( $json );
		}
		private function eliminaUsr() {
			$idUsr = $_POST['idUsr'];
			$json = array( "results" => "" );
			$mysqli = $this->conectar();
			$sql = "DELETE FROM usuarios WHERE id_usr=".$idUsr;
			$result = $mysqli->query( $sql ) or die( $mysqli->error );
			$mysqli->close();
			if( $result )
                                $json['results'] = "usrEliminado";
                        echo json_encode( $json );
		}
		private function cambiarPasswd() {
			$data = $_POST['data'];
			$json = array(
				"passwdActualError"  => true,
				"error"  => true,
				"result" => "",
				"sql" => ""
			);
			$mysqli = $this->conectar();
			$sql = "SELECT md5('".$data['passwdActual']."') as passType, pass FROM usuarios WHERE id_usr=".$data['id_usr'];
			//$json['sql'] = $sql;

			$result = $mysqli->query( $sql ) or die( $mysqli->error );
			$obj = $result->fetch_object();
			//$mysqli->close();
			//Validar que sea su passwd Anterior
			if( $obj->passType == $obj->pass ) {
				$json['passwdActualError'] = false;
				$json['error'] = false;
				$sql = "UPDATE usuarios SET pass=md5('".$data['nuevoPasswd']."') WHERE id_usr=".$data['id_usr'];
				$result = $mysqli->query( $sql ) or die( $mysqli->error );
				( $result ) ? $json['result']="Password Actualizado" : null;
				//$json['result'] = $sql;
			} 
			//$json['result'] = $sql;
			$mysqli->close();
			echo json_encode( $json );
		}
		private function obtReporte() {
			$data = $_POST['data'];
			$where = "";
			$fech = false;
			$preg = false; 
			$secc = false;
			$tbs = new clsTinyButStrong;
			$mysqli = $this->conectar();
			
			if( !empty( $data['fechaDe'] ) && !empty( $data['fechaA'] ) ) {
				$fech = true;
				//$where .= " AND DATE( registro_encuesta.FECHA ) BETWEEN '".$data['fechaDe']."' AND '".$data['fechaA']."'";
				$fecha = " AND DATE( FECHA ) BETWEEN '".$data['fechaDe']."' AND '".$data['fechaA']."'";
			} 
			if( is_array( $data['seccion'] ) && count( $data['seccion'] ) > 0 ) {
				$secc = true;
				$data['seccion'] = array_map( utf8_decode, $data['seccion'] );
				$seccion = "'" . implode( "', '", $data['seccion'] ) . "'";
				$seccion = " AND E.SECCION IN(".$seccion.")";
			}				
			if( is_array( $data['pregunta'] ) && count( $data['pregunta'] ) > 0 ) {
				$preg = true;
				$data['pregunta'] = array_map( utf8_decode, $data['pregunta'] );
				$pregunta = "'" . implode( "', '", $data['pregunta'] ) . "'";
				$pregunta = " AND E.PREGUNTA IN(".$pregunta.")";
			}
			
			$tot = "
				SELECT
					COUNT( registro_encuesta.ID ) AS TOTAL
				FROM registro_encuesta
				INNER JOIN encuesta E ON E.JOIN_ENCUESTA_REGISTRO=registro_encuesta.ID
				WHERE 1";
			$tot .= ( $fech ) ? $fecha : "";
			$tot .= ( $secc ) ? $seccion : "";
			$tot .= ( $preg ) ? $pregunta : "";
			
		
				
			

			$sql = "
				SELECT
					E.SECCION,
				    E.PREGUNTA,
				    ( SELECT
						COUNT( CALIFICACION )
					FROM encuesta
					INNER JOIN registro_encuesta ON registro_encuesta.ID=encuesta.JOIN_ENCUESTA_REGISTRO
					WHERE 1";
			$sql .= ( $fech ) ? $fecha : "";
			$sql .= " AND SECCION=E.SECCION
					  AND PREGUNTA=E.PREGUNTA
					  AND CALIFICACION=1
					GROUP BY SECCION, PREGUNTA ) AS UNO,
					( SELECT
						COUNT( CALIFICACION )
					FROM encuesta
					INNER JOIN registro_encuesta ON registro_encuesta.ID=encuesta.JOIN_ENCUESTA_REGISTRO
					WHERE 1";
			$sql .= ( $fech ) ? $fecha : "";
			$sql .= " AND SECCION=E.SECCION 
					  AND PREGUNTA=E.PREGUNTA
					  AND CALIFICACION=2
					GROUP BY SECCION, PREGUNTA ) AS DOS,
				    ( SELECT
						COUNT( CALIFICACION )
					FROM encuesta
					INNER JOIN registro_encuesta ON registro_encuesta.ID=encuesta.JOIN_ENCUESTA_REGISTRO
					WHERE 1";
			$sql .= ( $fech ) ? $fecha : "";
			$sql .= " AND SECCION=E.SECCION	
					  AND PREGUNTA=E.PREGUNTA
					  AND CALIFICACION=3
					GROUP BY SECCION, PREGUNTA ) AS TRES,
				    ( SELECT
						COUNT( CALIFICACION )
					FROM encuesta
					INNER JOIN registro_encuesta ON registro_encuesta.ID=encuesta.JOIN_ENCUESTA_REGISTRO
					WHERE 1";
			$sql .= ( $fech ) ? $fecha : "";
			$sql .= " AND SECCION=E.SECCION
					  AND PREGUNTA=E.PREGUNTA
					  AND CALIFICACION=4
					GROUP BY SECCION, PREGUNTA ) AS CUATRO
				FROM encuesta E
				INNER JOIN registro_encuesta re ON re.ID=E.JOIN_ENCUESTA_REGISTRO
				WHERE 1";
			$sql .= ( $fech ) ? " AND DATE( re.FECHA ) BETWEEN '".$data['fechaDe']."' AND '".$data['fechaA']."'" : "";
			$sql .= ( $secc ) ? $seccion : "";
			$sql .= ( $preg ) ? $pregunta : "";
			$sql .= "
				GROUP BY E.SECCION, E.PREGUNTA";
			
			
			
			$tbs->LoadTemplate( '../template/filtroReporte.htm' );
			$tbs->MergeBlock( 'total', $mysqli, $tot );
			$tbs->MergeBlock( 'blk', $mysqli, $sql );
			
			//$tbs->MergeBlock( 'prd', $mysqli, $filtro );
			$tbs->Show();
			$mysqli->close();
		}
		
/*		
		private function confirmarEnlance() {
			include_once('../lib/tbs_class.php');
			$tbs = new clsTinyButStrong;

			$tbs->VarRef = array(
				"numEmpleado" => Ajax::obtenerCadena( $_GET['us'], $_GET['k2'], $_GET['iv2'] ),
				"passwd" 	  => Ajax::obtenerCadena( $_GET['pd'], $_GET['k3'], $_GET['iv3'] ),
				"msg"		  => "",
				"color"		  => "color: red",
				"resultado"	  => "",
				"error"		  => false 
			);
		
			if( $tbs->VarRef['numEmpleado'] != "" && $tbs->VarRef['passwd'] ) {				
				$this->conectar();				
				$this->sql = "UPDATE login_vacaciones 
							  SET PASSWD = MD5( '".$tbs->VarRef['passwd']."' ),
							  	  NEW_PASSWD = NULL 
							  WHERE NUM_EMPLEADO = " . $tbs->VarRef['numEmpleado'];
				$exc = mysql_query( $this->sql );

				if( !$exc ) 									
					$tbs->VarRef['error'] = "Error " . mysql_errno() . ": " . mysql_error();	
				else 
					$tbs->VarRef['msg'] = "";
					
				$this->desconectar();	

			} else {
				$tbs->VarRef['error'] = "Esta vacio el Numero de Empleado";
			}

			if( $tbs->VarRef['error'] == false ) {
				$tbs->VarRef['resultado'] = $tbs->VarRef['msg'];	
				$tbs->VarRef['color'] = "color: black";
			} else {
				$tbs->VarRef['resultado'] = $tbs->VarRef['error'];	
				$tbs->VarRef['color'] = "color: red";	
			}
			
			$tbs->LoadTemplate( '../template/enlaceConfirmacion.htm' );
			$tbs->Show();
		}		
		private function filtrarReporte() {
			include_once('../lib/tbs_class.php');
			$tbs = new clsTinyButStrong;
			
			$data = array(
				"numEmp" => $_POST['data']['numEmp'],
				"fechaDe"=> $_POST['data']['fechaDe'],
				"fechaA" => $_POST['data']['fechaA'],
				"where"  => " WHERE 1"
			);
			if( !empty( $data['numEmp'] ) )
				$data['where'] .= " AND N_EMP = " . $data['numEmp'];
			if( !empty( $data['fechaDe'] ) && !empty( $data['fechaA'] ) )
				$data['where'] .= " AND DATE( FECHA ) BETWEEN '".$data['fechaDe']."' AND '".$data['fechaA']."'";
			else if( !empty( $data['fechaDe'] ) )
				$data['where'] .= " AND DATE( FECHA ) = '".$data['fechaDe']."'";
			else if( !empty( $data['fechaA'] ) )
				$data['where'] .= " AND DATE( FECHA ) = '".$data['fechaA']."'";
			
			
			
			$mysqli = new Mysqli( "localhost", "root", "gisr00t", "red_invitados" );
			$this->sql = "
				SELECT 
					A.N_EMP, DATE( A.FECHA ) AS FECHA, LG.EMAIL, A.FECHA AS FECHA_HORA
				FROM ACCESOS A
				INNER JOIN vacaciones.login_vacaciones LG ON LG.NUM_EMPLEADO=A.N_EMP " . $data['where'] . " 
				ORDER BY N_EMP, FECHA_HORA DESC";
			$GLOBALS['excel'] = json_encode( $this->sql );
			
			$tbs->LoadTemplate( '../template/tablaFiltro.htm' );
			$tbs->MergeBlock( 'blk', $mysqli, $this->sql );
			$mysqli->close();
			$tbs->Show();
		}	
*/		
	}

	/*ini_set( 'memory_limit', '-1' );
	
	if( isset( $_GET['ac'] ) ) { 
		$ac  = Ajax::obtenerCadena( $_GET['ac'], $_GET['k'], $_GET['iv'] );
		$obj = new Ajax( $ac );		
	} else {		
		$obj = new Ajax( $_POST['ac'] );		
	}*/
	
	$obj = new Ajax( $_POST['ac'] );
	
	//$obj = new Vacaciones( 'enviarNumEmpleado' );		
	
?>
