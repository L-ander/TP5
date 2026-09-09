<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Agregar Rol </h4>
            </div>
            <div class="modal-body">
                <form id="formulario">
                    <div class="row"> <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">shopping_basket</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombre">
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

-- --------------------------------------------------------

<div class="modal fade" id="modalEditarProducto" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Editar Rol </h4>
            </div>
            <div class="modal-body">
                <form id="formularioEditar">
                    <!-- Input oculto para conservar el ID del registro -->
                    <input type="hidden" id="idRol">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">shopping_basket</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombreEditar">
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



