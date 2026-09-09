<?php
require_once '../../config/conex.php';

class permisos extends Conexion{

        private $id;
		private $cod_rol;
		private $cod_modulo;


		public function setId($id){ $this->id = $id; }
		public function getId(){ return $this->id; }
		public function setCodrol($cod_rol){ $this->cod_rol = $cod_rol; }
		public function getCodrol(){ return $this->cod_rol; }
		public function setCodmodulo($cod_modulo){ $this->cod_modulo = $cod_modulo; }
		public function getCodmodulo(){ return $this->cod_modulo; }


		public function listarC(){
    		try {
        		$sql = "SELECT p.id AS ID, r.roles AS Rol, m.Modulo AS Modulo FROM permisos p
                JOIN roles r ON p.cod_rol = r.ID
                JOIN modulo m ON p.cod_modulo = m.id
                ORDER BY r.roles;";

        		$stmt = parent::conectar()->prepare($sql);
        		$stmt->execute();
        		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        		$result = array('success' => true, 'datos' => $data);
    		} catch (Exception $e) {
        		$result = array('success' => false, 'error' => 1);
    		}

    return $result;
}#Fin Funcion

		public function consultarC(){

			try {

				$sql = ("SELECT * FROM permisos WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_STR);

				$x = $stmt->execute();

				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data );

			} catch ( Exception $e ) {

				// var_dump($e->getMessage());
				// exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function crearC(){

			try {

				$sql = ("INSERT INTO permisos (cod_rol, cod_modulo) VALUES (:cod_rol, :cod_modulo)");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":cod_rol", $this->cod_rol, PDO::PARAM_STR);
				$stmt->bindparam(":cod_modulo", $this->cod_modulo, PDO::PARAM_STR);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				var_dump($e->getMessage());
				exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion


		public function obtenerSubcategorias(){
			try {
				$sql = "SELECT id, sub_categoria FROM subcategoria ORDER BY sub_categoria ASC";
				
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				$result = array('success' => false, 'error' => $e->getMessage());
			}
			return $result;
		}

		public function obtenerCategorias(){
    		try {
        		$sql = "SELECT id, categoria FROM categoria ORDER BY categoria ASC";
        		$stmt = parent::conectar()->prepare($sql);
        		$stmt->execute();
        		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        		return array('success' => true, 'datos' => $data);
    		} catch (Exception $e) {
        		return array('success' => false, 'error' => $e->getMessage());
    		}
		}

		public function editarC(){

			try {

				$sql = ("UPDATE permisos SET cod_rol=:cod_rol, cod_modulo = :cod_modulo  WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":cod_rol", $this->cod_rol, PDO::PARAM_STR);
				$stmt->bindparam(":cod_modulo", $this->cod_modulo, PDO::PARAM_STR);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_STR);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				// var_dump($e->getMessage());
				// exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function eliminarC(){

			try {

				$sql = ("DELETE FROM permisos WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_INT);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				// var_dump($e->getMessage());
				// exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion


		public function crearSubcategoria($id_categoria, $sub_categoria) {
        try {
            // Reemplaza $this->db por la variable de conexión PDO que maneje tu proyecto
            $sql = "INSERT INTO subcategoria (id_categoria, sub_categoria) VALUES (?, ?)";
            $stmt = parent::conectar()->prepare($sql);
            $result = $stmt->execute([$id_categoria, $sub_categoria]);

            if ($result) {
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

}

class rol extends Conexion{

        private $id;
		private $nombre;


		public function setId($id){ $this->id = $id; }
		public function getId(){ return $this->id; }
		public function setNombre($nombre) { $this->nombre = $nombre; }
        public function getNombre() { return $this->nombre; }


		public function listarRol(){
    		try {
        		$sql = "SELECT id, roles as nombre FROM roles;";

        		$stmt = parent::conectar()->prepare($sql);
        		$stmt->execute();
        		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        		$result = array('success' => true, 'datos' => $data);
    		} catch (Exception $e) {
        		$result = array('success' => false, 'error' => 1);
    		}

    return $result;
}#Fin Funcion

		public function consultarRol(){

			try {

				$sql = ("SELECT * FROM roles WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_STR);

				$x = $stmt->execute();

				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$result = array('success' => true, 'datos' => $data );

			} catch ( Exception $e ) {

				// var_dump($e->getMessage());
				// exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function crearRol(){

			try {

				$sql = "INSERT INTO roles (roles) VALUES (:nombre)";

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":nombre", $this->nombre, PDO::PARAM_STR);


				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				var_dump($e->getMessage());
				exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion




		public function editarRol(){

			try {

				$sql = ("UPDATE roles SET roles=:nombre  WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_STR);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				// var_dump($e->getMessage());
				// exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion

		public function eliminarRol(){

			try {

				$sql = ("DELETE FROM roles WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":id", $this->id, PDO::PARAM_INT);

				$x = $stmt->execute();

				$result = array('success' => true);

			} catch ( Exception $e ) {

				// var_dump($e->getMessage());
				// exit();
				$result = array('success' => false, 'error' => 1 );
			}

			return $result;

			$conex = null;

		} #Fin Funcion



}