<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_Asset_Processor_Yuicss {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		$tmp_file = tempnam(sys_get_temp_dir(), '_tmp_compressed_file');
		
		// Drop content into temp file.
		file_put_contents($tmp_file, $content);

		// Include the processor
		$jar = Kohana::find_file('vendor/yuicompressor', 'yuicompressor-2.4.8pre', 'jar');

		// Build our command.
		$cmd = 'java -jar '.
			escapeshellarg($jar).
			' --type="css" -o '.
			escapeshellarg($tmp_file).' '.
			escapeshellarg($tmp_file);

		// Execute, grab contents and cleanup.
		exec($cmd);
		$contents = file_get_contents($tmp_file);
		unlink($tmp_file);
		return $contents;
	}
}