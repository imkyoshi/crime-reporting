function printModalContent() {
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Print</title></head><body>');
    printWindow.document.write(document.getElementById('viewCrimeInfoModal<?php echo $crimeinfo['crime_id']; ?>').innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}