window.onload = function () {
    let ps = placeSearch({
        key: '0fFXuPkd14u4sjwUTzWeMvSaynvJ6yG6',
        container: document.querySelector('#search-input'),
        useDeviceLocation: true,
        collection: [
            'poi',
            'airport',
            'address',
            'adminArea',
        ]
    });

    L.mapquest.key = '0fFXuPkd14u4sjwUTzWeMvSaynvJ6yG6';

    var map = L.mapquest.map('map', {
        center: [0, 0],
        layers: L.mapquest.tileLayer('map'),
        zoom: 2
    });

    L.mapquest.control().addTo(map);

    let markers = [];
    let debounceTimeout;

    ps.on('change', (e) => {
        markers
            .forEach(function (marker, markerIndex) {
                if (markerIndex === e.resultIndex) {
                    markers = [marker];
                    marker.setOpacity(1);
                    map.setView(e.result.latlng, 11);
                    updateSearchInput(marker.getLatLng());
                } else {
                    removeMarker(marker);
                }
            });
    });

    ps.on('results', (e) => {
        markers.forEach(removeMarker);
        markers = [];

        if (e.results.length === 0) {
            map.setView(new L.LatLng(0, 0), 2);
            return;
        }

        e.results.forEach(addMarker);
        findBestZoom();
    });

    ps.on('cursorchanged', (e) => {
        markers
            .forEach(function (marker, markerIndex) {
                if (markerIndex === e.resultIndex) {
                    marker.setOpacity(1);
                    marker.setZIndexOffset(1000);
                } else {
                    marker.setZIndexOffset(0);
                    marker.setOpacity(0.5);
                }
            });
    });

    ps.on('clear', () => {
        console.log('cleared');
        map.setView(new L.LatLng(0, 0), 2);
        markers.forEach(removeMarker);

            const mapContainer = document.getElementById('map');
    if (mapContainer) {
        mapContainer.style.paddingTop = '20px'; // Adjust the value as needed
    }
    });

    ps.on('error', (e) => {
        console.log(e);
    });

    function addMarker(result) {
        let marker = L.marker(result.latlng, { opacity: 0.4, draggable: true });

        marker.on('dragend', function (event) {
            updateSearchInput(event.target.getLatLng());
        });

        marker.addTo(map);
        markers.push(marker);
    }

    function removeMarker(marker) {
        map.removeLayer(marker);
    }

    function findBestZoom() {
        let featureGroup = L.featureGroup(markers);
        map.fitBounds(featureGroup.getBounds().pad(0.5), { animate: false });
    }

    function updateSearchInput(latlng) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            fetch(`https://www.mapquestapi.com/geocoding/v1/reverse?key=${L.mapquest.key}&location=${latlng.lat},${latlng.lng}&includeRoadMetadata=true&includeNearestIntersection=true`)
                .then(response => response.json())
                .then(data => {
                    const firstLocation = data.results[0].locations[0];
                    const address = `${firstLocation.street || ''} ${firstLocation.adminArea5 || ''}, ${firstLocation.adminArea3 || ''} ${firstLocation.postalCode || ''}, ${firstLocation.adminArea1 || ''}`;
                    document.querySelector('#search-input').value = address;
                })
                .catch(error => {
                    console.error('Error fetching reverse geocoding data:', error);
                });
        }, 500);
    }

    // Open the modal when the button is clicked
    document.getElementById('pickLocationBtn').addEventListener('click', function () {
        var locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
        locationModal.show();
    });

    // Function to handle the submit button click
    document.getElementById('submitBtn').addEventListener('click', function () {
        // Get the value from the search-input
        const searchValue = document.querySelector('#search-input').value;

        // Display the value in the address input
        document.querySelector('#placeOfIncident').value = searchValue;

        // Close the modal
        var locationModal = bootstrap.Modal.getInstance(document.getElementById('locationModal'));
        locationModal.hide();
    });
};
