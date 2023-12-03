    document.getElementById('printButton').addEventListener('click', function() {
        var printContents = document.getElementById('residentTable').outerHTML;
        var originalContents = document.body.innerHTML;
        

        document.body.innerHTML = printContents;
        window.print();
        
        document.body.innerHTML = originalContents;
    });