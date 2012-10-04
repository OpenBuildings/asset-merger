**Table of Contents**  *generated with [DocToc](http://doctoc.herokuapp.com/)*

- [Asset Merger](#asset-merger)
	- [Blog Post](#blog-post)
	- [Virtual folders](#virtual-folders)
	- [Remote Files](#remote-files)
	- [IE Conditional Comments](#ie-conditional-comments)
	- [Configuration](#configuration)
	- [Engines](#engines)
	- [Processors](#processors)
	- [Assets Class](#assets-class)
	- [Extending](#extending)

Asset Merger
============

Blog Post
---------

[http://ivank.github.com/blog/2011/11/kohana-assets-done-right/](http://ivank.github.com/blog/2011/11/kohana-assets-done-right/)

This is a Kohana 3 module used to merge and and preprocess css and javascript files.

Quick Example: 

``` php
	<?php echo Assets::factory('main')
		->css('site/homepage.css.less')
		->css('main.css.less', array('processor' => 'cssmin')
		->css('ie.css.less', array('condition' => 'gte IE 7')
		->css('notifications.css')
		->js("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js")
		->js_block("window.asset_merger = true;")
		->js("functions.js", "jsmin")
	?>
```

This will output this in Development:
	
``` html
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript">window.asset_merger = true;</script>
	<script type="text/javascript" src="/assets/js/functions.js?1320415817"></script>
	<link type="text/css" href="/assets/css/site/homepage.css.less?1320504157" rel="stylesheet" />
	<link type="text/css" href="/assets/css/main.css?1320508620" rel="stylesheet" />
	<link type="text/css" href="/assets/css/notifications.css?1320227001" rel="stylesheet" />
	<!--[IF gte IE 7]><link type="text/css" href="/assets/css/ie.css?1320227001" rel="stylesheet" /><![endif]-->
```

And this in Production:

``` html
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="/assets/js/main.js?1320415817"></script>
	<link type="text/css" href="/assets/css/main.css?1320508620" rel="stylesheet" />
	<!--[IF gte IE 7]><link type="text/css" href="/assets/css/ie.css?1320227001" rel="stylesheet" /><![endif]-->
```

Virtual folders
---------------

Asset merger combines all your asset files and puts them in a single folder in a publicly accessible directory. This way you can have your assets wherever you want, even outside the document root. When you use the js / css methods of the Asset class it searches the directories you've configured and caches the files to your web folder - both the merged and the individual files.

Remote Files
------------

Asset merger does not do anything to remote files (starting with http://) just adds an html link/script tag to that resource.

IE Conditional Comments
-----------------------

It's a common practice to have conditional comments for CSS / JS files specifically for IE to fight some of it shortcomings. This is supported by asset merger. Assets class methods support a 'condition' option which will wrap the link / script tag in a IE conditional comment.

``` php
	<?php echo Assets::factory('main')
		->css('site/homepage.css.less')
		->css('ie.css.less', array('condition' => 'gte IE 7'))
		->css('notifications.css')
		->js_block("window.asset_merger = true;", array('condition' => 'gte IE 7'))
		->js("functions.js", "jsmin")
	?>
```

JS Local Fallback
-----------------

Sometimes you need to have a local fallback to your external javascript file (from google js apis for example). You can already achieve this manually with adding a js_block, but there is a cleaner way of doing this. You just set the "fallback" option and asset-merger will do this for you. For example:

``` php
	<?php echo Assets::factory('main')
		->css('site/homepage.css.less')
		->js("http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js", array(
			'fallback' => array('window.jQuery', '/js/plugins/jquery-1.7.2.min.js')
		))
	?>
```

The first element of the fallback array is the check if jquery is loaded (`window.jQuery`). The second is the local path to the replacement file. This will generate:

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2_/jquery.min.js">
	</script><script type="text/javascript">
	(window.jQuery) || document.write('<script type="text/javascript" src="/js/plugins/jquery-1.7.2.min.js"><\/script>')
	</script>


Configuration
-------------

config/asset-merger.php configuration file has a bunch of settings. Typical configuration file looks like this:

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
The URL to the folder that will contain the assets. Will be automatically generated if it's not present in the file system. Must be inside DOCROOT.

__load_paths__:
Where to search for files. The CSS and JS files have different directories. Each can be an array of directories.

__processor__:
The default processor to be used on each type. This can be overridden for any individual file.

Engines
-------

The assets class does some processing of the files based on the filename extension. For example if the file ends with .less it will be put through the LESSPHP processor, And if it ends with PHP - through raw PHP. You can also chain Engines so

	main.css.less.php

Will first pass it through PHP then through LESSPHP

__Available engines__ :

 - less - [http://leafo.net/lessphp/](http://leafo.net/lessphp/)
 - coffee - [https://github.com/alxlit/coffeescript-php](https://github.com/alxlit/coffeescript-php)
 - sass - [http://code.google.com/p/phamlp/](http://code.google.com/p/phamlp/)
 - php - raw php

Processors
----------

Each type and individual file can be set to be processed by a processor - this is done mainly to reduce its size. 

__Available engines__ :

 - cssmin - [http://code.google.com/p/cssmin/](http://code.google.com/p/cssmin/)
 - csscompressor - [http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php](http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php)
 - jsmin - [http://code.google.com/p/jsmin-php/](http://code.google.com/p/jsmin-php/)
 - jsminplus - [http://code.google.com/p/minify/source/browse/trunk/min/lib/JSMinPlus.php](http://code.google.com/p/minify/source/browse/trunk/min/lib/JSMinPlus.php)


Assets Class
------------

The Assets class exposes methods to add assets to it's queue which it then renderer when you convert it to a string (with echo for example)

	function css($file, $processor = null)
	function js($file, $processor = null)

Add an asset file to the queue. Thy will be outputted in the order that's given. Or if the files are merged, will appear in the merged files in that order. The second parameter overrides the default processor. You can pass FALSE to disable processing

	function css_block($content, $processor = null)
	function js_block($content, $processor = null)

Those methods place arbitrary content inside the queue to be rendered. This is useful when you want javascript/css to appear in an exact place in your assets loading.

	function merge(bool $merge)

Force merging of the files - useful for testing

	function process(bool $process)

Force processing of the files - useful for testing


	function render()

Render the whole queue, this is called automatically on __toString



Extending
---------

You can Add your own engines and processors easily by adding a class inside classes/asset/engine, or classes/asset/processor respectfully. The class must have a static method process which will return the desired result.

Minion Task
-----------

If you have a build process that requires you to prebuild the views for your production server you can do that with the included kohana-minion task

	./minion asset:generate --view={view}

Where --view is the view where your assets are rendered. This way the render/merge code can be executed and all the assets generated from the command line. If you have a seperate merging strategies for each environment you can use environment variables of the command line to set the appropriate Kohana environment (this is accually useful in general). 

	PHP_APP_ENV=production ./minion asset:generate --view={view}

For this to work you will need to have this in your bootstrap.php file:

	if (isset($_SERVER["PHP_APP_ENV"]))
	{
		Kohana::$environment = constant("Kohana::".strtoupper($_SERVER["PHP_APP_ENV"]));	
	}


License
-------

jamaker is Copyright © 2012 Despark Ltd. developed by Ivan Kerin. It is free software, and may be redistributed under the terms specified in the LICENSE file.




	