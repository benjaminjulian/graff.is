(function () {
    /* Imports */
    var $j = this.JpegMeta.JpegFile;

    function strComp(a, b) {
	    return (a > b) ? 1 : (a == b) ? 0 : -1;
    }

    function loadFiles(files) {
        var dataurl_reader = new FileReader();

        function display(data, filename) {
            var jpeg = new $j(data, filename);
            console.log("GPS:"+jpeg.gps.longitude +","+jpeg.gps.latitude + ";" + jpeg.exif.DateTimeOriginal.value);
        }

        dataurl_reader.onloadend = function() {
            display(atob(this.result.replace(/^.*?,/,'')), files[0]);
        }

        dataurl_reader.readAsDataURL(files[0]);
    }

    window.onload = function() {
	var file_el = $("#fileWidget")[0];
	file_el.addEventListener("change", function() { loadFiles(this.files); }, true);
    }
    /* No exports */
})();