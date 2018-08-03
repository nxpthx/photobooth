import 'lightgallery.js/dist/js/lightgallery';
import 'jquery';

lightGallery(document.getElementById('lightgallery'), {
    download: false,
    counter: false,
    dynamic: false,
    selector: 'a',
    preload: 2,
    hideControlOnEnd: true,
    getCaptionFromTitleOrAlt: false,
    appendSubHtmlTo: '.lg-item',
    addClass: 'withSidebar'
});

let checkNewPhotos = function() {
    $.ajax({ url: '/fileCount', contentType: 'text/plain'}).done(function(data) {
        if (parseFloat(data) > window.imageCount) {
            location.reload(true);
        }
        window.setTimeout(checkNewPhotos, 30000);
    })
};

window.setTimeout(checkNewPhotos, 30000);