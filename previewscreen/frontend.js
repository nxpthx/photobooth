const Config = require('electron-config');
var chokidar = require('chokidar');
var fs = require('fs');
var $ = require('jquery');

const config = new Config();
var imagePath = config.get('imagePath');

var $previewImage = null;


var currentWatcher = chokidar.watch(imagePath + "/currentImage.txt", {
	persistent: true
});
var timer;

currentWatcher.on('change', path => {
	fs.readFile(path, 'utf8', function(err, data) {
  		if (err) throw err;
  		if (timer) {
  			window.clearTimeout(timer);
  		}
  		$previewImage.attr('src', imagePath + '/' + data);
  		$previewImage.show();
  		timer = window.setTimeout(function() {
  			$previewImage.fadeOut();
  		}, 10000)
	});
});

$(document).ready(function() {
	window.$previewImage = $('#previewImage')
});