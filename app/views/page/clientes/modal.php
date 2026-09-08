<div class="modal fade" id="modal" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title"> Agregar Cliente </h4>
            </div>
            <div class="modal-body">
                <form id="formulario">
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">person</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombre">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">person_outline</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Apellido" id="apellido">
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">wc</i></span>
                                <div class="form-line">
                                    <select class="form-control" id="sexo">
                                        <option value="">-- Seleccione Sexo --</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">location_city</i></span>
                                <div class="form-line">
                                    <select class="form-control" id="id_ciudad">
                                        <option value="">-- Seleccione Ciudad --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">home</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Dirección" id="direccion">
                                </div>
                            </div>
                        </div>
                        </div></div>

                    <div class="row" id="slots_geograficos" style="display: none;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">location_city</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="municipio" placeholder="Municipio" readonly style="background-color: #f5f5f5; padding-left: 5px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">map</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="estado" placeholder="Estado" readonly style="background-color: #f5f5f5; padding-left: 5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="guardar"> Guardar </button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="cerrar"> Cerrar </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title"> Editar Cliente </h4>
            </div>
            <div class="modal-body">
                <form id="formularioEditar">
                    <input type="hidden" id="idEditar"> 
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">person</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombreEditar">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">person_outline</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Apellido" id="apellidoEditar">
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">wc</i></span>
                                <div class="form-line">
                                    <select class="form-control" id="sexoEditar">
                                        <option value="">-- Seleccione Sexo --</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">location_city</i></span>
                                <div class="form-line">
                                    <select class="form-control" id="id_ciudadEditar">
                                        <option value="">-- Seleccione Ciudad --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">home</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Dirección" id="direccionEditar">
                                </div>
                            </div>
                        </div>
                        </div>

                    <div class="row" id="slots_geograficos_editar" style="display: none;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">location_city</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="municipioEditar" placeholder="Municipio" readonly style="background-color: #f5f5f5; padding-left: 5px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="material-icons">map</i></span>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="estadoEditar" placeholder="Estado" readonly style="background-color: #f5f5f5; padding-left: 5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="editar"> Editar </button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="cerrarEditar"> Cerrar </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCiudad" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-col-blue-grey">
                <h4 class="modal-title">Agregar Nueva Ciudad</h4>
            </div>
            <div class="modal-body">
                <form id="formCiudad">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="id_estado_mod"></select>
                        <button type="button" class="btn btn-xs btn-info waves-effect" data-toggle="modal" data-target="#modalEstado" style="margin-top: 5px;">
                            <i class="material-icons" style="font-size: 14px;">add</i> Crear Nuevo Estado
                        </button>
                    </div>
                    <div class="form-group">
                        <label>Municipio</label>
                        <select class="form-control" id="id_municipio_mod">
                            <option value="">-- Seleccione un Estado Primero --</option>
                        </select>
                        <button type="button" class="btn btn-xs btn-info waves-effect" data-toggle="modal" data-target="#modalMunicipio" style="margin-top: 5px;">
                            <i class="material-icons" style="font-size: 14px;">add</i> Crear Nuevo Municipio
                        </button>
                    </div>
                    <div class="form-group">
                        <label>Nombre de la Ciudad</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="nombre_ciudad_mod" placeholder="Ej. Barquisimeto">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btnGuardarCiudad">Guardar</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="btnCerrarModCiudad">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarCiudad" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-col-blue-grey">
                <h4 class="modal-title">Editar Ciudad</h4>
            </div>
            <div class="modal-body">
                <form id="formEditarCiudad">
                    <input type="hidden" id="idCiudadEdit">
                    
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="id_estado_edit_mod"></select>
                    </div>
                    <div class="form-group">
                        <label>Municipio</label>
                        <select class="form-control" id="id_municipio_edit_mod"></select>
                    </div>

                    <div class="form-group">
                        <label>Nombre de la Ciudad</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="nombre_ciudad_edit_mod">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btnActualizarCiudad">Actualizar</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="btnCerrarEditModCiudad">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEstado" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-col-blue-grey">
                <h4 class="modal-title">Nuevo Estado</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre del Estado</label>
                    <div class="form-line">
                        <input type="text" class="form-control" id="nombre_estado_nuevo">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btnGuardarEstadoExpress">Guardar</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMunicipio" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header modal-col-blue-grey">
                <h4 class="modal-title">Nuevo Municipio</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Seleccione el Estado</label>
                    <select class="form-control" id="id_estado_muni_nuevo"></select>
                </div>
                <div class="form-group">
                    <label>Nombre del Municipio</label>
                    <div class="form-line">
                        <input type="text" class="form-control" id="nombre_municipio_nuevo">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btnGuardarMunicipioExpress">Guardar</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>