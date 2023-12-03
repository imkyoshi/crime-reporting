<?php
require_once '../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\QRImage;


$data = '1234444444';

echo '<img src="'.(new QRCode)->render($data).'" class="" alt="" style="width:150px;">;'
?>
