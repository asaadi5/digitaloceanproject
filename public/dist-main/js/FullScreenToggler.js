'use strict';
function isFullScreen() {
    return !!(document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
}

    function requestFullScreen(element) {
    if (element.requestFullscreen) {
    element.requestFullscreen();
} else if (element.msRequestFullscreen) {
    element.msRequestFullscreen();
} else if (element.webkitRequestFullscreen) {
    element.webkitRequestFullscreen();
} else if (element.mozRequestFullScreen) {
    element.mozRequestFullScreen();
}
}

    function exitFullScreen(doc) {
    if (doc.exitFullscreen) {
    doc.exitFullscreen();
} else if (doc.msExitFullscreen) {
    doc.msExitFullscreen();
} else if (doc.webkitExitFullscreen) {
    doc.webkitExitFullscreen();
} else if (doc.mozCancelFullScreen) {
    doc.mozCancelFullScreen();
}
}

    document.getElementById('fullscreenLink').addEventListener('click', function () {
    if (isFullScreen()) {
    exitFullScreen(document);
        $('#full-screen-icon').attr('name','scan-sharp');

} else {
    requestFullScreen(document.documentElement);
        $('#full-screen-icon').attr('name','contract-sharp');
}
});