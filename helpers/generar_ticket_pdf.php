<?php
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

function mm_get_qr_tmp($data)
{
    $url = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&format=png&data=" . urlencode($data);
    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "mm_qr_" . md5($data) . ".png";
    if (file_exists($tmp)) return $tmp;
    $img = @file_get_contents($url);
    if ($img === false) return null;
    @file_put_contents($tmp, $img);
    return file_exists($tmp) ? $tmp : null;
}

function mm_pdf_image_path(?string $absolutePath): ?string {
    if (!$absolutePath || !file_exists($absolutePath)) {
        return null;
    }
    $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return $absolutePath;
    }
    if ($ext !== 'webp' || !function_exists('imagecreatefromwebp') || !function_exists('imagejpeg')) {
        return null;
    }
    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mm_pdf_' . md5($absolutePath . '|' . filemtime($absolutePath)) . '.jpg';
    if (file_exists($tmp)) {
        return $tmp;
    }
    $img = @imagecreatefromwebp($absolutePath);
    if (!$img) {
        return null;
    }
    $width = imagesx($img); $height = imagesy($img);
    $canvas = imagecreatetruecolor($width, $height);
    $bg = imagecolorallocate($canvas, 15, 17, 24);
    imagefill($canvas, 0, 0, $bg);
    imagecopy($canvas, $img, 0, 0, 0, 0, $width, $height);
    imagejpeg($canvas, $tmp, 90);
    imagedestroy($img);
    imagedestroy($canvas);
    return file_exists($tmp) ? $tmp : null;
}

function generarPdfTicketGuardado(array $ticket): string
{
    $carpetaTickets = __DIR__ . '/../tickets';
    if (!is_dir($carpetaTickets)) {
        mkdir($carpetaTickets, 0777, true);
    }
    $rutaPdf = $carpetaTickets . '/ticket_' . $ticket['codigo'] . '.pdf';

    $PRIMARY = [245, 158, 11];
    $DARK    = [17, 24, 39];
    $LIGHT   = [249, 250, 251];

    class PDFMailTicket extends FPDF {
        public $dark = [0, 0, 0];
        function Header(){ $this->SetFillColor($this->dark[0], $this->dark[1], $this->dark[2]); $this->Rect(0, 0, 210, 28, 'F'); }
        function Footer(){ $this->SetY(-18); $this->SetFont('Arial', '', 9); $this->SetTextColor(120, 120, 120); $this->Cell(0, 8, utf8_decode('Gracias por tu compra - MMCINEMA'), 0, 1, 'C'); }
    }

    $pdf = new PDFMailTicket('P', 'mm', 'A4');
    $pdf->dark = $DARK;
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 20);

    $logoAbs = mm_pdf_image_path(__DIR__ . '/../assets/img/logo.png') ?: mm_pdf_image_path(__DIR__ . '/../assets/img/logo2.png');
    if ($logoAbs) {
        $pdf->Image($logoAbs, 10, 6, 18);
    }

    $pdf->SetXY(32, 7);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 10, utf8_decode("TICKET DE ENTRADA"), 0, 1);
    $pdf->SetX(32);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, utf8_decode("Código: " . $ticket['codigo']), 0, 1);

    $qrTmp = mm_get_qr_tmp($ticket['codigo']);
    if ($qrTmp) {
        $pdf->Image($qrTmp, 170, 8, 25, 25);
    }

    $pdf->Ln(8);
    $pdf->SetFillColor($LIGHT[0], $LIGHT[1], $LIGHT[2]);
    $pdf->SetDrawColor($PRIMARY[0], $PRIMARY[1], $PRIMARY[2]);

    $boxX = 10; $boxY = 40; $boxW = 190; $boxH = 120;
    $pdf->Rect($boxX, $boxY, $boxW, $boxH, 'DF');

    $posterAbs = mm_pdf_image_path(__DIR__ . '/../assets/img/posters/' . trim($ticket['poster'] ?? ''));
    $posterX = $boxX + 4; $posterY = $boxY + 6; $posterW = 55; $posterH = 75;
    $textX   = $posterX + $posterW + 8; $textY = $posterY; $textW = $boxX + $boxW - $textX - 6;

    if ($posterAbs) {
        $pdf->Image($posterAbs, $posterX, $posterY, $posterW, $posterH);
    } else {
        $pdf->SetXY($posterX, $posterY);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->MultiCell($posterW, 6, utf8_decode("Sin poster"));
    }

    $pdf->SetXY($textX, $textY);
    $pdf->SetTextColor(17, 24, 39);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->MultiCell($textW, 7, utf8_decode($ticket['titulo']));
    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetX($textX); $pdf->Cell(30, 8, utf8_decode("Fecha:"), 0, 0); $pdf->Cell($textW - 30, 8, utf8_decode($ticket['fecha']), 0, 1);
    $pdf->SetX($textX); $pdf->Cell(30, 8, utf8_decode("Hora:"), 0, 0); $pdf->Cell($textW - 30, 8, utf8_decode($ticket['hora']), 0, 1);
    $pdf->SetX($textX); $pdf->Cell(30, 8, utf8_decode("Sala:"), 0, 0); $pdf->Cell($textW - 30, 8, utf8_decode($ticket['sala']), 0, 1);
    $pdf->SetX($textX); $pdf->Cell(30, 8, utf8_decode("Entradas:"), 0, 0); $pdf->Cell($textW - 30, 8, (int)$ticket['cantidad'], 0, 1);
    $pdf->SetX($textX); $pdf->Cell(30, 8, utf8_decode("Asientos:"), 0, 0); $pdf->MultiCell($textW - 30, 8, utf8_decode($ticket['asientos']));

    $totalY = $posterY + $posterH + 10;
    $pdf->SetXY($boxX + 10, min($totalY, $boxY + $boxH - 18));
    $pdf->SetFillColor($PRIMARY[0], $PRIMARY[1], $PRIMARY[2]);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(17, 24, 39);
    $pdf->Cell(60, 10, utf8_decode("TOTAL"), 0, 0, 'C', true);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(60, 10, number_format((float)$ticket['total'], 2) . " EUR", 0, 1, 'C', true);

    $pdf->Output('F', $rutaPdf);
    return $rutaPdf;
}
