<?php
require_once '../api/vendor/autoload.php'; // Make sure to require the autoload file from chillerlan\QRCode library

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\QRImage;

$data = '1234444444';

// Create an instance of QRCode
$qrcode = new QRCode();

// Render the QR code and get the image data
$imageData = $qrcode->render($data);

// Define the directory to save the file
$saveDirectory = __DIR__. '../../dist/QRCodes/';

// Create the directory if it doesn't exist
if (!file_exists($saveDirectory)) {
    mkdir($saveDirectory, 0777, true);
}

// Save the image data to a file
$savePath = $saveDirectory . uniqid() . '.png';
file_put_contents($savePath, $imageData);

// Display the QR code image
// echo '<img src="'.$imageData.'" class="" alt="" style="width:0px;">';
?>
