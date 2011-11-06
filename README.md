Asset Merger
============

This is a Kohana 3 module used to merge and and preprocess css and javascript files.

Quick Example: 

	<?php echo Assets::factory('main')
		->css('site/homepage.css.less')
		->css('main.css.less', 'cssmin')
		->css('notifications.css')
		->js("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js")
		->js_block("window.asset_merger = true;")
		->js("functions.js", "jsmin")
	?>

This will output this in Development:
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript">window.asset_merger = true;</script>
	<script type="text/javascript" src="/assets/js/functions.js?1320415817"></script>
	<link type="text/css" href="/assets/css/site/homepage.css.less?1320504157" rel="stylesheet" />
	<link type="text/css" href="/assets/css/main.css.less?1320508620" rel="stylesheet" />
	<link type="text/css" href="/assets/css/notifications.css?1320227001" rel="stylesheet" />  					
		
And this in Production:

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="/assets/js/main.js?1320415817"></script>
	<link type="text/css" href="/assets/css/main.css?1320508620" rel="stylesheet" />

Virtual folders
---------------

Asset merger combines all your asset files and puts them in a single folder in a publicly accessable directory. This way you can have your assets wherever you want, even outside the document root. When you use the js / css methods of the Asset class it searches the directories you've configured and caches the files to your web folder - both the merged and the individual files

Configuration
-------------

config/asset-merger.php config file has a bunch of settings. Typical config file looks like this:

	return array(
		'merge' => Kohana::PRODUCTION,
		'folder' => "assets",
		"load_paths" => array(
			Assets::JAVASCRIPT => DOCROOT.'js'.DIRECTORY_SEPARATOR,
			Assets::STYLESHEET => DOCROOT.'css'.DIRECTORY_SEPARATOR,
		),
		'processor' => array(
			Assets::STYLESHEET => 'cssmin'
		)
	);

__merge__:
Define the environments that will have a merged version of the assets. This can either be an array or a single environment constant.

__folder__:
The URL to the folder that will contain the assets. Will be automatically generated if it's not present in the filesystem. Must be inside DOCROOT.

__load_paths__:
Where to search for files. The css and js files have different directories. Each can be an array of directories.

__processor__:
The default processor to be used on each type. This can be overriden for any individual file.

Engines
-------

The assets class does some processing of the files based on the filename extension. For example if the file ends with .less it will be put through the lessphp processor, And if it ends with php - through raw php. You can also chain Engines so

	main.css.less.php

Will first pass it through php then through lessphp

__Available engines__ :

 - less - uses Lessphp
 - coffee - Php port of Coffeescript
 - sass - PHamlP's Sass parser
 - php - raw php

Processors
----------

Each type and individual file can be set to be processed by a processor - this is done mainly to reduse its size. 

__Available engines__ :

 - cssmin - [http://code.google.com/p/cssmin/](http://code.google.com/p/cssmin/)
 - css_compressor - [http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php](http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php)
 - jsmin - [http://code.google.com/p/jsmin-php/](http://code.google.com/p/jsmin-php/)
 - jsminplus - [http://code.google.com/p/minify/source/browse/trunk/min/lib/JSMinPlus.php](http://code.google.com/p/minify/source/browse/trunk/min/lib/JSMinPlus.php)


Assets Class
------------

The Assets class exposes methods to add assets to it's queue which it then renderer when you convert it to a string (with echo for example)

	function css($file, $processor = null)
	function js($file, $processor = null)

Add an asset file to the queue. Thy will be outputed in the order that's given. Or if the files are merged, will appear in the merged files in that order. The second parameter overrides the default processor. You can pass FALSE to disable processing

	function css_block($content, $processor = null)
	function js_block($content, $processor = null)

Those methods place arbitrary content inside the que to be rendered. This is useful when you want javascript/css to appear in an exact place in your assets loading.

	function merge(bool $merge)

Forse merging of the files - useful for testing

	function render()

Render the whole queue, this is called automatically on __toString




	


	