<?php

/**
 * Model page type showing use of the ImageRotator.
 *
 * You can either extend this class for your own use, or copy the relevant 
 * parts out as needed.
 *
 *
 * Installation:
 * 	1. Copy this code, and the javascript ImageRotator.js to your mysite folder.
 * 	2. Make sure that $javascript_path (see below) is correctly set. Either 
 * 	   change the code here, or change it in your _config.php using 
 * 	   ImageRotatorPage::$javascript_path = 'x/y/z/';
 * 	3. Copy the ImageRotator.ss tmeplate to your templates/Includes/ folder and customise as you like.
 * 	   Important:  Keep the CSS IDs and SilverStripe variable intact or it 
 * 	   won't work.  If you add more image panes, make sure give them CSS IDs 
 * 	   like the others.  Eg  id="Rotator-4"
 * 	4. Enjoy and profit!
 *
 * 	Author: Luke Hudson / Lingo
 * 	
 * 	Please post issues here if you have problems:
 *	  	http://github.com/lingo/silverstripe-bits-and-bobs/issues
 *
 */
class ImageRotatorPage extends Page {
	static $db = array(
		'CenterText'	=> 'Varchar(100)', // Optional text to marquee in center of images
		'CycleInterval' => 'Int', // Cycle images every X milliseconds
		'ImageWidth'	=> 'Int', // Width of images in paens
		'ImageHeight'	=> 'Int'  // Ditto for height
	);

	static $has_one = array(
		'ImageFolder'	=> 'Folder'
	);

	/**
	 * This is shown if an image is requested that isn't found.
	 * You can customise this from your _config.php file as below
	 *   e.g.  ImageRotatorPage::$noimage_text = "Not found";
	 */
	public static $noimage_text = '[No image provided'];

	/**
	 * Alter this to reflect where you install ImageRotator.js
	 */
	public static $javascript_path = 'mysite/javascript/';

	/**
	 * Default dimensions for resampled images.
	 * You'll want to change these from your _config.php
	 */
	public static $default_width = 495;
	public static $default_height = 168;

	/**
	 * Cache for getImages
	 */
	private $images = array();


	/**
	 * Provide extra UI fields for our extra datafields
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Content', new Tab('ImageRotator'));
		$fields->addFieldToTab('Root.Content.ImageRotator', new TextField('CenterText', 'Text for center of image rotation'));
		$fields->addFieldToTab('Root.Content.ImageRotator',
			new TreeDropdownField('ImageFolderID', 'Source folder for rotating images', 'Folder')
		);
		$fields->addFieldToTab('Root.Content.ImageRotator', new NumericField('Width', 'Image width'));
		$fields->addFieldToTab('Root.Content.ImageRotator', new NumericField('Height', 'Image height'));
		return $fields;
	}

	/**
	 * Proviudes a method of retreiving the URL for an image by index
	 * @param int $n -  zero-based index for the image.
	 * @return string URL of resampled image, or default text from self::$noimage_text
	 */
	public function ImageIdx($n) {
		$img = $this->getImages(10);
		if ($img && count($img) > $n) {
			return $img[$n];
		}
		return self::$noimage_text;
	}

	/**
	 * Retrieve a list of the URLs for resampled images from our ImageFolder
	 * 
	 * @param int $limit	-	Limit how many we return.
	 * @param bool $cache	-	Whether to cache the results, or  recalculate.
	 * @return array		- 	Array of URL strings for the images.
	 */
	public function getImages($limit = null, $cache = true) {
		$folder = $this->ImageFolder();
		// Use cached results if poss.
		if ($cache && $this->images) {
			return $this->images;
		}
		// Make sure ImageFolder exists, or return whatver we got last time.
		if (!$folder) {
			return $this->images;
		}

		$width = $this->ImageWidth || self::$default_width;
		$height = $this->ImageHeight || self::$default_height;

		$i = 0; // track against $limit
		$items = $folder->myChildren();

		if (!$items) {
			// Empty folder, let's give up.
			return array();
		}
		foreach($items as $img) {
			// Ignore non-images.
			if ($img->ClassName != 'Image') {
				continue;
			}
			// Format the image
			$rszImg = $img->getFormattedImage('CroppedImage', $width, $height);
			if ($rszImg) {
				$this->images[] = $rszImg;
			}
			if ($limit && ++$i >= $limit) {
				break;
			}
		}
		return $this->images;
	}
}

/**
 * Controller for the page as per SilverStripe norms
 */
class ImageRotatorPage_Controller extends Page_Controller {
	static $allowed_actions = array(
		'index', 'imagenames'
	);

	/**
	 * If this is not installed in mysite you'll need to adjust the path below.
	 */
	public function init() {
		parent::init();
		Requirements::javascriptTemplate(ImageRotatorPage::$javascript_path . 'ImageRotator.js', array('CycleInterval' => $this->CycleInterval));
	}
	
	/**
	 * Åjax entrypoint to retrieve a list of image URLs
	 */
	public function imagenames() {
		if (!Director::is_ajax()) {
			Director::redirect($this->Link());
			return;
		}
		$names = array_map(create_function('$o', 'return $o->URL;'), $this->getImages());
		return Convert::array2json($names);
	}

	/**
	 * Return a link for the AJAX calls.  This is handy for template use.
	 * In my templates, I attach it to an HTML5 data- attribute, for retrieval via javascript.
	 * @see templates/Layout/ImageRotatorPage.ss
	 */
	public function getAjaxLink() {
		return Director::absoluteURL($this->Link() . 'imagenames');
	}
}
