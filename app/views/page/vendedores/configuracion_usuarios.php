<link rel="stylesheet" href="public/css/home.css">
<style>
    .config-tabs { margin-bottom: 20px; }
    .config-tabs > li > a { font-weight: bold; color: #555; }
    .config-tabs > li.active > a { color: #009688; }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Configuración de Usuarios y Accesos</h2>
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <ul class="nav nav-tabs config-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabCrearUsuario" data-toggle="tab"><i class="material-icons">person_add</i> Asignar Credenciales</a>
                            </li>
                            <li role="presentation">
                                <a href="#tabListadoUsuarios" data-toggle="tab"><i class="material-icons">list</i> Listado de Cuentas</a>
                            </li>
                            <li role="presentation">
                                <a href="#tabTiposPersonal" data-toggle="tab"><i class="material-icons">work</i> Cargos de Personal</a>
                            </li>
                        </ul>
                    </div>
                    <div class="body tab-content">

                        <!-- PESTAÑA: CREAR USUARIO -->
                        <div role="tabpanel" class="tab-pane fade in active" id="tabCrearUsuario">
                            <form id="formUsuario">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Seleccionar Personal (Sin Usuario Asignado)</label>
                                            <select class="form-control show-tick" name="id_personal" id="usuario_id_personal" required></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="username">Usuario (Username)</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="username" id="username" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password_usuario">Contraseña</label>
                                            <div class="form-line">
                                                <input type="password" class="form-control" name="password" id="password_usuario" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Rol del Sistema</label>
                                            <select class="form-control show-tick" name="cod_rol" id="usuario_cod_rol" required></select>
                                        </div>
                                        <div class="form-group">
                                            <label>Estado de la Cuenta</label>
                                            <select class="form-control show-tick" name="status" id="usuario_status" required>
                                                <option value="1">Activo (Permitir ingreso)</option>
                                                <option value="0">Bloqueado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right" style="margin-top: 15px;">
                                    <button type="button" class="btn btn-info waves-effect" id="btnGuardarUsuario">GUARDAR USUARIO</button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA: LISTADO DE USUARIOS -->
                        <div role="tabpanel" class="tab-pane fade" id="tabListadoUsuarios">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Personal Asociado</th>
                                            <th>Rol</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaUsuariosRegistrados"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- PESTAÑA: CARGOS DE PERSONAL -->
                        <div role="tabpanel" class="tab-pane fade" id="tabTiposPersonal">
                            <div style="margin-bottom: 15px;">
                                <button type="button" class="btn btn-success waves-effect" onclick="abrirModalTipoPersonal()">
                                    <i class="material-icons">add</i> NUEVO CARGO
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre del Cargo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaTiposPersonal"></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Editar Usuario (Se queda como modal para no salir de la pestaña de listado) -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modificar Credenciales</h4>
            </div>
            <div class="modal-body">
                <form id="formEditarUsuario">
                    <input type="hidden" name="id_usuario" id="edit_id_usuario">
                    <div class="form-group">
                        <label>Personal</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="edit_nombre_personal" readonly disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_username">Username</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="edit_username" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Nueva Contraseña</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="edit_password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select class="form-control" id="edit_usuario_cod_rol" required></select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="edit_usuario_status" required>
                            <option value="1">Activo</option>
                            <option value="0">Bloqueado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning waves-effect" id="btnActualizarUsuario">ACTUALIZAR</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tipo de Personal (Cargos) -->
<div class="modal fade" id="modalTipoPersonal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalTipo">Nuevo Cargo</h4>
            </div>
            <div class="modal-body">
                <form id="formTipoPersonal">
                    <input type="hidden" id="id_tipo_personal">
                    <div class="form-group">
                        <label>Nombre del Cargo</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="nombre_tipo_personal" placeholder="Ej: Empleado" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" onclick="guardarTipoPersonal()">GUARDAR</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<script src="resources/library/plugins/jquery/jquery.min.js"></script>
<script src="public/js/configuracion_usuarios.js"></script>