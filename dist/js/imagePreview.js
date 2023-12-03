function previewValidID() {
    var input = document.getElementById('formFileValidID');
    var previewsContainer = document.getElementById('ValidIDPreviews');
    previewsContainer.innerHTML = '';

    if (input.files) {
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];

            if (file.type.startsWith('image/')) {
                // For images
                var reader = new FileReader();
                reader.onload = function (e) {
                    var preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.classList.add('preview-media');
                    preview.style.maxWidth = '160px'; // Set the maximum width
                    preview.style.marginRight = '5px'; // Add some spacing between images
                    preview.addEventListener('click', function () {
                        openMediaModal(preview.src);
                    });
                    previewsContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                // For videos
                var video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.style.maxWidth = '200px'; // Set the maximum width
                video.style.marginRight = '5px'; // Add some spacing between videos
                video.style.paddingTop = '10px'; // Add some spacing between videos
                video.addEventListener('click', function () {
                    openMediaModal(video.src);
                });
                previewsContainer.appendChild(video);
            }
        }
    }
}


function previewEvidence() {
    var input = document.getElementById('formFileEvidence');
    var previewsContainer = document.getElementById('EvidencePreviews');
    previewsContainer.innerHTML = '';

    if (input.files) {
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];

            if (file.type.startsWith('image/')) {
                // For images
                var reader = new FileReader();
                reader.onload = function (e) {
                    var preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.classList.add('preview-media');
                    preview.style.maxWidth = '160px'; // Set the maximum width
                    preview.style.marginRight = '5px'; // Add some spacing between images
                    preview.addEventListener('click', function () {
                        openMediaModal(preview.src);
                    });
                    previewsContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                // For videos
                var video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.style.maxWidth = '200px'; // Set the maximum width
                video.style.marginRight = '5px'; // Add some spacing between videos
                video.style.paddingTop = '10px'; // Add some spacing between videos
                video.addEventListener('click', function () {
                    openMediaModal(video.src);
                });
                previewsContainer.appendChild(video);
            }
        }
    }
}

function openMediaModal(mediaSrc) {
    var modal = document.getElementById('mediaModal');
    var modalMedia = document.getElementById('modalMedia');
    modalMedia.innerHTML = ''; // Clear previous content

    if (mediaSrc.startsWith('blob:')) {
        // For videos with blob URL
        var video = document.createElement('video');
        video.src = mediaSrc;
        video.controls = true; // Add controls
        video.style.width = '120%'; // Adjusted width
        video.style.maxHeight = '300px'; // Adjusted height
        modalMedia.appendChild(video);

        // Play the video when clicked in the modal
        video.addEventListener('click', function () {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        });
    } else {
        // For images
        var image = document.createElement('img');
        image.src = mediaSrc;
        image.classList.add('modal-content');
        image.style.width = '100%'; // Adjusted width
        image.style.maxHeight = '70vh'; // Adjusted height
        modalMedia.appendChild(image);
    }

    // Center the modal
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}


function closeMediaModal() {
    var modal = document.getElementById('mediaModal');
    var modalMedia = document.getElementById('modalMedia');
    
    // Revoke the object URL for videos
    var video = modalMedia.querySelector('video');
    if (video && video.src.startsWith('blob:')) {
        URL.revokeObjectURL(video.src);
    }

    modal.style.display = 'none';
}
