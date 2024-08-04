<div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">
    <div class="mb-4">
        <h2>Sales Reports</h2>
        <ul class="list-group">
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

            // Sort PDFs by modification time in descending order
            usort($pdfs, function ($a, $b) use ($pdf_dir) {
                $file1 = $pdf_dir . $a;
                $file2 = $pdf_dir . $b;
                return filemtime($file2) - filemtime($file1);
            });

            if (!empty($pdfs)) {
                foreach ($pdfs as $pdf) {
                    $filepath = $pdf_dir . $pdf;
                    echo "<li><a href='$filepath' target='_blank'>$pdf</a></li>";
                }
            } else {
                echo '<li>No Sales Report found.</li>';
            }
            ?>
        </ul>
    </div>
    <div class="mb-4">
        <h2>COD Receipts</h2>
        <ul class="list-group">
            <?php
            $cpdf_dir = '../receipts/';

            $cpdfs = array_filter(scandir($cpdf_dir), function ($cfile) use ($cpdf_dir) {
                return isPDF($cfile) && !in_array($cfile, ['.', '..']);
            });

            if (!empty($cpdfs)) {
                foreach ($cpdfs as $cpdf) {
                    $cfilepath = $cpdf_dir . $cpdf;
                    echo "<li><a href='$cfilepath' target='_blank'>$cpdf</a></li>";
                }
            } else {
                echo '<li>No COD Receipts found.</li>';
            }
            ?>
        </ul>
    </div>
</div>