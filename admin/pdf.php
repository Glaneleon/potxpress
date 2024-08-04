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

            usort($pdfs, function ($a, $b) use ($cpdf_dir) {
                $file1 = $cpdf_dir . $a;
                $file2 = $cpdf_dir . $b;
                return filemtime($file2) - filemtime($file1);
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
    <div class="mb-4">
        <h2>Official COD Receipts</h2>
        <ul class="list-group">
            <?php
            $opdf_dir = '../receipts/storecopy/';

            $opdfs = array_filter(scandir($opdf_dir), function ($ofile) use ($opdf_dir) {
                return isPDF($ofile) && !in_array($ofile, ['.', '..']);
            });

            usort($pdfs, function ($a, $b) use ($opdf_dir) {
                $file1 = $opdf_dir . $a;
                $file2 = $opdf_dir . $b;
                return filemtime($file2) - filemtime($file1);
            });

            if (!empty($opdfs)) {
                foreach ($opdfs as $opdf) {
                    $ofilepath = $opdf_dir . $opdf;
                    echo "<li><a href='$ofilepath' target='_blank'>$opdf</a></li>";
                }
            } else {
                echo '<li>No Official COD Receipts found.</li>';
            }
            ?>
        </ul>
    </div>
</div>