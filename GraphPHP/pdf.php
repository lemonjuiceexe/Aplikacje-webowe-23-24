<?php 
require_once('./TCPDF-main/examples/tcpdf_include.php');

ob_start();
include "image.php";
$img = ob_get_contents();
ob_end_clean();
// ob_start();
// include "index.php";
// $html = ob_get_contents();
// ob_end_clean();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// $img_html = '<img src="image.php?width=650&height=400"">';

$pdf->AddPage();
// $pdf->writeHTML($img_html, true, false, true, false, '');
$pdf->writeHTML("<h1>asdasdassda</h1>");
$pdf->Image("@".$img);
// $pdf->writeHTML($html);

$pdf->Output('graph.pdf', 'I');

?>