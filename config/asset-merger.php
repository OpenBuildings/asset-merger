<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'merge'      => array(Kohana::PRODUCTION, Kohana::STAGING),
	'folder'     => 'assets',
	'load_paths' => array(
		Assets::JAVASCRIPT => DOCROOT.'assets'.DIRECTORY_SEPARATOR,
		Assets::STYLESHEET => DOCROOT.'assets'.DIRECTORY_SEPARATOR
	),
	'processor'  => array(
		Assets::STYLESHEET => 'cssmin',
	),
	'docroot' => DOCROOT
);