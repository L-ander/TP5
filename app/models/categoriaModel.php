<?php
require_once '../../config/conex.php';

class categoria extends Conexion{

private $id;
		private $nombre;
		private $id_subcategoria;


		public function setId($id){ $this->id = $id; }
		public function getId(){ return $this->id; }
		public function setNombre($nombre){ $this->nombre = $nombre; }
		public function getNombre(){ return $this->nombre; }
		public function setSubcategoria($id_subcategoria){ $this->id_subcategoria = $id_subcategoria; }
		public function getSubcategoria(){ return $this->id_subcategoria; }


		public function listarC(){
    		try {
        		$sql = "SELECT lp.id, lp.nombre AS linea_producto, lp.id_subcategoria, sc.sub_categoria, sc.id_categoria, c.categoria
                		FROM linea_producto lp
                		LEFT JOIN subcategoria sc ON lp.id_subcategoria = sc.id
                		LEFT JOIN categoria c ON sc.id_categoria = c.id";

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

				$sql = ("SELECT * FROM linea_producto WHERE id = :id");

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

				$sql = ("INSERT INTO linea_producto (nombre, id_subcategoria) VALUES (:nombre, :id_subcategoria)");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindparam(":id_subcategoria", $this->id_subcategoria, PDO::PARAM_STR);

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

				$sql = ("UPDATE linea_producto SET nombre=:nombre, id_subcategoria = :id_subcategoria  WHERE id = :id");

				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindparam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindparam(":id_subcategoria", $this->id_subcategoria, PDO::PARAM_STR);
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

				$sql = ("DELETE FROM linea_producto WHERE id = :id");

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