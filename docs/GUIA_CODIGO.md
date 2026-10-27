# Guia de estudio del codigo

Este documento explica como esta armado el sistema para que puedas estudiar el proyecto sin tener que adivinar que hace cada archivo.

## 1. Flujo general del sistema

El punto de entrada principal es `index.php`.

Ese archivo carga la aplicacion y termina usando `app/controllers/enlacesController.php`, que decide si el usuario puede entrar o debe volver al login.

El flujo normal es:

1. El usuario entra al sistema.
2. `index.php` carga el controlador principal.
3. `EnlacesController::run()` inicia la sesion y valida si el usuario esta logueado.
4. Si el usuario no inicio sesion, solo puede ver `index`, que es el login.
5. Si el usuario inicio sesion, se carga `app/template/template.php`.
6. Dentro del template se llama a `EnlacesController::enlacesControl()`.
7. `enlacesControl()` usa `Router` para saber que vista debe incluir.
8. Si la ruta usa menu, se incluye `app/views/page/menu.php`.
9. Finalmente se incluye la vista solicitada.

## 2. Configuracion principal

### `config/config.php`

Define la configuracion base del sistema.

Lo mas importante es:

- Inicia la sesion si no existe.
- Define la base de datos por defecto.
- Crea el arreglo `$config`, que contiene los datos de conexion:
  - driver
  - host
  - puerto
  - nombre de base de datos
  - usuario
  - clave

### `config/conex.php`

Contiene la clase `Conexion`.

Esta clase centraliza la conexion con la base de datos usando PDO.

Metodos importantes:

- `conectar()`: devuelve una conexion PDO a la base actual.
- `conexionVieja()`: conecta con una base antigua, aunque actualmente casi no se usa.

Cada modelo usa `Conexion::conectar()` para consultar o modificar la base de datos.

### `config/routes.php`

Define las rutas internas del sistema.

Cada entrada tiene:

- `vista`: archivo que se va a cargar.
- `menu`: indica si se debe mostrar el menu lateral.
- `roles`: lista de roles que pueden entrar.

Regla actual:

- Rol `1`: administrador.
- Rol `2`: vendedor.

Ejemplo:

```php
'clientes' => [
    'vista' => 'clientes/clientes.php',
    'menu'  => true,
    'roles' => [1, 2]
]
```

Eso significa:

- La ruta `clientes` carga `app/views/page/clientes/clientes.php`.
- Muestra el menu.
- Puede entrar administrador y vendedor.

## 3. Router y control de permisos

### `app/core/Router.php`

El router recibe una accion, por ejemplo `clientes`, `producto` o `reportes`.

Luego:

1. Busca esa accion en `config/routes.php`.
2. Si no existe, carga `error.php`.
3. Si existe, revisa si el rol del usuario esta permitido.
4. Si no tiene permiso, carga `denied.php`.
5. Si tiene permiso, devuelve la vista que debe cargarse.

Este archivo evita que un vendedor escriba manualmente una URL como `producto` y entre sin permiso.

### `app/core/BaseController.php`

Es una clase base para controladores.

Contiene metodos comunes:

- `jsonResponse()`: responde datos en JSON y termina la ejecucion.
- `redirect()`: redirige a otra pagina.
- `startSession()`: inicia la sesion y le da duracion extendida.

## 4. Login y sesiones

### `app/views/page/login.php`

Es la pantalla visual del login.

Tiene los campos:

- usuario
- password

### `public/js/login.js`

Controla el envio del login por AJAX.

Funcion principal:

- `ingresar()`: toma usuario y clave, los envia a `app/controllers/login.php` y procesa la respuesta.

Si el login es correcto, redirige a `home`.

Si el usuario esta inactivo, muestra:

```text
Usuario inactivo
```

### `app/controllers/login.php`

Recibe la peticion AJAX del login.

Funcion principal:

- `iniciarSesion()`

Hace esto:

1. Valida que lleguen usuario y password.
2. Consulta el usuario en `loginModel`.
3. Verifica cedula y password.
4. Si el usuario tiene `estatus != 1`, bloquea el acceso.
5. Si todo esta bien, guarda datos en `$_SESSION`.

Variables importantes de sesion:

- `$_SESSION['id']`
- `$_SESSION['cedula']`
- `$_SESSION['nombre']`
- `$_SESSION['apellido']`
- `$_SESSION['codrol']`
- `$_SESSION['session']`

### `app/models/login.php`

Consulta la tabla `usuarios` por cedula.

Devuelve los datos del usuario encontrado para que el controlador valide la clave y el estado.

## 5. Menu lateral

### `app/views/page/menu.php`

Muestra el menu segun el rol del usuario.

Reglas actuales:

- Administrador `codrol = 1`:
  - Home
  - Clientes
  - Vendedores
  - Productos
  - Ordenes
  - Pedidos
  - Reportes
  - Salir

- Vendedor `codrol = 2`:
  - Home
  - Clientes
  - Ordenes
  - Pedidos
  - Salir

Variables importantes:

```php
$rolUsuario = isset($_SESSION['codrol']) ? (int)$_SESSION['codrol'] : 0;
$esAdministrador = $rolUsuario === 1;
$esVendedor = $rolUsuario === 2;
```

## 6. Home

### `app/views/page/home.php`

Pantalla principal despues del login.

Muestra indicadores:

- Usuarios activos.
- Clientes registrados.
- Productos.
- Pedidos activos.

### `app/models/HomeModel.php`

Calcula los indicadores del home.

Consultas principales:

- Usuarios activos: `usuarios WHERE estatus = 1`
- Clientes registrados: total de `cliente`
- Productos: total de `producto`
- Pedidos activos: `pedido WHERE id_estatus = 1`

## 7. Clientes

### `app/views/page/clientes/clientes.php`

Pantalla del modulo de clientes.

Permite:

- Listar clientes.
- Crear cliente.
- Editar cliente.
- Eliminar cliente.

Usa AJAX para comunicarse con:

```text
app/controllers/ClienteController.php
```

Funciones JavaScript importantes:

- `listarClientes()`
- `abrirModalCrear()`
- `abrirModalEditar(cliente)`
- `guardarCliente()`
- `eliminarCliente(id, nombreCompleto)`

### `app/controllers/ClienteController.php`

Recibe las acciones del modulo clientes.

Acciones:

- `listar`
- `crear`
- `editar`
- `eliminar`

Cada accion llama a `ClienteModel`.

### `app/models/ClienteModel.php`

Trabaja directamente con la tabla `cliente`.

Metodos:

- `listar()`: trae todos los clientes.
- `crear($data)`: inserta un cliente.
- `editar($data)`: actualiza un cliente.
- `eliminar($id)`: elimina un cliente.

## 8. Vendedores

### `app/views/page/vendedores/vendedores.php`

Pantalla para administrar usuarios/vendedores.

Permite:

- Listar vendedores.
- Crear vendedor.
- Editar vendedor.
- Eliminar vendedor.

Usa AJAX hacia:

```text
app/controllers/VendedorController.php
```

### `app/controllers/VendedorController.php`

Procesa acciones del modulo vendedores.

Acciones:

- `listar`
- `crear`
- `editar`
- `eliminar`

### `app/models/VendedorModel.php`

Trabaja con la tabla `usuarios`.

Importante:

- En la base el campo se llama `estatus`.
- En la vista se usa `status`.
- Por eso el modelo hace un alias: `estatus AS status`.

Metodos:

- `listar()`
- `crear($data)`
- `editar($data)`
- `eliminar($id)`

## 9. Productos

### `app/views/page/productos/productos.php`

Pantalla principal del modulo productos.

Contiene pestañas para:

- Productos.
- Nombres base de producto.
- Presentaciones.

Incluye:

```php
app/views/page/productos/modal.php
```

### `app/views/page/productos/modal.php`

Contiene los modales para:

- Crear/editar producto.
- Crear nombre base.
- Crear presentacion.

### `public/js/productos.js`

Maneja toda la interaccion del modulo productos.

Funciones importantes:

- `cargarProductos()`
- `cargarNombres()`
- `cargarPresentaciones()`
- `cargarSelects()`
- `guardarProducto()`
- `editarProducto(id)`
- `eliminarProducto(id)`
- `guardarNombre()`
- `guardarPresentacion()`

### `app/controllers/ProductoController.php`

Controlador AJAX para productos.

Usa una forma dinamica:

```php
$method = lcfirst($action);
```

Eso significa que si llega `action = listar`, llama al metodo `listar()`.

Acciones principales:

- `listar`
- `crear`
- `consultar`
- `editar`
- `eliminar`
- `listarNombresSelect`
- `crearNombre`
- `eliminarNombre`
- `listarPresentacionesSelect`
- `crearPresentacion`
- `eliminarPresentacion`

### `app/models/ProductoModel.php`

Trabaja con:

- `producto`
- `nombre_producto`
- `presentacion`

Tambien contiene `ensureSchema()`, que crea tablas si no existen y ajusta la precision del precio.

## 10. Ordenes

### Idea del modulo

Una orden es la cabecera de una venta.

Contiene:

- cliente
- vendedor
- fecha
- total
- estatus

Al crear una orden:

- El total nace en `0`.
- El estatus nace en `Pendiente`.

Al editar una orden:

- Se puede cambiar cliente.
- Se puede cambiar vendedor.
- Se puede cambiar fecha.
- Se puede cambiar estatus.
- No se edita el total manualmente.

### `app/views/page/ordenes/ordenes.php`

Pantalla visual del modulo ordenes.

Funciones JavaScript importantes:

- `cargarCombos()`: carga clientes, vendedores y estatus.
- `listarOrdenes()`: lista las ordenes.
- `abrirModalCrear()`: abre formulario para crear.
- `abrirModalEditar(id)`: abre formulario para editar.
- `eliminarOrden(id)`: elimina una orden.
- `guardarOrden()`: envia datos al controlador.

### `app/controllers/OrdenController.php`

Recibe acciones del modulo ordenes.

Acciones:

- `listar`
- `crear`
- `editar`
- `eliminar`
- `clientes`
- `vendedores`
- `estatus`

### `app/models/OrdenModel.php`

Trabaja con la tabla `pedido`, porque la base de datos todavia usa ese nombre para guardar las ordenes.

Metodos:

- `listar()`: trae ordenes con cliente, vendedor y estatus.
- `crear($data)`: crea orden con total `0` y estatus `1`.
- `editar($data)`: edita datos de cabecera y estatus.
- `eliminar($id)`: elimina detalle y cabecera.
- `listarClientes()`
- `listarVendedores()`
- `listarEstatus()`

## 11. Pedidos

### Idea del modulo

El modulo pedidos sirve para ingresar productos dentro de una orden.

Es decir:

- `ordenes` crea la cabecera.
- `pedidos` agrega los productos a esa orden.

### `app/views/page/pedidos/pedidos.php`

Pantalla que lista ordenes y permite ingresar productos.

Funciones JavaScript importantes:

- `cargarProductos()`
- `listarOrdenesPedidos()`
- `abrirProductosOrden(idOrden)`
- `cargarDetalle(idOrden)`
- `agregarProducto()`
- `eliminarProducto(idDetalle, idOrden)`

El precio unitario:

- Se carga automaticamente desde el producto.
- Es solo lectura.
- No se puede modificar desde el formulario.

### `app/controllers/PedidoController.php`

Recibe acciones del modulo pedidos.

Acciones:

- `ordenes`: lista ordenes disponibles.
- `productos`: lista productos disponibles.
- `detalle`: lista productos de una orden.
- `agregar`: agrega producto a una orden.
- `eliminar`: elimina producto del detalle.

### `app/models/PedidoModel.php`

Trabaja con:

- `pedido`
- `detalle_pedido`
- `producto`
- `nombre_producto`

Metodos importantes:

- `listarOrdenes()`: ordenes con cantidad de productos.
- `listarProductos()`: productos disponibles.
- `listarDetalle($idOrden)`: productos dentro de una orden.
- `agregarProducto($data)`: agrega producto y recalcula total.
- `eliminarProducto($idDetalle, $idOrden)`: elimina producto y recalcula total.
- `recalcularTotalOrden($conexion, $idOrden)`: suma subtotales y actualiza `pedido.total`.

## 12. Reportes

### `app/views/page/reportes/reportes.php`

Pantalla de reportes.

Muestra tres bloques:

- Vendedores con mayor venta.
- Productos mas vendidos.
- Clientes con mayor consumo.

Cada bloque muestra:

- Esta semana.
- Esta quincena.
- Este mes.

### `app/models/ReportesModel.php`

Calcula los reportes con consultas SQL.

Metodos:

- `obtenerPeriodos()`: calcula fechas de semana, quincena y mes.
- `vendedoresMayorVenta($desde, $hasta)`
- `productosMasVendidos($desde, $hasta)`
- `clientesMayorConsumo($desde, $hasta)`
- `obtenerReportes()`: arma todos los reportes juntos.

Los reportes excluyen pedidos cancelados:

```sql
AND p.id_estatus <> 3
```

## 13. Salir del sistema

### `app/views/page/salir.php`

Cierra la sesion.

Hace:

1. Inicia sesion si hace falta.
2. Limpia `$_SESSION`.
3. Destruye la sesion.
4. Redirige al login.

## 14. Vistas de error

### `app/views/page/error.php`

Se muestra cuando una ruta no existe.

### `app/views/page/denied.php`

Se muestra cuando la ruta existe, pero el usuario no tiene permiso por rol.

## 15. Tablas principales de base de datos

### `usuarios`

Guarda usuarios del sistema.

Campos importantes:

- `id`
- `cedula`
- `nombre`
- `apellido`
- `password`
- `estatus`
- `cod_rol`

Reglas:

- `estatus = 1`: usuario activo.
- `cod_rol = 1`: administrador.
- `cod_rol = 2`: vendedor.

### `cliente`

Guarda clientes.

### `producto`

Guarda productos.

### `nombre_producto`

Catalogo de nombres base de productos.

### `presentacion`

Catalogo de presentaciones.

### `pedido`

Guarda la cabecera de la orden.

Aunque el modulo visual se llama `ordenes`, la tabla sigue llamandose `pedido`.

### `detalle_pedido`

Guarda los productos agregados a cada orden.

### `estatus`

Catalogo de estados de una orden.

Valores usados:

- `1`: Pendiente.
- `2`: Entregado/Listo.
- `3`: Cancelado.

## 16. Resumen de responsabilidades

### Controladores

Reciben acciones desde AJAX o desde el flujo principal.

Validan que accion se pidio y llaman al modelo correspondiente.

### Modelos

Consultan o modifican la base de datos.

No deberian imprimir HTML.

### Vistas

Muestran la interfaz.

En este proyecto muchas vistas tambien contienen JavaScript para llamar controladores por AJAX.

### JavaScript publico

Maneja acciones del usuario en pantalla:

- clicks
- submits
- llamadas AJAX
- actualizacion de tablas

## 17. Como estudiar el proyecto

Orden recomendado:

1. Lee `index.php`.
2. Lee `app/controllers/enlacesController.php`.
3. Lee `app/core/Router.php`.
4. Lee `config/routes.php`.
5. Lee `app/views/page/menu.php`.
6. Lee login:
   - `app/views/page/login.php`
   - `public/js/login.js`
   - `app/controllers/login.php`
   - `app/models/login.php`
7. Luego estudia modulo por modulo:
   - Vista
   - JavaScript
   - Controlador
   - Modelo

Ejemplo con clientes:

1. `app/views/page/clientes/clientes.php`
2. `app/controllers/ClienteController.php`
3. `app/models/ClienteModel.php`

Ese mismo patron se repite en vendedores, productos, ordenes y pedidos.
