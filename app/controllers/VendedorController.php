<?php
require_once __DIR__ . '/../models/VendedorModel.php';

class VendedorController {
    
    // Función auxiliar privada para limpiar código
    private function validarDatos($datos, $modelo, $esEdicion = false) {
        // Validar Cedula
        if (!is_numeric($datos['cedula']) || $datos['cedula'] < 5000000) {
            return "La cédula debe ser un número mayor a 5.000.000.";
        }
        
        // Validar duplicado
        $idAExcluir = $esEdicion ? $datos['id'] : null;
        if ($modelo->existeCedula($datos['cedula'], $idAExcluir)) {
            return "La cédula {$datos['cedula']} ya se encuentra registrada en el sistema.";
        }

        // Validar Nombre (Sin acentos, ni numeros, ni simbolos)
        if (!preg_match('/^[a-zA-Z\s]+$/', $datos['nombre'])) {
            return "El nombre solo puede contener letras (sin acentos).";
        }

        // Validar Apellido (Acentos permitidos, pero sin numeros ni simbolos)
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['apellido'])) {
            return "El apellido contiene caracteres no válidos.";
        }

        // Validar Telefono (+58 o 0, prefijo valido, 7 dígitos)
        if (!preg_match('/^(?:\+58|0)(?:412|414|424|416|426|422|251)\d{7}$/', $datos['telefono'])) {
            return "El teléfono no cumple con los formatos de operadoras válidas en el país.";
        }
        
        return null;
    }

    public function ejecutar($accion, $datos) {
        $modelo = new VendedorModel();
        switch ($accion) {
            case 'listar':
                return json_encode(['success' => true, 'data' => $modelo->listar()]);
            
            case 'crear':
                $errorValidacion = $this->validarDatos($datos, $modelo, false);
                if ($errorValidacion) {
                    return json_encode(['success' => false, 'message' => $errorValidacion]);
                }
                $ok = $modelo->crear($datos);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Personal creado exitosamente' : 'Error al crear en BD']);
            
            case 'editar':
                $errorValidacion = $this->validarDatos($datos, $modelo, true);
                if ($errorValidacion) {
                    return json_encode(['success' => false, 'message' => $errorValidacion]);
                }
                $ok = $modelo->editar($datos);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Personal actualizado exitosamente' : 'Error al actualizar en BD']);
            
            case 'eliminar':
                $ok = $modelo->eliminar($datos['id']);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Personal eliminado exitosamente' : 'Error al eliminar']);
                                
            case 'tipos_personal':
                return json_encode(['success' => true, 'data' => $modelo->listarTiposPersonal()]);
                
            case 'roles':
                return json_encode(['success' => true, 'data' => $modelo->listarRoles()]);
                
            case 'personal_sin_usuario':
                return json_encode(['success' => true, 'data' => $modelo->listarPersonalSinUsuario()]);
                
            case 'crear_usuario':
                $resultado = $modelo->crearUsuario($datos);
                if ($resultado === 'exito') {
                    return json_encode(['success' => true, 'message' => 'Usuario registrado']);
                } elseif ($resultado === 'duplicado') {
                    return json_encode(['success' => false, 'message' => 'Usuario ya existe']); // ¡Aquí cumples el diagrama!
                } else {
                    return json_encode(['success' => false, 'message' => 'Error en el sistema']);
                }

            case 'listar_usuarios':
                return json_encode(['success' => true, 'data' => $modelo->listarUsuarios()]);
                
            case 'editar_usuario':
                $ok = $modelo->editarUsuario($datos);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Usuario actualizado exitosamente' : 'Error al actualizar usuario']);
                
            case 'eliminar_usuario':
                $ok = $modelo->eliminarUsuario($datos['id']);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Usuario eliminado (El personal sigue intacto)' : 'Error al eliminar usuario']);
                
            default:
                return json_encode(['success' => false, 'message' => 'Acción no reconocida']);
        }
    }
}

if (isset($_POST['action'])) {
    $controller = new VendedorController();
    echo $controller->ejecutar($_POST['action'], $_POST);
}
?>