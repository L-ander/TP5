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
                <button type="button" class="btn btn-success waves-effect" id="btnGuardarTipoPersonal">GUARDAR</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>
