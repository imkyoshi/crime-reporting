

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
    modal.style.display = 'flex'; // Show the modal
    modalMedia.innerHTML = ''; // Clear previous content

    // Set styles for the modal
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.position = 'fixed';
    modal.style.backgroundColor = 'rgba(0,0,0,0.8)'; // Semi-transparent background
    modal.style.zIndex = '1000'; // Ensure modal is on top

    if (mediaSrc.startsWith('blob:')) {
        // For videos with blob URL
        var video = document.createElement('video');
        video.src = mediaSrc;
        video.controls = true; // Add controls
        video.style.width = '120px'; // Adjusted width
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
        // image.classList.add('modal-content');
        image.src = mediaSrc;
        image.style.width = '300px'; // Set fixed width
        image.style.height = '300px'; // Set fixed height
        image.style.objectFit = 'contain'; // Ensure image maintains aspect ratio
        image.style.border = 'none'; // Remove any border
        modalMedia.appendChild(image);
    }
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
