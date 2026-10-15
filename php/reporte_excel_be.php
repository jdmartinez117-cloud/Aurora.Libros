<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

include 'conexion_be.php';

// ?tipo=libros | usuarios | ventas
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'libros';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Estilo del encabezado (fila 1)
function estiloEncabezado($sheet, $rango) {
    $sheet->getStyle($rango)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle($rango)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('B45309');
}

switch ($tipo) {

    case 'usuarios':
        $sheet->setTitle('Usuarios');
        $sheet->fromArray(['ID', 'Nombre Completo', 'Email', 'Usuario'], NULL, 'A1');
        estiloEncabezado($sheet, 'A1:D1');

        $query = "SELECT id, nombre_completo, email, usuario FROM usuarios ORDER BY id";
        $result = mysqli_query($conexion, $query);
        $fila = 2;
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->fromArray(array_values($row), NULL, "A$fila");
            $fila++;
        }
        $filename = 'reporte_usuarios_' . date('Y-m-d') . '.xlsx';
        $ultimaCol = 'D';
        break;

    case 'ventas':
        $sheet->setTitle('Ventas');
        $sheet->fromArray(['ID Pedido', 'Fecha', 'Cliente', 'Total Pagado'], NULL, 'A1');
        estiloEncabezado($sheet, 'A1:D1');

        $query = "SELECT p.id, p.fecha, u.nombre_completo, p.total 
                  FROM pedidos p 
                  JOIN usuarios u ON p.usuario_id = u.id 
                  ORDER BY p.fecha DESC";
        $result = mysqli_query($conexion, $query);
        $fila = 2;
        $totalGeneral = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue("A$fila", $row['id']);
            $sheet->setCellValue("B$fila", date('d/m/Y H:i', strtotime($row['fecha'])));
            $sheet->setCellValue("C$fila", $row['nombre_completo']);
            $sheet->setCellValue("D$fila", (float)$row['total']);
            $totalGeneral += $row['total'];
            $fila++;
        }
        // Fila de total general
        $sheet->setCellValue("C$fila", 'TOTAL GENERAL:');
        $sheet->getStyle("C$fila")->getFont()->setBold(true);
        $sheet->setCellValue("D$fila", $totalGeneral);
        $sheet->getStyle("D$fila")->getFont()->setBold(true);

        $filename = 'reporte_ventas_' . date('Y-m-d') . '.xlsx';
        $ultimaCol = 'D';
        break;

    case 'libros':
    default:
        $sheet->setTitle('Libros');
        $sheet->fromArray(['ID', 'Título', 'Autor', 'Categoría', 'Precio', 'Stock'], NULL, 'A1');
        estiloEncabezado($sheet, 'A1:F1');

        $query = "SELECT id, titulo, author, category, price, stock FROM libros ORDER BY id";
        $result = mysqli_query($conexion, $query);
        $fila = 2;
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue("A$fila", $row['id']);
            $sheet->setCellValue("B$fila", $row['titulo']);
            $sheet->setCellValue("C$fila", $row['author']);
            $sheet->setCellValue("D$fila", $row['category']);
            $sheet->setCellValue("E$fila", (float)$row['price']);
            $sheet->setCellValue("F$fila", (int)$row['stock']);
            $fila++;
        }
        $filename = 'reporte_libros_' . date('Y-m-d') . '.xlsx';
        $ultimaCol = 'F';
        break;
}

// Autoajustar ancho de columnas
foreach (range('A', $ultimaCol) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;