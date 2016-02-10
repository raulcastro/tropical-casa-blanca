<?php
date_default_timezone_set('America/Cancun');

$root = $_SERVER['DOCUMENT_ROOT']."/";

include $root.'Framework/PHPExcel/PHPExcel.php';
include $root.'Framework/PHPExcel/PHPExcel/Writer/Excel2007.php';
require_once $root.'backends/admin-backend.php';
require_once $root.'/'.'views/Layout_View.php';

	$section = 'reports';

	$data 	= $backend->loadBackend($section);

	$view 	= new Layout_View($data, 'Reports');

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Raul Castro");
$objPHPExcel->getProperties()->setLastModifiedBy("Raul Castro");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Report");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Report");
$objPHPExcel->getProperties()->setDescription("Monthly report.");

// Add some data
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getCell('A1')->setValue('R. ID');
$objPHPExcel->getActiveSheet()->getCell('B1')->setValue('Date');
$objPHPExcel->getActiveSheet()->getCell('C1')->setValue('Guest Name');
$objPHPExcel->getActiveSheet()->getCell('D1')->setValue('Ad.');
$objPHPExcel->getActiveSheet()->getCell('E1')->setValue('Ch.');
$objPHPExcel->getActiveSheet()->getCell('F1')->setValue('Nights');
$objPHPExcel->getActiveSheet()->getCell('G1')->setValue('Agency');
$objPHPExcel->getActiveSheet()->getCell('H1')->setValue('PPN');
$objPHPExcel->getActiveSheet()->getCell('I1')->setValue('Total');
$objPHPExcel->getActiveSheet()->getCell('J1')->setValue('Paid');
$objPHPExcel->getActiveSheet()->getCell('K1')->setValue('Room');
$objPHPExcel->getActiveSheet()->getCell('L1')->setValue('Check In');
$objPHPExcel->getActiveSheet()->getCell('M1')->setValue('Check Out');
$objPHPExcel->getActiveSheet()->getCell('N1')->setValue('Status');
$objPHPExcel->getActiveSheet()->getCell('O1')->setValue('Country');
$objPHPExcel->getActiveSheet()->getCell('P1')->setValue('External Id');
$objPHPExcel->getActiveSheet()->getCell('Q1')->setValue('Comments');

$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFill()->getStartColor()->setRGB('111111');
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFont()->getColor()->setRGB('111111');

$i = 1;
foreach ($data['reservations'] as $reservation)
{
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $reservation['reservation_id']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $reservation['date']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $reservation['name'].' '.$reservation['last_name']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $reservation['adults']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $reservation['children']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i, $reservation['n_days']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $reservation['agency']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i, '$ '.$reservation['ppn']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $i, '$ '.$reservation['total']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $i, '$ '.$reservation['paid']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $i, $reservation['room']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $i, Tools::formatMYSQLToFront($reservation['check_in']));
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $i, Tools::formatMYSQLToFront($reservation['check_out']));
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $i, $reservation['r_status']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $i, $reservation['country']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $i, $reservation['external_id']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $i, $reservation['notes']);
}


if (!$_GET['from'])
{
	$from = date('Y-m-d', strtotime(' -1 day'));
	$start = date('Y-Md', strtotime(' -1 day', strtotime($from)));
	$end = date('Y-m-M', strtotime(' +31 day', strtotime($from)));
}
else
{
	$from = date('Y-m-d', strtotime($_GET['from']));
	$start = date('Y-M', strtotime(' -1 day', strtotime($_GET['from'])));
	$end = date('Y-M-d', strtotime(' +32 day', strtotime($_GET['from'])));
}

$reportTitle = 'Report '.$start.'.xlsx';

$objPHPExcel->getActiveSheet()->setTitle($reportTitle);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$reportTitle.'"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
