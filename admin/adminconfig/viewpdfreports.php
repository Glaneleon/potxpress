<?php
$pdf_dir = '../dailyreports/';

function isPDF($filename)
{
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return $extension === 'pdf';
}

$pdfs = array_filter(scandir($pdf_dir), function ($file) use ($pdf_dir) {
    return isPDF($file) && !in_array($file, ['.', '..']);
});

if (!empty($pdfs)) {
    foreach ($pdfs as $pdf) {
        $filepath = $pdf_dir . $pdf;
        echo "<li><a href='$filepath' target='_blank'>$pdf</a></li>";
    }
} else {
    echo '<li>No PDFs found.</li>';
}
