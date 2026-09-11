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
                        <span class="error-msg" id="error-cedula">La cédula debe contener un mínimo de siete dígitos</span>
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
