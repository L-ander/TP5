<?php
require_once __DIR__ . '/../../config/conex.php';

class VendedorModel extends Conexion {
    
    public function listar() {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare(
            "SELECT p.id, p.cedula, p.nombre, p.apellido, p.telefono, p.status, 
                    tp.nombre as tipo_personal, p.id_tipo_personal 
             FROM personal p 
             LEFT JOIN tipo_personal tp ON p.id_tipo_personal = tp.id 
             ORDER BY p.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si la cedula ya existe
    public function existeCedula($cedula, $idExcluir = null) {
        $conexion = Conexion::conectar();
        if ($idExcluir) {
            $stmt = $conexion->prepare("SELECT id FROM personal WHERE cedula = ? AND id != ?");
            $stmt->execute([$cedula, $idExcluir]);
        } else {
            $stmt = $conexion->prepare("SELECT id FROM personal WHERE cedula = ?");
            $stmt->execute([$cedula]);
        }
        return $stmt->rowCount() > 0;
    }

    public function crear($data) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO personal (cedula, nombre, apellido, telefono, id_tipo_personal, status) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['cedula'], $data['nombre'], $data['apellido'], $data['telefono'], $data['id_tipo_personal'], $data['status']]);
    }

    public function editar($data) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE personal SET cedula=?, nombre=?, apellido=?, telefono=?, id_tipo_personal=?, status=? WHERE id=?");
        return $stmt->execute([$data['cedula'], $data['nombre'], $data['apellido'], $data['telefono'], $data['id_tipo_personal'], $data['status'], $data['id']]);
    }

    public function eliminar($id) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("DELETE FROM personal WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function listarTiposPersonal() {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("SELECT id, nombre FROM tipo_personal");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarRoles() {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("SELECT ID as id, roles as nombre FROM roles");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPersonalSinUsuario() {
        $conexion = Conexion::conectar();
        // Filtramos al personal que no tiene un registro asociado en la tabla usuario
        $stmt = $conexion->prepare(
            "SELECT p.id, p.cedula, p.nombre, p.apellido 
             FROM personal p 
             LEFT JOIN usuario u ON p.id = u.id_personal 
             WHERE u.id IS NULL AND p.status = 1"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearUsuario($data) {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("INSERT INTO usuario (id_personal, username, password, cod_rol, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['id_personal'], $data['username'], $data['password'], $data['cod_rol'], $data['status']]);
            return 'exito';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || $e->errorInfo[1] == 1062) {
                return 'duplicado';
            }
            return 'error';
        }
    }

    public function listarUsuarios() {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare(
            "SELECT u.id, u.username, u.password, u.cod_rol, u.status, u.id_personal, 
                    p.nombre, p.apellido, r.roles as nombre_rol 
             FROM usuario u 
             INNER JOIN personal p ON u.id_personal = p.id 
             LEFT JOIN roles r ON u.cod_rol = r.ID 
             ORDER BY u.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editarUsuario($data) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE usuario SET username=?, password=?, cod_rol=?, status=? WHERE id=?");
        return $stmt->execute([
            $data['username'], 
            $data['password'], 
            $data['cod_rol'], 
            $data['status'], 
            $data['id_usuario']
        ]);
    }

    public function eliminarUsuario($id) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("DELETE FROM usuario WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>