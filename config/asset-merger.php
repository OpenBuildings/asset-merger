<?php defined('SYSPATH') or die('No direct script access.');

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