<?php
require_once '../../config/conex.php';

class UbicacionModel extends Conexion {
    private $id;
    private $nombre;
    private $id_municipio;
    private $id_estado;

    
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getNombre() { return $this->nombre; }
    public function setIdMunicipio($id_municipio) { $this->id_municipio = $id_municipio; }
    public function getIdMunicipio() { return $this->id_municipio; }

    public function setIdEstado($id_estado) { $this->id_estado = $id_estado; }
    public function getIdEstado() { return $this->id_estado; }

   
    public function listarCiudadesCompletas() {
        try {
            $sql = "SELECT p.id AS id_ciudad, p.nombre AS nombre_ciudad, m.id AS id_municipio, m.nombre AS municipio, e.id AS id_estado, e.nombre AS estado 
                    FROM parroquia p
                    INNER JOIN municipio m ON p.id_municipio = m.id
                    INNER JOIN estado e ON m.id_estado = e.id
                    ORDER BY p.id DESC";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

   
    public function listarEstados() {
        try {
            $sql = "SELECT * FROM estado ORDER BY nombre ASC";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

    
    public function listarMunicipiosPorEstado($id_estado) {
        try {
            $sql = "SELECT * FROM municipio WHERE id_estado = :id_estado ORDER BY nombre ASC";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':id_estado', $id_estado, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

    public function crearEstado() {
        try {
            $sql = "INSERT INTO estado (nombre) VALUES (:nombre)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

    public function crearMunicipio() {
        try {
            $sql = "INSERT INTO municipio (nombre, id_estado) VALUES (:nombre, :id_estado)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id_estado', $this->id_estado, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

    
    public function crearCiudad() {
        try {
            $sql = "INSERT INTO parroquia (nombre, id_municipio, id_estado) VALUES (:nombre, :id_municipio, :id_estado)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id_municipio', $this->id_municipio, PDO::PARAM_INT);
            $stmt->bindParam(':id_estado', $this->id_estado, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

    
    public function editarCiudad() {
        try {
            $sql = "UPDATE parroquia SET nombre = :nombre, id_municipio = :id_municipio, id_estado = :id_estado WHERE id = :id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id_municipio', $this->id_municipio, PDO::PARAM_INT);
            $stmt->bindParam(':id_estado', $this->id_estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'msj' => $e->getMessage());
        }
    }

   
    public function eliminarCiudad() {
        try {
            $sql = "DELETE FROM parroquia WHERE id = :id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'msj' => 'No se puede eliminar la ciudad porque está asignada a uno o más clientes.');
        }
    }
}