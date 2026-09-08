$path = "app/views/page/pedido/pedido.php"
$contents = @"
<style>
.pedidos-page {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  margin: 20px 0 30px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}
.pedidos-page h3 {
  margin: 0 0 8px;
  color: #1f2937;
}
.pedidos-page p {
  margin: 0 0 16px;
  color: #6b7280;
}
.pedidos-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}
.pedidos-btn {
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 8px 14px;
  cursor: pointer;
}
.pedidos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}
.pedidos-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 12px;
}
.pedidos-card strong {
  display: block;
  font-size: 1.1rem;
  margin-top: 6px;
  color: #111827;
}
.pedidos-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}
.pedidos-table th, .pedidos-table td {
  text-align: left;
  padding: 10px 8px;
  border-bottom: 1px solid #e5e7eb;
}
.pedidos-table th {
  color: #6b7280;
  font-weight: 600;
}
.pedidos-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 999px;
  background: #dcfce7;
  color: #166534;
  font-size: 0.8rem;
}
</style>

<div class="pedidos-page">
  <div class="pedidos-toolbar">
    <div>
      <h3>Gestión de Pedidos</h3>
      <p>Vista integrada dentro del menú del sistema.</p>
    </div>
    <button class="pedidos-btn">Nuevo pedido</button>
  </div>

  <div class="pedidos-grid">
    <div class="pedidos-card">
      <small>Total pedidos</small>
      <strong>24</strong>
    </div>
    <div class="pedidos-card">
      <small>En proceso</small>
      <strong>8</strong>
    </div>
    <div class="pedidos-card">
      <small>Entregados</small>
      <strong>14</strong>
    </div>
    <div class="pedidos-card">
      <small>Cancelados</small>
      <strong>2</strong>
    </div>
  </div>

  <table class="pedidos-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Estatus</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>001</td>
        <td>Ana Gómez</td>
        <td>2026-06-10</td>
        <td>$1.250</td>
        <td><span class="pedidos-badge">Entregado</span></td>
      </tr>
      <tr>
        <td>002</td>
        <td>Luis Martínez</td>
        <td>2026-06-10</td>
        <td>$3.840</td>
        <td><span class="pedidos-badge">En proceso</span></td>
      </tr>
    </tbody>
  </table>
</div>
"@
Set-Content -Path $path -Value $contents -NoNewline
