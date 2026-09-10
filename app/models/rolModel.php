<?php

	require_once '../../config/conex.php';
	// require_once 'logs.php';

	class rolModel extends Conexion {

		private $id;
		private $nombre;


		public function setId($id) { $this->id = $id; }
		public function getId() { return $this->id; }
		public function setNombre($nombre) { $this->nombre = $nombre; }
		public function getNombre() { return $this->nombre; }

		public function listar() {
			try {
				$sql = "SELECT r.id AS id, r.nombre AS nombre
				FROM roles r";

				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function consultar() {
    try {
        $sql = "SELECT r.id AS id, r.nombre AS nombre FROM roles r WHERE r.id = :id";

        $stmt = parent::conectar()->prepare($sql);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        return array('success' => true, 'datos' => $data);
    } catch (Exception $e) {
        return array('success' => false, 'error' => $e->getMessage());
    }
}

		public function crear() {
			try {
				$sql = "INSERT INTO roles (nombre) VALUES (:nombre)";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->execute();
				return array('success' => true);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}


		public function editar() {
			try {
				$sql = "UPDATE roles SET nombre = :nombre WHERE id = :id";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				return array('success' => true);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function eliminar() {
			try {
				$sql = "DELETE FROM roles WHERE id = :id";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				return array('success' => true);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}



        



	}
?>