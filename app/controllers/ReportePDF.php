<?php
require_once __DIR__ . '/../../resources/library/fpdf/fpdf.php';
require_once __DIR__ . '/../models/ReportesModel.php';

$model = new ReportesModel();
$datos = $model->generarReporte($_GET);
$tipo = $_GET['tipo'];

// Títulos
$titulos = [
    'cliente' => 'REPORTE DE PEDIDOS POR CLIENTE',
    'articulo' => 'REPORTE DE PEDIDOS POR ARTICULO (PRODUCTO)',
    'vendedor' => 'REPORTE DE PEDIDOS POR VENDEDOR',
    'fecha' => 'REPORTE DE PEDIDOS POR FECHA'
];

$pdf = new FPDF('L', 'mm', 'A4'); // L = Landscape (Horizontal)
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetFillColor(238, 179, 112); // Color Naranja del encabezado
$pdf->Cell(0, 10, $titulos[$tipo], 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230);

// Dibujar Cabeceras según el tipo
if ($tipo === 'cliente') {
    $pdf->Cell(50, 7, 'CLIENTE', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'FACTURA', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'FECHA', 1, 0, 'C', true);
    $pdf->Cell(70, 7, 'PRODUCTO', 1, 0, 'C', true);
    $pdf->Cell(50, 7, 'VENDEDOR', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'NETO', 1, 1, 'C', true);
} elseif ($tipo === 'articulo') {
    $pdf->Cell(60, 7, 'ARTICULO', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'FACTURA', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'FECHA', 1, 0, 'C', true);
    $pdf->Cell(45, 7, 'VENDEDOR', 1, 0, 'C', true);
    $pdf->Cell(45, 7, 'CLIENTE', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'CANTIDAD', 1, 0, 'C', true);
    $pdf->Cell(15, 7, 'UNIDAD', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'PRECIO', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'NETO', 1, 1, 'C', true);
} else {
    $pdf->Cell(60, 7, 'VENDEDOR', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'FACTURA', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'FECHA', 1, 0, 'C', true);
    $pdf->Cell(80, 7, 'CLIENTE', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'NETO', 1, 1, 'C', true);
}

// Pintar filas
$pdf->SetFont('Arial', '', 8);
foreach ($datos as $f) {
    if ($tipo === 'cliente') {
        $pdf->Cell(50, 7, substr(utf8_decode($f['cliente']), 0, 25), 1);
        $pdf->Cell(20, 7, $f['factura'], 1, 0, 'C');
        $pdf->Cell(25, 7, $f['fecha'], 1, 0, 'C');
        $pdf->Cell(70, 7, substr(utf8_decode($f['producto']), 0, 35), 1);
        $pdf->Cell(50, 7, substr(utf8_decode($f['vendedor']), 0, 25), 1);
        $pdf->Cell(25, 7, '$' . $f['neto'], 1, 1, 'R');
    } elseif ($tipo === 'articulo') {
        $pdf->Cell(60, 7, substr(utf8_decode($f['producto']), 0, 30), 1);
        $pdf->Cell(20, 7, $f['factura'], 1, 0, 'C');
        $pdf->Cell(25, 7, $f['fecha'], 1, 0, 'C');
        $pdf->Cell(45, 7, substr(utf8_decode($f['vendedor']), 0, 20), 1);
        $pdf->Cell(45, 7, substr(utf8_decode($f['cliente']), 0, 20), 1);
        $pdf->Cell(20, 7, $f['cantidad'], 1, 0, 'C');
        $pdf->Cell(15, 7, utf8_decode($f['unidad']), 1, 0, 'C');
        $pdf->Cell(20, 7, '$' . $f['precio'], 1, 0, 'R');
        $pdf->Cell(25, 7, '$' . $f['neto'], 1, 1, 'R');
    } else {
        $pdf->Cell(60, 7, substr(utf8_decode($f['vendedor']), 0, 30), 1);
        $pdf->Cell(30, 7, $f['factura'], 1, 0, 'C');
        $pdf->Cell(30, 7, $f['fecha'], 1, 0, 'C');
        $pdf->Cell(80, 7, substr(utf8_decode($f['cliente']), 0, 45), 1);
        $pdf->Cell(30, 7, '$' . $f['neto'], 1, 1, 'R');
    }
}

$pdf->Output('I', 'Reporte_Distribuidora.pdf');
?>