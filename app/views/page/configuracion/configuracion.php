<link rel="stylesheet" href="public/css/home.css">
<style>
    .config-tabs { margin-bottom: 20px; }
    .config-tabs > li > a { font-weight: bold; color: #555; }
    .config-tabs > li.active > a { color: #009688; }
    .config-toolbar { margin-bottom: 15px; }
    .config-toolbar .btn { margin-right: 8px; }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Configuración</h2>
        </div>

        <div class="card">
            <div class="header">
                <ul class="nav nav-tabs config-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#configMedidas" data-toggle="tab"><i class="material-icons">straighten</i> Unidades de medida</a></li>
                    <li role="presentation"><a href="#configCategorias" data-toggle="tab"><i class="material-icons">category</i> Categorías</a></li>
                    <li role="presentation"><a href="#configSubcategorias" data-toggle="tab"><i class="material-icons">account_tree</i> Subcategorías</a></li>
                    <li role="presentation"><a href="vendedores#usuarios"><i class="material-icons">manage_accounts</i> Usuario / Personal</a></li>
                </ul>
            </div>
            <div class="body tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="configMedidas">
                    <div class="config-toolbar"><button class="btn btn-success js-nueva-config" data-tabla="unidad_medida"><i class="material-icons">add</i> Nueva unidad</button></div>
                    <div class="table-responsive"><table class="table table-bordered table-striped" id="tablaMedidas"><thead><tr><th>ID</th><th>Unidad</th><th>Acciones</th></tr></thead><tbody></tbody></table></div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="configCategorias">
                    <div class="config-toolbar"><button class="btn btn-success js-nueva-config" data-tabla="categoria"><i class="material-icons">add</i> Nueva categoría</button></div>
                    <div class="table-responsive"><table class="table table-bordered table-striped" id="tablaCategorias"><thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead><tbody></tbody></table></div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="configSubcategorias">
                    <div class="config-toolbar"><button class="btn btn-success js-nueva-config" data-tabla="subcategoria"><i class="material-icons">add</i> Nueva subcategoría</button></div>
                    <div class="table-responsive"><table class="table table-bordered table-striped" id="tablaSubcategorias"><thead><tr><th>ID</th><th>Categoría</th><th>Subcategoría</th><th>Acciones</th></tr></thead><tbody></tbody></table></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalConfiguracion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header"><h4 class="modal-title" id="tituloConfiguracion">Nuevo registro</h4></div>
        <div class="modal-body">
            <form id="formConfiguracion">
                <input type="hidden" id="configId"><input type="hidden" id="configTabla">
                <div id="grupoCategoria" class="form-group" style="display:none;"><label for="id_categoria">Categoría</label><select class="form-control" id="id_categoria" required></select></div>
                <div class="form-group"><label for="configNombre">Nombre</label><input type="text" class="form-control" id="configNombre" maxlength="50" required></div>
            </form>
        </div>
        <div class="modal-footer"><button type="submit" form="formConfiguracion" class="btn btn-success">GUARDAR</button><button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button></div>
    </div></div>
</div>
<script src="resources/library/plugins/jquery/jquery.min.js"></script>
<script src="public/js/configuracion.js"></script>
