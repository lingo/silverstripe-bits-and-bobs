(function($) { // encapsulate jQuery $ variable to prevent interactions with Prototype etc.
$(function() { // Run this code after page load.

	/**
	 * Author: Luke Hudson <lukeletters@gmail.com>
	 * Usage:
	 * 		See ImageRotatorPage.php in code/
	 */

	if (!Array.indexOf) {
		/**
		 * This basic function Array.indexOf is not available on various IE
		 * versions.  Here we define it if it didn't exist.
		 */
		Array.prototype.indexOf = function(obj){
			for(var i=0; i<this.length; i++){
				if(this[i]==obj){
					return i;
				}
			}
			return -1;
		}
	}

	/**
	 * The list of source image URLs
	 */
	var imageURLs = [];

	/**
	 * This array has an index into imageURLs for each pane, which determines
	 * which image is shown in that pane
	 */
	var indexes  = [];

	/**
	 * Convenience func to return a random int between min and max
	 */
	function rRnd(min, max) {
		return Math.floor( Math.random() * (max-min) + min );
	}

	/**
	 * For the given pane, cycle the images once.
	 */
	function cycleImages(whichPane) {
			$('#Rotator_' + whichPane + ' img').attr('src', imageURLs[indexes[whichPane]]);
			indexes[whichPane] = -1;
			indexes[whichPane] = uniqRandIndex(indexes, imageURLs.length);
	}

	/**
	 * A not-too-random random func.  This finds a random index between 0 and
	 * *max*, making sure it's not already used within *arr*
	 * This is used to prevent repeating the same image on different panes at
	 * the same time.
	 */
	function uniqRandIndex(arr, max) {
		if (!max) { return 0; }
		var x;
		do {
			x = rRnd(0, max);
		} while(arr.length && arr.indexOf(x) != -1);
		return x;
	}


	/**
	 * Initial AJAX call retrieves the image URLs list and starts image
	 * cycling.
	 */
	$.getJSON($('#Rotator').data('ajaxref'), function(images) {
		imageURLs = images; // save the list

		// Use setTimeout to run the preloading in a separate 'thread'
		setTimeout(function() {
			// Preload the images.
			for(var i=0; i < images.length; i++) {
				// preload images
				var img = new Image();
				img.src = images[i];
			}
		}, 100);

		var numPanes = $('.imgFrame').length;
		// Set up the indexes for the next images.
		for(var i=0; i < numPanes; i++) {
			indexes[i] = uniqRandIndex(indexes, images.length);
		}

		var curPane = 0; // which pane is currently cycled.

		/**
		 * The main bit!
		 * Cycle images every X milliseconds.
		 */
		var rtID = setInterval(function() {
			if (++curPane >= numPanes) {
				curPane = 0;
			}
			cycleImages(curPane);
		}, $CycleInterval); // $CycleInterval is replaced via javascriptTemplate in ImageRotatorPage::init.
	});

});
})(jQuery);
