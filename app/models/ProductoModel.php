<?php

	require_once '../../config/conex.php';
	// require_once 'logs.php';

	class productoModel extends Conexion {

		private $id;
		private $nombre;
		private $id_linea;
		private $presentacion;
		private $id_medida;
        private $precio;

		public function setId($id) { $this->id = $id; }
		public function getId() { return $this->id; }
		public function setNombre($nombre) { $this->nombre = $nombre; }
		public function getNombre() { return $this->nombre; }
		public function setLinea($id_linea) { $this->id_linea = $id_linea; }
		public function getLinea() { return $this->id_linea; }
		public function setPresentacion($presentacion) { $this->presentacion = $presentacion; }
		public function getPresentacion() { return $this->presentacion; }
		public function setMedida($id_medida) { $this->id_medida = $id_medida; }
		public function getMedida() { return $this->id_medida; }
		public function setPrecio($precio) { $this->precio = $precio; }
		public function getPrecio() { return $this->precio; }

		public function listar() {
			try {
				$sql = "SELECT p.id,
                        np.nombre, p.nombre AS nombre,
                        p.id_linea,
                        lp.nombre AS nombre_linea,
                        p.presentacion,
                        pr.contenido, p.presentacion AS nombre_presentacion,
                        p.id_medida,
                        um.medida, '' AS nombre_medida,
                        p.precio
                FROM producto p
                LEFT JOIN linea_producto lp ON p.id_linea = lp.id
                LEFT JOIN presentacion pr ON p.presentacion = pr.id
                LEFT JOIN unidad_medida um ON p.id_medida = um.id
                LEFT JOIN nombre_producto np ON p.nombre = np.id";

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
				$sql = "SELECT p.id,
                        COALESCE(np.nombre, p.nombre) AS nombre,
                        p.id_linea,
                        lp.nombre AS nombre_linea,
                        p.presentacion,
                        COALESCE(pr.contenido, p.presentacion) AS nombre_presentacion,
                        p.id_medida,
                        COALESCE(um.medida, '') AS nombre_medida,
                        p.precio
                FROM producto p
                LEFT JOIN linea_producto lp ON p.id_linea = lp.id
                LEFT JOIN presentacion pr ON p.presentacion = pr.id
                LEFT JOIN unidad_medida um ON p.id_medida = um.id
                LEFT JOIN nombre_producto np ON p.nombre = np.id
                WHERE p.id = :id";

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
				$sql = "INSERT INTO producto (nombre, id_linea, presentacion, id_medida, precio) VALUES (:nombre, :id_linea, :presentacion, :id_medida, :precio)";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":id_linea", $this->id_linea, PDO::PARAM_INT);
				$stmt->bindParam(":presentacion", $this->presentacion, PDO::PARAM_INT);
				$stmt->bindParam(":id_medida", $this->id_medida, PDO::PARAM_INT);
				$stmt->bindParam(":precio", $this->precio, PDO::PARAM_STR);
				$stmt->execute();
				return array('success' => true);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function obtenerLineas() {
			try {
				$sql = "SELECT id, nombre FROM linea_producto ORDER BY nombre ASC";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function obtenerPresentaciones() {
			try {
				$sql = "SELECT id, contenido FROM presentacion ORDER BY contenido ASC";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function obtenerMedidas() {
			try {
				$sql = "SELECT id, medida FROM unidad_medida ORDER BY medida ASC";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return array('success' => true, 'datos' => $data);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function editar() {
			try {
				$sql = "UPDATE producto SET nombre = :nombre, id_linea = :id_linea, presentacion = :presentacion, id_medida = :id_medida, precio = :precio WHERE id = :id";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":id_linea", $this->id_linea, PDO::PARAM_INT);
				$stmt->bindParam(":presentacion", $this->presentacion, PDO::PARAM_INT);
				$stmt->bindParam(":id_medida", $this->id_medida, PDO::PARAM_INT);
				$stmt->bindParam(":precio", $this->precio, PDO::PARAM_STR);
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				return array('success' => true);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function eliminar() {
			try {
				$sql = "DELETE FROM producto WHERE id = :id";
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