<?php

require_once '../../config/conex.php';

class clienteModel extends Conexion {

    private $id;
    private $nombre;
    private $apellido;
    private $cedula;
    private $sexo;
    private $id_parroquia;
    private $direccion;
    private $fecha_creacion;

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getNombre() { return $this->nombre; }
    public function setapellido($apellido) { $this->apellido = $apellido; }
    public function getapellido() { return $this->apellido; }
    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function getCedula() { return $this->cedula; }
    public function setsexo($sexo) { $this->sexo = $sexo; }
    public function getsexo() { return $this->sexo; }
    public function setciudad($id_parroquia) { $this->id_parroquia = $id_parroquia; }
    public function setParroquia($id_parroquia) { $this->id_parroquia = $id_parroquia; }
    public function getciudad() { return $this->id_parroquia; }
    public function getParroquia() { return $this->id_parroquia; }

    public function setdireccion($direccion) { $this->direccion = $direccion; }
    public function getdireccion() { return $this->direccion; }
    public function setfecha_creacion($fecha_creacion) { $this->fecha_creacion = $fecha_creacion; }
    public function getfecha_creacion() { return $this->fecha_creacion; }

    public function listar() {
        try {
            $sql = "SELECT c.id, c.nombre, c.apellido, c.cedula, c.sexo,
                           c.id_parroquia, c.direccion, c.fecha_creacion, c.fecha_modificacion,
                           p.nombre AS nombre_parroquia, m.nombre AS municipio, e.nombre AS estado 
                    FROM cliente c
                    LEFT JOIN parroquia p ON c.id_parroquia = p.id
                    LEFT JOIN municipio m ON p.id_municipio = m.id
                    LEFT JOIN estado e ON m.id_estado = e.id
                    ORDER BY c.id DESC";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
        }
    }

    public function consultar() {
        try {
            $sql = "SELECT c.*, c.id_parroquia 
                    FROM cliente c WHERE c.id = :id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
        }
    }

    public function crear() {
        try {
            $sql = "INSERT INTO cliente (nombre, apellido, cedula, sexo, id_parroquia, direccion) 
                    VALUES (:nombre, :apellido, :cedula, :sexo, :id_parroquia, :direccion)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $this->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":cedula", $this->cedula, PDO::PARAM_INT);
            $stmt->bindParam(":sexo", $this->sexo, PDO::PARAM_STR);
            $stmt->bindParam(":id_parroquia", $this->id_parroquia, PDO::PARAM_INT);
            $stmt->bindParam(":direccion", $this->direccion, PDO::PARAM_STR);
            $stmt->execute();

            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
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

    public function listarCiudades() {
        try {
            $sql = "SELECT p.id AS id_parroquia, p.nombre AS nombre_parroquia, m.nombre AS municipio, e.nombre AS estado,
                           p.id_municipio, e.id AS id_estado
                    FROM parroquia p
                    INNER JOIN municipio m ON p.id_municipio = m.id
                    INNER JOIN estado e ON m.id_estado = e.id
                    ORDER BY p.nombre ASC";

            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
        }
    }

    public function editar() {
        try {
            $sql = "UPDATE cliente 
                    SET nombre = :nombre, apellido = :apellido, cedula = :cedula, sexo = :sexo, id_parroquia = :id_parroquia, direccion = :direccion 
                    WHERE id = :id";

            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $this->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":cedula", $this->cedula, PDO::PARAM_INT);
            $stmt->bindParam(":sexo", $this->sexo, PDO::PARAM_STR);
            $stmt->bindParam(":id_parroquia", $this->id_parroquia, PDO::PARAM_INT);
            $stmt->bindParam(":direccion", $this->direccion, PDO::PARAM_STR);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();

            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
        }
    }

    public function eliminar() {
        try {
            $sql = "DELETE FROM cliente WHERE id = :id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();

            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => 1, 'msj' => $e->getMessage());
        }
    }
}
?>