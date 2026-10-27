<?php
require_once __DIR__ . '/../../config/conex.php';

class ConfiguracionModel extends Conexion {
    private function conexion() {
        return Conexion::conectar();
    }

    private function texto($valor) {
        return trim((string)$valor);
    }

    private function validarId($valor) {
        return filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }

    public function listarCategorias() {
        $stmt = $this->conexion()->query('SELECT id, categoria AS nombre FROM categoria ORDER BY categoria ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarSubcategorias() {
        $stmt = $this->conexion()->query(
            'SELECT s.id, s.id_categoria, s.sub_categoria AS nombre, c.categoria AS categoria
             FROM subcategoria s INNER JOIN categoria c ON c.id = s.id_categoria
             ORDER BY c.categoria ASC, s.sub_categoria ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMedidas() {
        $stmt = $this->conexion()->query('SELECT id, medida AS nombre FROM unidad_medida ORDER BY medida ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarCategoria($data) {
        $nombre = $this->texto($data['nombre'] ?? '');
        if ($nombre === '') return false;
        $conexion = $this->conexion();
        $id = $this->validarId($data['id'] ?? null);
        if ($id !== false) {
            $stmt = $conexion->prepare('UPDATE categoria SET categoria = ? WHERE id = ?');
            return $stmt->execute([$nombre, $id]);
        }
        $stmt = $conexion->prepare('INSERT INTO categoria (categoria) VALUES (?)');
        return $stmt->execute([$nombre]);
    }

    public function guardarSubcategoria($data) {
        $nombre = $this->texto($data['nombre'] ?? '');
        $idCategoria = $this->validarId($data['id_categoria'] ?? null);
        if ($nombre === '' || $idCategoria === false) return false;
        $conexion = $this->conexion();
        $id = $this->validarId($data['id'] ?? null);
        if ($id !== false) {
            $stmt = $conexion->prepare('UPDATE subcategoria SET id_categoria = ?, sub_categoria = ? WHERE id = ?');
            return $stmt->execute([$idCategoria, $nombre, $id]);
        }
        $stmt = $conexion->prepare('INSERT INTO subcategoria (id_categoria, sub_categoria) VALUES (?, ?)');
        return $stmt->execute([$idCategoria, $nombre]);
    }

    public function guardarMedida($data) {
        $nombre = $this->texto($data['nombre'] ?? '');
        if ($nombre === '') return false;
        $conexion = $this->conexion();
        $id = $this->validarId($data['id'] ?? null);
        if ($id !== false) {
            $stmt = $conexion->prepare('UPDATE unidad_medida SET medida = ? WHERE id = ?');
            return $stmt->execute([$nombre, $id]);
        }
        $stmt = $conexion->prepare('INSERT INTO unidad_medida (medida) VALUES (?)');
        return $stmt->execute([$nombre]);
    }

    public function eliminar($tabla, $id) {
        $tablasPermitidas = ['categoria', 'subcategoria', 'unidad_medida'];
        $id = $this->validarId($id);
        if (!in_array($tabla, $tablasPermitidas, true) || $id === false) return false;
        $stmt = $this->conexion()->prepare("DELETE FROM $tabla WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
