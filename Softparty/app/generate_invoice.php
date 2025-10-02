<?php
// Define la tasa horaria fija, debe coincidir con el archivo principal.
const HOURLY_RATE = 3000; 

// 1. Incluir la librería FPDF (Asegúrate que el archivo fpdf.php esté en el mismo directorio)
require('fpdf.php');

// 2. Recibir los datos del formulario principal (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['plate'])) {
    die("Acceso denegado o datos insuficientes.");
}

$plate          = htmlspecialchars($_POST['plate']);
$entry_time     = htmlspecialchars($_POST['entry_time']);
$exit_time      = htmlspecialchars($_POST['exit_time']);
$readable_time  = htmlspecialchars($_POST['readable_time']);
$billable_hours = (int)$_POST['billable_hours'];
$payment_amount = (int)$_POST['payment_amount'];
$owner_name     = htmlspecialchars($_POST['owner_name'] ?? 'Cliente Desconocido');
$document_id    = htmlspecialchars($_POST['document_id'] ?? 'N/A');

// 3. Crear una nueva instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();

// 4. Configuración inicial de fuente
$pdf->SetFont('Arial','B',16);

// --- Cabecera de la Factura ---
$pdf->Cell(0, 10, 'FACTURA DE PARQUEADERO', 0, 1, 'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(0, 5, 'Fecha de Emision: ' . date('Y-m-d H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// --- Información del Cliente/Vehículo ---
$pdf->SetFillColor(200, 220, 255); // Color de fondo para las cabeceras de sección
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0, 8, 'Detalles del Cliente y Vehiculo', 1, 1, 'L', true);

$pdf->SetFont('Arial','',10);
$pdf->Cell(50, 6, 'Propietario:', 0);
$pdf->Cell(0, 6, $owner_name, 0, 1);
$pdf->Cell(50, 6, 'Documento:', 0);
$pdf->Cell(0, 6, $document_id, 0, 1);
$pdf->Cell(50, 6, 'Placa:', 0);
$pdf->Cell(0, 6, $plate, 0, 1);
$pdf->Ln(5);

// --- Detalles de la Estancia ---
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0, 8, 'Detalles de la Estancia', 1, 1, 'L', true);

$pdf->SetFont('Arial','',10);
$pdf->Cell(50, 6, 'Hora de Entrada:', 0);
$pdf->Cell(0, 6, $entry_time, 0, 1);
$pdf->Cell(50, 6, 'Hora de Salida:', 0);
$pdf->Cell(0, 6, $exit_time, 0, 1);
$pdf->Cell(50, 6, 'Tiempo Total Estacionado:', 0);
$pdf->Cell(0, 6, $readable_time, 0, 1);
$pdf->Ln(5);

// --- Resumen de Facturación ---
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0, 8, 'Resumen de Costos', 1, 1, 'L', true);

$pdf->SetFont('Arial','',10);
$pdf->Cell(100, 6, 'Tarifa por Hora:', 0);
$pdf->Cell(0, 6, '$' . number_format(HOURLY_RATE, 0, '.', ','), 0, 1, 'R');

$pdf->Cell(100, 6, 'Horas Facturables (Redondeo):', 0);
$pdf->Cell(0, 6, $billable_hours . ' horas', 0, 1, 'R');

$pdf->Ln(5);

// --- Total a Pagar ---
$pdf->SetFont('Arial','B',14);
$pdf->SetFillColor(255, 200, 200); // Fondo rojo claro para el total
$pdf->Cell(100, 10, 'VALOR TOTAL A PAGAR:', 1, 0, 'L', true);
$pdf->Cell(0, 10, '$' . number_format($payment_amount, 0, '.', ','), 1, 1, 'R', true);


// 5. Salida del PDF: 'D' fuerza la descarga, 'I' lo muestra en el navegador
$pdf->Output('factura_' . $plate . '_' . time() . '.pdf', 'D');

// Asegurarse de que no se ejecute más código HTML después de esto.
exit;
?>