<!-- modal.php -->
<div class="modal fade" id="modalRol" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Registrar Rol y Permisos </h4>
            </div>
            <div class="modal-body">
                <!-- Formulario original de Rol -->
                <form id="formulario">
                    <h5 style="margin-bottom: 10px; font-weight: bold; color: #333;">1. Datos del Rol</h5>
                    <div class="row"> 
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">shopping_basket</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre del Rol" id="nombre">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success waves-effect" id="guardar" style="width: 100%;"> Guardar Rol </button>
                        </div>
                    </div>
                </form>

                <hr style="margin: 20px 0;">

                <!-- Bloque Transaccional de Módulos / Permisos -->
                <div id="contenedorPermisosNuevo" style="display: none;">
                    <h5 style="margin-bottom: 10px; font-weight: bold; color: #333;">2. Asignar Módulos (Permisos)</h5>
                    <input type="hidden" id="idRolReciente">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-line">
                                    <select class="form-control" id="selectModuloNuevo">
                                        <option value="">-- Seleccione Módulo --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary waves-effect" id="btnAgregarModuloNuevo" style="width: 100%;"> Agregar </button>
                        </div>
                    </div>

                    <!-- Pila de permisos agregados -->
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-12">
                            <ul class="list-group" id="listaModulosPilaNuevo" style="max-height: 200px; overflow-y: auto;">
                                <!-- Se llenará dinámicamente en pila -->
                            </ul>
                        </div>
                    </div>

                    <div class="text-right" style="margin-top: 15px;">
                        <button type="button" class="btn btn-success waves-effect" id="guardarPermisosNuevo"> Guardar Permisos </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="cerrar"> Cerrar </button>
            </div>
        </div>
    </div>
</div>

-- --------------------------------------------------------

<div class="modal fade" id="modalEditarRol" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Editar Rol y Permisos </h4>
            </div>
            <div class="modal-body">
                <!-- Formulario original de Edición de Rol -->
                <form id="formularioEditar">
                    <input type="hidden" id="idRol">
                    <h5 style="margin-bottom: 10px; font-weight: bold; color: #333;">1. Datos del Rol</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">shopping_basket</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre del Rol" id="nombreEditar">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success waves-effect" id="editar" style="width: 100%;"> Editar Rol </button>
                        </div>
                    </div>
                </form>

                <hr style="margin: 20px 0;">

                <!-- Bloque Transaccional de Módulos / Permisos en Edición -->
                <div>
                    <h5 style="margin-bottom: 10px; font-weight: bold; color: #333;">2. Asignar Módulos (Permisos)</h5>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-line">
                                    <select class="form-control" id="selectModuloEditar">
                                        <option value="">-- Seleccione Módulo --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary waves-effect" id="btnAgregarModuloEditar" style="width: 100%;"> Agregar </button>
                        </div>
                    </div>

                    <!-- Pila de permisos agregados en edición -->
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-12">
                            <ul class="list-group" id="listaModulosPilaEditar" style="max-height: 200px; overflow-y: auto;">
                                <!-- Se llenará dinámicamente en pila -->
                            </ul>
                        </div>
                    </div>

                    <div class="text-right" style="margin-top: 15px;">
                        <button type="button" class="btn btn-success waves-effect" id="guardarPermisosEditar"> Guardar Permisos </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="cerrarEditar"> Cerrar </button>
            </div>
        </div>
    </div>
</div>