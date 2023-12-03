<?php
error_reporting(error_reporting() & ~E_DEPRECATED);



use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Function to generate a QR code and return the image path
function generateQRCode($data, $filename)
{
    $tempDir = __DIR__ .'../dist/QRCodes'; // Replace with your desired temp directory

    // Create the temp directory if it doesn't exist
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true); // Set recursive parameter to true
    }

    // Set the QR code parameters using QROptions
    // $options = new QROptions([
    //     'version' => 5,
    //     'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    //     'eccLevel' => QRCode::ECC_L,
    //     'scale' => 5,
    // ]);

    // Generate the QR code
    $qrCode = new QRCode();
    $imageData = $qrCode->render($data);

    // Save the QR code image
    $filePath = $tempDir . __DIR__. '../dist/QRCodes' . $filename;
    file_put_contents($filePath, $imageData);

    // Return the path to the generated QR code image
    return $filePath;
}

// Example usage
$data = 'https://example.com'; // Replace with your actual data
$filename = 'qrcode.png'; // Replace with your desired filename

$qrImagePath = generateQRCode($data, $filename);

// Check the generated path
var_dump($qrImagePath);

?>