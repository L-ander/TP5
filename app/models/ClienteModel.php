<?php

	require_once '../../config/conex.php';
	

	class clienteModel extends Conexion{

		private $id;
		private $cedula;
		private $nombre;
		private $apellido;
		private $sexo;
		private $id_parroquia;
		private $direccion;
        private $fecha_creacion;

		public function setId($id){ $this->id = $id; }
		public function getId(){ return $this->id; }
		public function setCedula($cedula){ $this->cedula = $cedula; }
		public function getCedula(){ return $this->cedula; }
		public function setNombre($nombre){ $this->nombre = $nombre; }
		public function getNombre(){ return $this->nombre; }
		public function setapellido($apellido){ $this->apellido = $apellido; }
		public function getapellido(){ return $this->apellido; }
		public function setsexo($sexo){ $this->sexo = $sexo; }
		public function getsexo(){ return $this->sexo; }
		public function setciudad($id_parroquia){ $this->id_parroquia = $id_parroquia; }
		public function getciudad(){ return $this->id_parroquia; }


		public function setdireccion($direccion){ $this->direccion = $direccion; }
		public function getdireccion(){ return $this->direccion; }
		public function setfecha_creacion($fecha_creacion){ $this->fecha_creacion = $fecha_creacion; }
		public function getfecha_creacion(){ return $this->fecha_creacion; }

		private function asegurarCedula(){
			$db = parent::conectar();
			if (!$db) return;
			$existe = $db->query("SHOW COLUMNS FROM cliente LIKE 'cedula'")->fetchColumn();
			if (!$existe) {
				$db->exec("ALTER TABLE cliente ADD COLUMN cedula varchar(20) NULL AFTER id");
			}
		}

		public function listar(){

			try {
				$this->asegurarCedula();

				$sql = ("SELECT c.id, c.cedula, c.nombre, c.apellido, c.sexo,
		 c.id_parroquia AS id_ciudad, c.direccion, c.fecha_creacion, c.fecha_modificacion,
		 p.nombre AS nombre_ciudad, m.nombre AS municipio, e.nombre AS estado 
         FROM cliente c
		 LEFT JOIN parroquia p ON c.id_parroquia = p.id
		 LEFT JOIN municipio m ON p.id_municipio = m.id
         LEFT JOIN estado e ON m.id_estado = e.id
         ORDER BY c.id DESC");
					$stmt = parent::conectar()->prepare($sql);

				$x = $stmt->execute();

				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data );

			} catch ( Exception $e ) {

				
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function consultar(){

			try {
				$this->asegurarCedula();

				$sql = ("SELECT c.*, c.id_parroquia AS id_ciudad
						FROM cliente c WHERE c.id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_STR);

				$x = $stmt->execute();

				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data );

			} catch ( Exception $e ) {

				
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function crear(){
    try {
        $this->asegurarCedula();
        
			$sql = ("INSERT INTO cliente (cedula, nombre, apellido, sexo, id_parroquia, direccion) VALUES (:cedula, :nombre, :apellido, :sexo, :id_parroquia, :direccion)");

        $stmt = parent::conectar()->prepare($sql);
        $stmt->bindparam(":cedula", $this->cedula, PDO::PARAM_STR);
        $stmt->bindparam(":nombre", $this->nombre, PDO::PARAM_STR);
        $stmt->bindparam(":apellido", $this->apellido, PDO::PARAM_STR);
        $stmt->bindparam(":sexo", $this->sexo, PDO::PARAM_STR);
		$stmt->bindparam(":id_parroquia", $this->id_parroquia, PDO::PARAM_INT);
        $stmt->bindparam(":direccion", $this->direccion, PDO::PARAM_STR);
        
        $x = $stmt->execute();

        $result = array('success' => true);
				

				$result = array('success' => true);

			} catch ( Exception $e ) {

				var_dump($e->getMessage());
				exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		}  #Fin Funcion


		public function obtenerLineas(){
			try {
				$sql = "SELECT id, nombre FROM linea_producto ORDER BY nombre ASC";
				
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				$result = array('success' => false, 'error' => $e->getMessage());
			}
			return $result;
		}

		public function obtenerMedidas(){
			try {
				$sql = "SELECT id, medida FROM unidad_medida ORDER BY medida ASC";
				
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				$result = array('success' => false, 'error' => $e->getMessage());
			}
			return $result;
		}
		public function listarCiudades(){
    try {
       
				$sql = "SELECT p.id AS id_ciudad, p.nombre AS nombre_ciudad, m.nombre AS municipio, e.nombre AS estado,
				p.id_municipio, e.id AS id_estado
		FROM parroquia p
		INNER JOIN municipio m ON p.id_municipio = m.id
        INNER JOIN estado e ON m.id_estado = e.id
		ORDER BY p.nombre ASC";

        $stmt = parent::conectar()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = array('success' => true, 'datos' => $data);

    } catch ( Exception $e ) {
        $result = array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
    }

    return $result;
} #Fin Funcion

		public function cedulaExiste($excluirId = 0){
			try {
				$sql = "SELECT id FROM cliente WHERE cedula = :cedula";
				if ((int)$excluirId > 0) {
					$sql .= " AND id <> :id";
				}
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":cedula", $this->cedula, PDO::PARAM_STR);
				if ((int)$excluirId > 0) {
					$stmt->bindValue(":id", (int)$excluirId, PDO::PARAM_INT);
				}
				$stmt->execute();
				return $stmt->fetchColumn() !== false;
			} catch (Exception $e) {
				return false;
			}
		}
		
		public function editar(){

			try {
				$this->asegurarCedula();

				$sql = ("UPDATE cliente SET cedula = :cedula, nombre=:nombre, apellido = :apellido, sexo = :sexo, id_parroquia = :id_parroquia, direccion = :direccion WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":cedula", $this->cedula, PDO::PARAM_STR);
				$stmt->bindparam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindparam(":apellido", $this->apellido, PDO::PARAM_STR);
				$stmt->bindparam(":sexo", $this->sexo, PDO::PARAM_STR);
				$stmt->bindparam(":id_parroquia", $this->id_parroquia, PDO::PARAM_INT);
				$stmt->bindparam(":direccion", $this->direccion, PDO::PARAM_STR);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_STR);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function eliminar(){

			try {

				$sql = ("DELETE FROM cliente WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_INT);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion
	} #Fin clase
?>