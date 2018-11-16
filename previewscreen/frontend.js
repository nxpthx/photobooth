const Config = require('electron-config');
var chokidar = require('chokidar');
var fs = require('fs');
var Mousetrap = require('mousetrap');

window.$ = window.jQuery = require('jquery');


const config = new Config();
var imagePath = config.get('imagePath');

var $previewImage = null;
var imageProcessing = false;

var takePicture = function() {
  cameraProcess = require('child_process').exec;

  var cmd = "/usr/bin/gphoto2 --capture-image-and-download --keep -q --hook-script=/usr/local/bin/postProcessCameraDownload";
  cameraProcess.exec(
    cmd,
    {
      cwd: imagePath
    }, 
    (err, stdout, stderr) => {
      if (err) {
        imageProcessing = false
        $('#processingLayer').hide();
      }
    }
  );
}

var currentWatcher = chokidar.watch(imagePath + "/currentImage.txt", {
	persistent: true
});
var timer;

currentWatcher.on('change', path => {
	fs.readFile(path, 'utf8', function(err, data) {
  		if (err) throw alert(err);
  		if (timer) {
  			window.clearTimeout(timer);
  		}
  		$previewImage.attr('src', imagePath + '/' + data);
      $('#processingLayer').hide();
  		$previewImage.show();
  		timer = window.setTimeout(function() {
  			$previewImage.fadeOut();
        imageProcessing = false;
  		}, 10000)
	});
});


Mousetrap.bind('1', function() {
  if (imageProcessing == false) {
    imageProcessing = true;
    $('#countdownLayer').show();
    window.setTimeout(function(){ 
      $('#countdownLayer').hide();
      $('#processingLayer').show();
      takePicture();
    }, 3000);
  }
  
});


$(document).ready(function() {
	window.$previewImage = $('#previewImage');
});