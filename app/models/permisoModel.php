<?php
require_once '../../config/conex.php';

class permisoModel extends Conexion {

    private $id;
    private $cod_rol;
    private $cod_modulo;

    public function setId($id) { $this->id = $id; }
    public function setCodRol($cod_rol) { $this->cod_rol = $cod_rol; }
    public function setCodModulo($cod_modulo) { $this->cod_modulo = $cod_modulo; }

    // Listar todos los módulos del sistema para pintarlos en los checkboxes/vistas
    public function listarModulos() {
        try {
            $sql = "SELECT id, Modulo AS nombre FROM modulo";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Obtener los módulos que tiene asignado un rol específico
    public function obtenerPermisosPorRol() {
        try {
            $sql = "SELECT cod_modulo FROM permisos WHERE cod_rol = :cod_rol";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":cod_rol", $this->cod_rol, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Guardar o actualizar los permisos de un rol (transaccional)
    public function guardarPermisos($modulosSeleccionados) {
        $conexion = parent::conectar();
        try {
            $conexion->beginTransaction();

            // 1. Borramos los permisos anteriores de este rol
            $sqlDelete = "DELETE FROM permisos WHERE cod_rol = :cod_rol";
            $stmtDelete = $conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(":cod_rol", $this->cod_rol, PDO::PARAM_INT);
            $stmtDelete->execute();

            // 2. Insertamos los nuevos módulos seleccionados
            if (!empty($modulosSeleccionados)) {
                $sqlInsert = "INSERT INTO permisos (cod_rol, cod_modulo) VALUES (:cod_rol, :cod_modulo)";
                $stmtInsert = $conexion->prepare($sqlInsert);

                foreach ($modulosSeleccionados as $idModulo) {
                    $stmtInsert->execute([
                        ':cod_rol' => $this->cod_rol,
                        ':cod_modulo' => $idModulo
                    ]);
                }
            }

            $conexion->commit();
            return array('success' => true);
        } catch (Exception $e) {
            $conexion->rollBack();
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
}
?>