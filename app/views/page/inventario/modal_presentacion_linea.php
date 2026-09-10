<div class="modal fade" id="modalPresentacionLinea" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="tituloPresentacionLinea">Gestión</h4>
            </div>
            <div class="modal-body">
                <form id="formPresentacionLinea">
                    <input type="hidden" id="plId">
                    <input type="hidden" id="plTabla">
                    
                    <div class="form-group" id="grupoPlSubcategoria" style="display: none;">
                        <label for="plSubcategoriaSelect">Subcategoría</label>
                        <div class="form-line">
                            <select class="form-control" id="plSubcategoriaSelect">
                                <option value="">Seleccione subcategoría</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="plNombre">Nombre / Contenido</label>
                        <div class="input-group" style="margin-bottom:0;">
                            <span class="input-group-addon">
                                <i class="material-icons">edit</i>
                            </span>
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Nombre / Contenido" id="plNombre" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-right" style="margin-top: 15px;">
                        <button type="submit" class="btn btn-success waves-effect">Guardar</button>
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>