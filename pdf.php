<?php
require_once('./TCPDF-main/examples/tcpdf_include.php');

ob_start();
include "image.php";
$img = ob_get_contents();
ob_end_clean();

class MYPDF extends TCPDF
{
    public function Header()
    {
        $this->Write(0, "PDF generated at " . date("d-M-Y G:i:s"), '', 0, 'C', true, 0, false, false, 0);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// $img_html = '<img src="image.php?width=650&height=400"">';
$pdf->setTitle("Graph");
$pdf->setHeaderData("", 0, "", "", array(0, 0, 0), array(0, 0, 0));

$pdf->AddPage();
// $pdf->writeHTML($img_html, true, false, true, false, '');
$pdf->writeHTML("<h1>asdasdassda</h1>");

$pdf->Image("@" . $img);
$html = "<style> 
</style>";

$html .= ("<table id='pozdro'><tr><td></td><td><table border=\"1\"><thead><tr><th>Argument</th><th>Value</th></tr></thead><tbody>");
foreach ($data as $item) {
    $value = $item[1];
    if($value == -1) $value = "Illness";
    else if($value == null) $value = "No data";
    $html .= ("<tr>
        <td>" . $item[0] . "</td>
        <td>" . ($value). "</td>
        </tr>");
}
$html .= ("</tbody></table></td></tr></table>");

$pdf->Circle(5, 165, 2, 0, 360, 'DF', [], array(24, 163, 36));
$pdf->setXY(10, 162);
$pdf->Write(0, "illness", '', 0, 'L', true, 0, false, false, 0);
$pdf->Circle(5, 172, 2, 0, 360, 'DF', [], array(100, 100, 100));
$pdf->setXY(10, 169);
$pdf->Write(0, "no data", '', 0, 'L', true, 0, false, false, 0);
$pdf->setY(160);
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('graph.pdf', 'I');

?>