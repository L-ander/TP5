<?php
require_once '../core/BaseController.php';
require_once '../models/UbicacionModel.php';

class UbicacionController extends BaseController {

    public function ejecutar($action, $data) {
        $method = lcfirst($action);
        $accionesPermitidas = [
            'listarCiudades',
            'cargarEstados',
            'cargarMunicipios',
            'guardarCiudad',
            'guardarEstado',
            'guardarMunicipio',
            'modificarCiudad',
            'borrarCiudad'
        ];
        if (in_array($method, $accionesPermitidas, true) && method_exists($this, $method)) {
            return $this->$method($data);
        } else {
            return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
        }
    }

    protected function listarCiudades($data) {
    $model = new UbicacionModel();
    $res = $model->listarCiudadesCompletas();
    return $this->jsonResponse($res);
}

    protected function cargarEstados($data) {
        $model = new UbicacionModel();
        $res = $model->listarEstados();
        return $this->jsonResponse($res);
    }

    protected function cargarMunicipios($data) {
        $model = new UbicacionModel();
        $res = $model->listarMunicipiosPorEstado($data['id_estado']);
        return $this->jsonResponse($res);
    }

    protected function guardarCiudad($data) {
        $nombre = $this->texto($data, 'nombre');
        $idMunicipio = $data['id_municipio'] ?? null;
        $idEstado = $data['id_estado'] ?? null;
        $errorValidacion = $this->validarNombreUbicacion($nombre, 'La ciudad', 26);
        if ($errorValidacion !== null) {
            return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 400);
        }
        if (!$this->esIdPositivo($idMunicipio) || !$this->esIdPositivo($idEstado)) {
            return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar un estado y un municipio válidos.'], 400);
        }

        $model = new UbicacionModel();
        $referencia = $model->verificarMunicipioEstado((int)$idMunicipio, (int)$idEstado);
        if (empty($referencia['success'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar el estado y municipio seleccionados.'], 500);
        }
        if (empty($referencia['existe'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'El municipio no pertenece al estado seleccionado.'], 400);
        }

        $model->setNombre($nombre);
        $model->setIdMunicipio((int)$idMunicipio);
        $model->setIdEstado((int)$idEstado);
        $res = $model->crearCiudad();
        return $this->respuestaGuardado($res, 'No se pudo guardar la ciudad.');
    }

    protected function guardarEstado($data) {
        $nombre = $this->texto($data, 'nombre');
        $errorValidacion = $this->validarNombreUbicacion($nombre, 'El estado', 50);
        if ($errorValidacion !== null) {
            return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 400);
        }

        $model = new UbicacionModel();
        $model->setNombre($nombre);
        $res = $model->crearEstado();
        return $this->respuestaGuardado($res, 'No se pudo guardar el estado.');
    }

    protected function guardarMunicipio($data) {
        $nombre = $this->texto($data, 'nombre');
        $idEstado = $data['id_estado'] ?? null;
        $errorValidacion = $this->validarNombreUbicacion($nombre, 'El municipio', 50);
        if ($errorValidacion !== null) {
            return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 400);
        }
        if (!$this->esIdPositivo($idEstado)) {
            return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar un estado válido.'], 400);
        }

        $model = new UbicacionModel();
        $estado = $model->verificarEstado((int)$idEstado);
        if (empty($estado['success'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar el estado seleccionado.'], 500);
        }
        if (empty($estado['existe'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'El estado seleccionado no es válido.'], 400);
        }

        $model->setNombre($nombre);
        $model->setIdEstado((int)$idEstado);
        $res = $model->crearMunicipio();
        return $this->respuestaGuardado($res, 'No se pudo guardar el municipio.');
    }

    protected function modificarCiudad($data) {
        $id = $data['id'] ?? null;
        $nombre = $this->texto($data, 'nombre');
        $idMunicipio = $data['id_municipio'] ?? null;
        $idEstado = $data['id_estado'] ?? null;
        if (!$this->esIdPositivo($id)) {
            return $this->jsonResponse(['success' => false, 'msj' => 'ID de ciudad no válido.'], 400);
        }
        $errorValidacion = $this->validarNombreUbicacion($nombre, 'La ciudad', 26);
        if ($errorValidacion !== null) {
            return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 400);
        }
        if (!$this->esIdPositivo($idMunicipio) || !$this->esIdPositivo($idEstado)) {
            return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar un estado y un municipio válidos.'], 400);
        }

        $model = new UbicacionModel();
        $ciudad = $model->verificarCiudad((int)$id);
        if (empty($ciudad['success'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la ciudad.'], 500);
        }
        if (empty($ciudad['existe'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'La ciudad que intenta editar no existe.'], 400);
        }

        $referencia = $model->verificarMunicipioEstado((int)$idMunicipio, (int)$idEstado);
        if (empty($referencia['success'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar el estado y municipio seleccionados.'], 500);
        }
        if (empty($referencia['existe'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'El municipio no pertenece al estado seleccionado.'], 400);
        }

        $model->setId((int)$id);
        $model->setNombre($nombre);
        $model->setIdMunicipio((int)$idMunicipio);
        $model->setIdEstado((int)$idEstado);
        $res = $model->editarCiudad();
        return $this->respuestaGuardado($res, 'No se pudo actualizar la ciudad.');
    }

    private function texto($data, $campo) {
        $valor = $data[$campo] ?? '';
        return is_scalar($valor) ? trim((string)$valor) : '';
    }

    private function esIdPositivo($valor) {
        return (is_string($valor) || is_int($valor))
            && preg_match('/^[1-9][0-9]*$/D', (string)$valor) === 1;
    }

    private function validarNombreUbicacion($nombre, $tipo, $limite) {
        $longitud = preg_match_all('/./us', $nombre);
        if ($nombre === '' || $longitud === false
            || $longitud > $limite
            || preg_match('/^[\p{L}\p{M}][\p{L}\p{M} .\'-]*$/u', $nombre) !== 1) {
            return $tipo . ' debe contener solo letras y espacios y no exceder ' . $limite . ' caracteres.';
        }

        return null;
    }

    private function respuestaGuardado($resultado, $mensaje) {
        if (empty($resultado['success'])) {
            return $this->jsonResponse(['success' => false, 'msj' => $mensaje], 500);
        }

        return $this->jsonResponse(['success' => true]);
    }

    protected function borrarCiudad($data) {
        $model = new UbicacionModel();
        $model->setId($data['id']);
        $res = $model->eliminarCiudad();
        return $this->jsonResponse($res);
    }
}

if (isset($_POST['action'])) {
    $controller = new UbicacionController();
    $controller->ejecutar($_POST['action'], $_POST);
}