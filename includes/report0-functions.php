<?php 
require_once '../api/vendor/autoload.php';

use chillerlan\QRCode\QRCode;

function generateQRCode($filename, $data) {
    // Ensure the directory exists, create it if not
    $dir = pathinfo($filename, PATHINFO_DIRNAME);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // Generate QR code and save it to a file
    $qrCode = (new QRCode)->render($data);
    file_put_contents($filename, $qrCode);

    // Return the path to the saved QR code
    return $filename;
}



?>