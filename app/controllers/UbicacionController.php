<?php
require_once '../core/BaseController.php';
require_once '../models/UbicacionModel.php';

class UbicacionController extends BaseController {

    public function ejecutar($action, $data) {
        $method = lcfirst($action);
        if (method_exists($this, $method)) {
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
        $model = new UbicacionModel();
        $model->setNombre($data['nombre']);
        $model->setIdMunicipio($data['id_municipio']);
        $model->setIdEstado($data['id_estado']);
        $res = $model->crearCiudad();
        return $this->jsonResponse($res);
    }
    protected function guardarEstado($data) {
        $model = new UbicacionModel();
        $model->setNombre($data['nombre']);
        $res = $model->crearEstado();
        return $this->jsonResponse($res);
    }

    protected function guardarMunicipio($data) {
        $model = new UbicacionModel();
        $model->setNombre($data['nombre']);
        $model->setIdEstado($data['id_estado']);
        $res = $model->crearMunicipio();
        return $this->jsonResponse($res);
    }

    protected function modificarCiudad($data) {
        $model = new UbicacionModel();
        $model->setId($data['id']);
        $model->setNombre($data['nombre']);
        $model->setIdMunicipio($data['id_municipio']);
        $model->setIdEstado($data['id_estado']);
        $res = $model->editarCiudad();
        return $this->jsonResponse($res);
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