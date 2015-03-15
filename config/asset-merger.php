<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'merge'      => array(Kohana::PRODUCTION, Kohana::STAGING),
	'folder'     => 'assets',
	'load_paths' => array(
		Assets::JAVASCRIPT => array( DOCROOT.'js'.DIRECTORY_SEPARATOR ),
		Assets::STYLESHEET => array( DOCROOT.'css'.DIRECTORY_SEPARATOR ),
	),
	'processor'  => array(
		Assets::STYLESHEET => 'cssmin',
	),
	'docroot' => DOCROOT
);
