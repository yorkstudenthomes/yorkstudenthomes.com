document.addEventListener('DOMContentLoaded', function () {
    if (location.hash && /^#img:\d+$/.test(location.hash)) {
        fsLightbox.open(parseInt(location.hash.split(':')[1], 10) - 1);
    }
});
