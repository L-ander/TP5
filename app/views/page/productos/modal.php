<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Agregar Producto </h4>
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

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">list</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="id_linea">
                                        <option value="">-- Seleccione Línea --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">line_weight</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="presentacion">
                                        <option value="">-- Seleccione Presentación --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div> <div class="row"> <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">straighten</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="id_medida">
                                        <option value="">-- Unidad de Medida --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">attach_money</i>
                                </span>
                                <div class="form-line">
                                    <input type="number" step="0.01" class="form-control" placeholder="Precio" id="precio">
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
    <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Editar Producto </h4>
            </div>
            <div class="modal-body">
                <form id="formularioEditar">
                    <input type="hidden" id="idProducto">

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

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">list</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="id_lineaEditar">
                                        <option value="">-- Seleccione Línea --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">line_weight</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="presentacionEditar">
                                        <option value="">-- Seleccione Presentación --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div> <div class="row">
                        
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">straighten</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="id_medidaEditar">
                                        <option value="">-- Unidad de Medida --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">attach_money</i>
                                </span>
                                <div class="form-line">
                                    <input type="number" step="0.01" class="form-control" placeholder="Precio" id="precioEditar">
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




--------------------------------------------------------------------------------------------------------------------------
<div class="modal fade" id="modalCategoria" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Agregar Linea de Producto </h4>
            </div>
            <div class="modal-body">
                <form id="formularioPresentacion">


                
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">list</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="id_subcategoria">
                                        <option value="">-- Seleccione Línea --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    <div class="row"> <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">shopping_basket</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombrePresentacion">
                                </div>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="guardarPresentacion"> Guardar </button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="cerrarPresentacion"> Cerrar </button>
            </div>
        </div>
    </div>
</div>

-- --------------------------------------------------------

<div class="modal fade" id="modalEditarCategoria" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="defaultModalLabel"> Editar Categoria </h4>
            </div>
            <div class="modal-body">
                <form id="formularioEditar">
                    <input type="hidden" id="idLinea">

                    <div class="row">



                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">list</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control" id="id_subcategoriaEditar">
                                        <option value="">-- Seleccione Línea --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">shopping_basket</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombreEditarSubcategoria">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="editarSubcategoria"> Editar </button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal" id="cerrarEditarSubcategoria"> Cerrar </button>
            </div>
        </div>
    </div>
</div>