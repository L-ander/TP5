<?php
require_once __DIR__ . '/../../config/conex.php';

class VendedorModel extends Conexion {
    
    private $id;
    private $cedula;
    private $nombre;
    private $apellido;
    private $telefono;
    private $id_tipo_personal;
    private $status;

    private $id_usuario;
    private $username;
    private $password;
    private $cod_rol;

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }

    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function getCedula() { return $this->cedula; }

    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getNombre() { return $this->nombre; }

    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function getApellido() { return $this->apellido; }

    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function getTelefono() { return $this->telefono; }

    public function setIdTipoPersonal($id_tipo_personal) { $this->id_tipo_personal = $id_tipo_personal; }
    public function getIdTipoPersonal() { return $this->id_tipo_personal; }

    public function setStatus($status) { $this->status = $status; }
    public function getStatus() { return $this->status; }

    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function getIdUsuario() { return $this->id_usuario; }

    public function setUsername($username) { $this->username = $username; }
    public function getUsername() { return $this->username; }

    public function setPassword($password) { $this->password = $password; }
    public function getPassword() { return $this->password; }

    public function setCodRol($cod_rol) { $this->cod_rol = $cod_rol; }
    public function getCodRol() { return $this->cod_rol; }

    //Personal

    public function listar() {
        try {
            $sql = "SELECT p.id, p.cedula, p.nombre, p.apellido, p.telefono, p.status, 
                           tp.nombre as tipo_personal, p.id_tipo_personal 
                    FROM personal p 
                    LEFT JOIN tipo_personal tp ON p.id_tipo_personal = tp.id 
                    ORDER BY p.id DESC";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function existeCedula($idExcluir = null) {
        try {
            if ($idExcluir) {
                $sql = "SELECT id FROM personal WHERE cedula = :cedula AND id != :id";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":cedula", $this->cedula, PDO::PARAM_INT);
                $stmt->bindParam(":id", $idExcluir, PDO::PARAM_INT);
            } else {
                $sql = "SELECT id FROM personal WHERE cedula = :cedula";
                $stmt = parent::conectar()->prepare($sql);
                $stmt->bindParam(":cedula", $this->cedula, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function crear() {
        try {
            $sql = "INSERT INTO personal (cedula, nombre, apellido, telefono, id_tipo_personal, status) 
                    VALUES (:cedula, :nombre, :apellido, :telefono, :id_tipo_personal, :status)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":cedula", $this->cedula, PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $this->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $this->telefono, PDO::PARAM_STR);
            $stmt->bindParam(":id_tipo_personal", $this->id_tipo_personal, PDO::PARAM_INT);
            $stmt->bindParam(":status", $this->status, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function editar() {
        try {
            $sql = "UPDATE personal 
                    SET cedula=:cedula, nombre=:nombre, apellido=:apellido, telefono=:telefono, 
                        id_tipo_personal=:id_tipo_personal, status=:status 
                    WHERE id=:id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":cedula", $this->cedula, PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $this->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $this->telefono, PDO::PARAM_STR);
            $stmt->bindParam(":id_tipo_personal", $this->id_tipo_personal, PDO::PARAM_INT);
            $stmt->bindParam(":status", $this->status, PDO::PARAM_INT);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function eliminar() {
        try {
            $sql = "DELETE FROM personal WHERE id=:id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    //Usuarios

    public function listarTiposPersonal() {
        try {
            $sql = "SELECT id, nombre FROM tipo_personal";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function listarRoles() {
        try {
            $sql = "SELECT ID as id, nombre FROM roles";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function listarPersonalSinUsuario() {
        try {
            $sql = "SELECT p.id, p.cedula, p.nombre, p.apellido 
                    FROM personal p 
                    LEFT JOIN usuario u ON p.id = u.id_personal 
                    WHERE u.id IS NULL AND p.status = 1";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function crearUsuario() {
        try {
            $sql = "INSERT INTO usuario (id_personal, username, password, cod_rol, status) 
                    VALUES (:id_personal, :username, :password, :cod_rol, :status)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":id_personal", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $this->username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $this->password, PDO::PARAM_STR);
            $stmt->bindParam(":cod_rol", $this->cod_rol, PDO::PARAM_INT);
            $stmt->bindParam(":status", $this->status, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true, 'resultado' => 'exito');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                return array('success' => true, 'resultado' => 'duplicado');
            }
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function listarUsuarios() {
        try {
            $sql = "SELECT u.id, u.username, u.password, u.cod_rol, u.status, u.id_personal, 
                           p.nombre, p.apellido, r.nombre as nombre_rol 
                    FROM usuario u 
                    INNER JOIN personal p ON u.id_personal = p.id 
                    LEFT JOIN roles r ON u.cod_rol = r.ID 
                    ORDER BY u.id DESC";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array('success' => true, 'datos' => $data);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function editarUsuario() {
        try {
            $sql = "UPDATE usuario 
                    SET username=:username, password=:password, cod_rol=:cod_rol, status=:status 
                    WHERE id=:id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":username", $this->username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $this->password, PDO::PARAM_STR);
            $stmt->bindParam(":cod_rol", $this->cod_rol, PDO::PARAM_INT);
            $stmt->bindParam(":status", $this->status, PDO::PARAM_INT);
            $stmt->bindParam(":id", $this->id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function eliminarUsuario() {
        try {
            $sql = "DELETE FROM usuario WHERE id=:id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":id", $this->id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    //Tipo Personal

    public function crearTipoPersonal() {
        try {
            $sql = "INSERT INTO tipo_personal (nombre) VALUES (:nombre)";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function editarTipoPersonal() {
        try {
            $sql = "UPDATE tipo_personal SET nombre=:nombre WHERE id=:id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":nombre", $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function eliminarTipoPersonal() {
        try {
            $sql = "DELETE FROM tipo_personal WHERE id=:id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true);
        } catch (PDOException $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
}
?>