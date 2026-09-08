<?php 

	require_once '../../config/conex.php';
	// require_once 'logs.php';

	class loginModel extends Conexion{

		private $usuario;
		private $password;

		public function setloginuser($usuario){ $this->usuario = $usuario; }
		public function getloginuser(){ return $this->usuario; }
		public function setpassword($password){ $this->password = $password; }	
		public function getpassword(){ return $this->password; }

		public function IniciarSesion(){
			try {
				$conexion = new Conexion();
				$conex = $conexion->conectar();
				
				// Buscamos el usuario y lo unimos con sus datos personales
				$sql = "SELECT u.id, u.username, u.password, u.cod_rol, u.status as estatus, 
							p.cedula, p.nombre, p.apellido 
						FROM usuario u
						INNER JOIN personal p ON u.id_personal = p.id
						WHERE u.username = :username";

				$stmt = $conex->prepare($sql);
				// Bind con la variable que recibe el input (username)
				$stmt->bindparam(":username", $this->usuario, PDO::PARAM_STR);

				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data );

			} catch ( Exception $e ) {
				error_log($e->getMessage());
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;
		}
	} #Fin clase loginModel
?>