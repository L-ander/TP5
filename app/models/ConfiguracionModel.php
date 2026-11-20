<?php

require_once __DIR__ . '/../../config/conex.php';

class ConfiguracionModel extends Conexion {
    private $id;
    private $nombre;
    private $idCategoria;
    private $idSubcategoria;

    // FASE 1: Estado del modelo
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getNombre() { return $this->nombre; }
    public function setIdCategoria($idCategoria) { $this->idCategoria = $idCategoria; }
    public function getIdCategoria() { return $this->idCategoria; }
    public function setIdSubcategoria($idSubcategoria) { $this->idSubcategoria = $idSubcategoria; }
    public function getIdSubcategoria() { return $this->idSubcategoria; }

    // FASE 2: Consultas de categorías, subcategorías y catálogos
    public function listarCategorias() {
        return $this->listar('SELECT id, categoria AS nombre FROM categoria ORDER BY categoria ASC');
    }

    public function listarSubcategorias() {
        return $this->listar(
            'SELECT s.id, s.id_categoria, s.sub_categoria AS nombre, c.categoria AS categoria
             FROM subcategoria s INNER JOIN categoria c ON c.id = s.id_categoria
             ORDER BY c.categoria ASC, s.sub_categoria ASC'
        );
    }

    public function listarMedidas() {
        return $this->listar('SELECT id, medida AS nombre FROM unidad_medida ORDER BY medida ASC');
    }

    public function listarPresentaciones() {
        return $this->listar('SELECT id, contenido AS nombre FROM presentacion');
    }

    public function listarLineas() {
        return $this->listar(
            'SELECT lp.id, lp.nombre, lp.id_subcategoria, s.sub_categoria AS subcategoria
             FROM linea_producto lp
             LEFT JOIN subcategoria s ON lp.id_subcategoria = s.id'
        );
    }

    public function registroExiste($tabla, $id) {
        $tablasPermitidas = ['categoria', 'subcategoria', 'unidad_medida', 'presentacion', 'linea_producto'];
        if (!in_array($tabla, $tablasPermitidas, true) || !$this->idValido($id)) {
            return ['success' => false];
        }

        try {
            $stmt = parent::conectar()->prepare("SELECT id FROM $tabla WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$id]);
            return ['success' => true, 'existe' => $stmt->fetchColumn() !== false];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // FASE 3: Altas y ediciones de catálogos
    public function crearCategoria() {
        return $this->guardar(
            'INSERT INTO categoria (categoria) VALUES (?)',
            [$this->nombre]
        );
    }

    public function editarCategoria() {
        return $this->editar(
            'UPDATE categoria SET categoria = ? WHERE id = ?',
            [$this->nombre, $this->id]
        );
    }

    public function crearSubcategoria() {
        if (!$this->idValido($this->idCategoria)) {
            return ['success' => false];
        }

        return $this->guardar(
            'INSERT INTO subcategoria (id_categoria, sub_categoria) VALUES (?, ?)',
            [(int)$this->idCategoria, $this->nombre]
        );
    }

    public function editarSubcategoria() {
        if (!$this->idValido($this->idCategoria)) {
            return ['success' => false];
        }

        return $this->editar(
            'UPDATE subcategoria SET id_categoria = ?, sub_categoria = ? WHERE id = ?',
            [(int)$this->idCategoria, $this->nombre, $this->id]
        );
    }

    public function crearMedida() {
        return $this->guardar(
            'INSERT INTO unidad_medida (medida) VALUES (?)',
            [$this->nombre]
        );
    }

    public function editarMedida() {
        return $this->editar(
            'UPDATE unidad_medida SET medida = ? WHERE id = ?',
            [$this->nombre, $this->id]
        );
    }

    public function crearPresentacion() {
        return $this->guardar(
            'INSERT INTO presentacion (contenido) VALUES (?)',
            [$this->nombre]
        );
    }

    public function editarPresentacion() {
        return $this->editar(
            'UPDATE presentacion SET contenido = ? WHERE id = ?',
            [$this->nombre, $this->id]
        );
    }

    public function crearLinea() {
        if (!$this->idValido($this->idSubcategoria)) {
            return ['success' => false];
        }

        return $this->guardar(
            'INSERT INTO linea_producto (nombre, id_subcategoria) VALUES (?, ?)',
            [$this->nombre, (int)$this->idSubcategoria]
        );
    }

    public function editarLinea() {
        if (!$this->idValido($this->idSubcategoria)) {
            return ['success' => false];
        }

        return $this->editar(
            'UPDATE linea_producto SET nombre = ?, id_subcategoria = ? WHERE id = ?',
            [$this->nombre, (int)$this->idSubcategoria, $this->id]
        );
    }

    // FASE 4: Eliminación y utilidades de persistencia
    public function eliminar($tabla, $id) {
        $tablasPermitidas = ['categoria', 'subcategoria', 'unidad_medida', 'presentacion', 'linea_producto'];
        if (!in_array($tabla, $tablasPermitidas, true) || !$this->idValido($id)) {
            return ['success' => false];
        }

        return $this->ejecutar(
            "DELETE FROM $tabla WHERE id = ?",
            [(int)$id]
        );
    }

    private function listar($sql) {
        try {
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            return ['success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function guardar($sql, $parametros) {
        if (trim((string)$this->nombre) === '') {
            return ['success' => false];
        }

        return $this->ejecutar($sql, $parametros);
    }

    private function editar($sql, $parametros) {
        if (trim((string)$this->nombre) === '' || !$this->idValido($this->id)) {
            return ['success' => false];
        }

        return $this->ejecutar($sql, $parametros);
    }

    private function ejecutar($sql, $parametros) {
        try {
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute($parametros);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function idValido($id) {
        return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }
}
