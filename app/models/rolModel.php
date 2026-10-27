<?php

	require_once '../../config/conex.php';

	class rolModel extends Conexion {

		private $id;
		private $nombre;

		public function setId($id) { $this->id = $id; }
		public function getId() { return $this->id; }
		public function setNombre($nombre) { $this->nombre = $nombre; }
		public function getNombre() { return $this->nombre; }

		public function listar() {
			try {
				$sql = "SELECT r.id AS id, r.nombre AS nombre FROM roles r ORDER BY r.id";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
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
				return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function crear() {
			try {
				$conex = parent::conectar();

				$chk = $conex->prepare("SELECT COUNT(*) FROM roles WHERE nombre = :nombre");
				$chk->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$chk->execute();
				if ($chk->fetchColumn() > 0) {
					return array('success' => false, 'msj' => 'Ya existe un rol con ese nombre.');
				}

				$stmt = $conex->prepare("INSERT INTO roles (nombre) VALUES (:nombre)");
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->execute();
				return array('success' => true, 'id' => (int)$conex->lastInsertId());
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function editar() {
			try {
				$conex = parent::conectar();

				$chk = $conex->prepare("SELECT COUNT(*) FROM roles WHERE nombre = :nombre AND id <> :id");
				$chk->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$chk->bindParam(":id", $this->id, PDO::PARAM_INT);
				$chk->execute();
				if ($chk->fetchColumn() > 0) {
					return array('success' => false, 'msj' => 'Ya existe un rol con ese nombre.');
				}

				$stmt = $conex->prepare("UPDATE roles SET nombre = :nombre WHERE id = :id");
				$stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				return array('success' => true);
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		public function eliminar() {
			$conex = null;
			try {
				if ((int)$this->id === 1) {
					return array('success' => false, 'msj' => 'El rol administrador no se puede eliminar.');
				}

				$conex = parent::conectar();

				// No borrar roles que tengan usuarios asignados
				$chk = $conex->prepare("SELECT COUNT(*) FROM usuario WHERE cod_rol = :id");
				$chk->bindParam(":id", $this->id, PDO::PARAM_INT);
				$chk->execute();
				if ($chk->fetchColumn() > 0) {
					return array('success' => false, 'msj' => 'No se puede eliminar: hay usuarios con este rol.');
				}

				$conex->beginTransaction();

				// Primero sus permisos (la FK no tiene ON DELETE CASCADE)
				$del = $conex->prepare("DELETE FROM permisos WHERE cod_rol = :id");
				$del->bindParam(":id", $this->id, PDO::PARAM_INT);
				$del->execute();

				$stmt = $conex->prepare("DELETE FROM roles WHERE id = :id");
				$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
				$stmt->execute();

				$conex->commit();
				return array('success' => true);
			} catch (Exception $e) {
				if ($conex && $conex->inTransaction()) { $conex->rollBack(); }
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		// ---------------------------------------------------------------
		// Permisos (rol <-> módulos)
		// ---------------------------------------------------------------

		public function listarModulos() {
			try {
				$sql = "SELECT id, Modulo AS nombre FROM modulo WHERE Modulo != 'Permisos' ORDER BY Modulo";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->execute();
				return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		// Usa setId() con el id del rol
		public function obtenerPermisosRol() {
			try {
				$sql = "SELECT cod_modulo FROM permisos WHERE cod_rol = :rol";
				$stmt = parent::conectar()->prepare($sql);
				$stmt->bindParam(":rol", $this->id, PDO::PARAM_INT);
				$stmt->execute();
				return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
			} catch (Exception $e) {
				return array('success' => false, 'error' => $e->getMessage());
			}
		}

		// Usa setId() con el id del rol. Reemplaza todos sus permisos por la lista recibida.
		public function guardarPermisosRol($modulos) {
			$conex = null;
			try {
				$conex = parent::conectar();
				$conex->beginTransaction();

				$del = $conex->prepare("DELETE FROM permisos WHERE cod_rol = :rol");
				$del->bindParam(":rol", $this->id, PDO::PARAM_INT);
				$del->execute();

				$ins = $conex->prepare("INSERT INTO permisos (cod_rol, cod_modulo) VALUES (:rol, :modulo)");
				foreach (array_unique(array_map('intval', $modulos)) as $idModulo) {
					$ins->bindValue(":rol", (int)$this->id, PDO::PARAM_INT);
					$ins->bindValue(":modulo", $idModulo, PDO::PARAM_INT);
					$ins->execute();
				}

				$conex->commit();
				return array('success' => true);
			} catch (Exception $e) {
				if ($conex && $conex->inTransaction()) { $conex->rollBack(); }
				return array('success' => false, 'error' => $e->getMessage());
			}
		}
	}
?>
