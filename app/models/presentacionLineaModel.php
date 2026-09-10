<?php
    require_once '../../config/conex.php';

    class presentacionLineaModel extends Conexion {
        private $id;
        private $nombre;
        private $contenido;
        private $id_subcategoria;

        public function setId($id) { $this->id = $id; }
        public function setNombre($nombre) { $this->nombre = $nombre; }
        public function setContenido($contenido) { $this->contenido = $contenido; }
        public function setIdSubcategoria($id_subcategoria) { $this->id_subcategoria = $id_subcategoria; }

        public function listarPresentaciones() {
            try {
                $sql = "SELECT id, contenido AS nombre FROM presentacion";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->execute();
                return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }

        public function listarLineas() {
            try {
                $sql = "SELECT lp.id, lp.nombre, lp.id_subcategoria, s.sub_categoria AS subcategoria 
                        FROM linea_producto lp 
                        LEFT JOIN subcategoria s ON lp.id_subcategoria = s.id";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->execute();
                return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }

        public function crearPresentacion() {
            try {
                $sql = "INSERT INTO presentacion (contenido) VALUES (:contenido)";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":contenido", $this->contenido, PDO::PARAM_STR);
                $stmt->execute();
                return array('success' => true);
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }

        public function editarPresentacion() {
            try {
                $sql = "UPDATE presentacion SET contenido = :contenido WHERE id = :id";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":contenido", $this->contenido, PDO::PARAM_STR);
                $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
                $stmt->execute();
                return array('success' => true);
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }

        public function crearLinea() {
            try {
                $sql = "INSERT INTO linea_producto (nombre, id_subcategoria) VALUES (:nombre, :id_subcategoria)";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
                $stmt->bindParam(":id_subcategoria", $this->id_subcategoria, PDO::PARAM_INT);
                $stmt->execute();
                return array('success' => true);
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }

        public function editarLinea() {
            try {
                $sql = "UPDATE linea_producto SET nombre = :nombre, id_subcategoria = :id_subcategoria WHERE id = :id";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
                $stmt->bindParam(":id_subcategoria", $this->id_subcategoria, PDO::PARAM_INT);
                $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
                $stmt->execute();
                return array('success' => true);
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }

        public function eliminarRecord($tabla, $id) {
            try {
                $tablaPermitida = ($tabla === 'presentacion') ? 'presentacion' : (($tabla === 'linea_producto') ? 'linea_producto' : null);
                if (!$tablaPermitida) throw new Exception("Tabla no válida");

                $sql = "DELETE FROM {$tablaPermitida} WHERE id = :id";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                return array('success' => true);
            } catch (Exception $e) {
                return array('success' => false, 'error' => $e->getMessage());
            }
        }
    }
?>