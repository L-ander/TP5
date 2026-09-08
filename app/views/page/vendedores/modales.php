<div class="modal fade" id="modalPersonal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModal">Formulario de Personal</h4>
            </div>
            <div class="modal-body">
                <form id="formPersonal">
                    <input type="hidden" id="personal_id" name="id">
                    
                    <label for="cedula">Cédula</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" class="form-control" name="cedula" id="cedula" placeholder="Ej: 25123456" required>
                        </div>
                        <span class="error-msg" id="error-cedula">La cedula debe de ser mayor de 5000000 en adelante</span>
                    </div>

                    <label for="nombre">Nombre</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Solo letras sin acentos" required>
                        </div>
                        <span class="error-msg" id="error-nombre">Solo se permiten letras (sin acentos ni caracteres especiales)</span>
                    </div>

                    <label for="apellido">Apellido</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="apellido" id="apellido" placeholder="Solo letras" required>
                        </div>
                        <span class="error-msg" id="error-apellido">Solo se permiten letras (acentos permitidos)</span>
                    </div>

                    <label for="telefono">Teléfono</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Ej: 04121234567 o +584141234567">
                        </div>
                        <span class="error-msg" id="error-telefono">Formato inválido. Debe iniciar con 04 o +58 y tener los dígitos correctos.</span>
                    </div>
                    
                    <label>Tipo de Personal</label>
                    <div class="form-group">
                        <select class="form-control show-tick" name="id_tipo_personal" id="id_tipo_personal" required></select>
                        <span class="error-msg" id="error-tipo">Debe seleccionar un cargo</span>
                    </div>

                    <label>Estado Laboral</label>
                    <div class="form-group">
                        <select class="form-control show-tick" name="status" id="status" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo / Retirado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btnGuardarPersonal" disabled onclick="guardarPersonal()">GUARDAR</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar Credenciales de Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="formUsuario">
                    <div class="form-group">
                        <label>Seleccionar Personal</label>
                        <select class="form-control show-tick" name="id_personal" id="usuario_id_personal" required></select>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;">Debe seleccionar un empleado.</span>
                        <small class="text-muted" style="display:block; margin-top:5px;">Solo se muestra el personal activo que aún no tiene cuenta.</small>
                    </div>

                    <div class="form-group">
                        <label for="username">Usuario (Username)</label>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" id="username" autocomplete="off" required>
                        </div>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;"></span>
                    </div>

                    <div class="form-group">
                        <label for="password_usuario">Contraseña</label>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" id="password_usuario" autocomplete="off" required>
                        </div>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;"></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Rol del Sistema</label>
                        <select class="form-control show-tick" name="cod_rol" id="usuario_cod_rol" required></select>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;">Debe asignar un rol.</span>
                    </div>

                    <div class="form-group">
                        <label>Estado de la Cuenta</label>
                        <select class="form-control show-tick" name="status" id="usuario_status" required>
                            <option value="1">Activo (Permitir ingreso)</option>
                            <option value="0">Bloqueado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info waves-effect" id="btnGuardarUsuario" disabled onclick="guardarUsuario()">CREAR USUARIO</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalListadoUsuarios" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Usuarios del Sistema Registrados</h4>
            </div>
            <div class="modal-body table-responsive">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modificar Credenciales de Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="formEditarUsuario">
                    <input type="hidden" name="id_usuario" id="edit_id_usuario">
                    
                    <div class="form-group">
                        <label>Personal (No modificable)</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="edit_nombre_personal" readonly disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_username">Usuario (Username)</label>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" id="edit_username" autocomplete="off" required>
                        </div>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;"></span>
                    </div>

                    <div class="form-group">
                        <label for="edit_password">Contraseña</label>
                        <div class="form-line">
                            <input type="text" class="form-control" name="password" id="edit_password" autocomplete="off" required>
                        </div>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;"></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Rol del Sistema</label>
                        <select class="form-control show-tick" name="cod_rol" id="edit_usuario_cod_rol" required></select>
                        <span class="error-msg" style="display:none; color:#e53935; font-size:12px; margin-top:4px;">Debe asignar un rol.</span>
                    </div>

                    <div class="form-group">
                        <label>Estado de la Cuenta</label>
                        <select class="form-control show-tick" name="status" id="edit_usuario_status" required>
                            <option value="1">Activo (Permitir ingreso)</option>
                            <option value="0">Bloqueado / Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning waves-effect" id="btnActualizarUsuario" disabled onclick="guardarEdicionUsuario()">ACTUALIZAR USUARIO</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>