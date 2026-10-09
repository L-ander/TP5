<?php
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/VendedorModel.php';

class VendedorController extends BaseController {
    
    //Funcion central
    public function ejecutar($accion, $datos) {
        $method = lcfirst($accion);

        if (method_exists($this, $method)) {
            return $this->$method($datos);
        } else {
            return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
        }
    }

    //Funcion auxiliar para validaciones
    private function validarDatos($datos, $modelo, $esEdicion = false) {
        if (!is_numeric($datos['cedula']) || $datos['cedula'] < 5000000) {
            return "La cédula debe contener un mínimo de siete dígitos";
        }
        
        $modelo->setCedula($datos['cedula']);
        $idAExcluir = $esEdicion ? $datos['id'] : null;
        if ($modelo->existeCedula($idAExcluir)) {
            return "La cédula {$datos['cedula']} ya se encuentra registrada en el sistema.";
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $datos['nombre'])) {
            return "El nombre solo puede contener letras (sin acentos).";
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['apellido'])) {
            return "El apellido contiene caracteres no válidos.";
        }

        if (!preg_match('/^(?:\+58|0)(?:412|414|424|416|426|422|251)\d{7}$/', $datos['telefono'])) {
            return "El teléfono no cumple con los formatos de operadoras válidas en el país.";
        }
        
        return null;
    }

    protected function listar($datos) {
        $modelo = new VendedorModel();
        $res = $modelo->listar();

        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al obtener la información']);
    }

    protected function crear($datos) {
        $modelo = new VendedorModel();
        
        $errorValidacion = $this->validarDatos($datos, $modelo, false);
        if ($errorValidacion) {
            return $this->jsonResponse(['success' => false, 'message' => $errorValidacion]);
        }

        $modelo->setCedula($datos['cedula']);
        $modelo->setNombre($datos['nombre']);
        $modelo->setApellido($datos['apellido']);
        $modelo->setTelefono($datos['telefono']);
        $modelo->setIdTipoPersonal($datos['id_tipo_personal']);
        $modelo->setStatus($datos['status']);

        $res = $modelo->crear();

        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Personal creado exitosamente']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al crear en BD']);
    }

    protected function editar($datos) {
        $modelo = new VendedorModel();
        
        $errorValidacion = $this->validarDatos($datos, $modelo, true);
        if ($errorValidacion) {
            return $this->jsonResponse(['success' => false, 'message' => $errorValidacion]);
        }

        $modelo->setId($datos['id']);
        $modelo->setCedula($datos['cedula']);
        $modelo->setNombre($datos['nombre']);
        $modelo->setApellido($datos['apellido']);
        $modelo->setTelefono($datos['telefono']);
        $modelo->setIdTipoPersonal($datos['id_tipo_personal']);
        $modelo->setStatus($datos['status']);

        $res = $modelo->editar();

        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Personal actualizado exitosamente']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al actualizar en BD']);
    }

    protected function eliminar($datos) {
        $modelo = new VendedorModel();
        $modelo->setId($datos['id']);
        
        $res = $modelo->eliminar();

        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Personal eliminado exitosamente']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar']);
    }

    protected function tipos_personal($datos) {
        $modelo = new VendedorModel();
        $res = $modelo->listarTiposPersonal();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al listar cargos']);
    }

    protected function roles($datos) {
        $modelo = new VendedorModel();
        $res = $modelo->listarRoles();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al listar roles']);
    }

    protected function personal_sin_usuario($datos) {
        $modelo = new VendedorModel();
        $res = $modelo->listarPersonalSinUsuario();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al listar personal sin usuario']);
    }

    protected function crear_usuario($datos) {
        $modelo = new VendedorModel();
        
        // Uso de setters en usuario
        $modelo->setId($datos['id_personal']);
        $modelo->setUsername($datos['username']);
        $modelo->setPassword($datos['password']);
        $modelo->setCodRol($datos['cod_rol']);
        $modelo->setStatus($datos['status']);

        $res = $modelo->crearUsuario();

        if ($res['success']) {
            if ($res['resultado'] === 'exito') {
                return $this->jsonResponse(['success' => true, 'message' => 'Usuario registrado']);
            } elseif ($res['resultado'] === 'duplicado') {
                return $this->jsonResponse(['success' => false, 'message' => 'El Username ya existe en el sistema.']);
            }
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error en el sistema']);
    }

    protected function listar_usuarios($datos) {
        $modelo = new VendedorModel();
        $res = $modelo->listarUsuarios();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al listar usuarios']);
    }

    protected function editar_usuario($datos) {
        $modelo = new VendedorModel();
        
        $modelo->setIdUsuario($datos['id_usuario']);
        $modelo->setUsername($datos['username']);
        $modelo->setPassword($datos['password']);
        $modelo->setCodRol($datos['cod_rol']);
        $modelo->setStatus($datos['status']);

        $res = $modelo->editarUsuario();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al actualizar usuario']);
    }

    protected function eliminar_usuario($datos) {
        $modelo = new VendedorModel();
        $modelo->setIdUsuario($datos['id']);
        
        $res = $modelo->eliminarUsuario();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Usuario eliminado (El personal sigue intacto)']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar usuario']);
    }

    protected function crear_tipo_personal($datos) {
        $modelo = new VendedorModel();
        $modelo->setNombre($datos['nombre']);
        
        $res = $modelo->crearTipoPersonal();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Cargo registrado exitosamente']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al registrar']);
    }

    protected function editar_tipo_personal($datos) {
        $modelo = new VendedorModel();
        $modelo->setId($datos['id']);
        $modelo->setNombre($datos['nombre']); 
        
        $res = $modelo->editarTipoPersonal();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Cargo actualizado exitosamente']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'Error al actualizar']);
    }

    protected function eliminar_tipo_personal($datos) {
        $modelo = new VendedorModel();
        $modelo->setId($datos['id']);
        
        $res = $modelo->eliminarTipoPersonal();
        
        if ($res['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Cargo eliminado']);
        }
        return $this->jsonResponse(['success' => false, 'message' => 'No se puede eliminar: el cargo está siendo usado por personal registrado']);
    }
}

// Punto de Entrada de las peticiones AJAX
if (isset($_POST['action'])) {
    $controller = new VendedorController();
    $controller->ejecutar($_POST['action'], $_POST);
}
?>