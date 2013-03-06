<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_Asset_Processor_Yuijs {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		$tmp_dir = sys_get_temp_dir();

		// Ensure we have a trailing slash at the end.
		if ( ! preg_match('/\/$/', $tmp_dir))
		{
			$tmp_dir = $tmp_dir . '/';
		}

		// Ensure the dir is writable.
		if ( ! is_writable($tmp_dir)) 
		{
			throw new Kohana_Exception('Temp directory is not writable.');
		}

		// Our temp file.
		$tmp_file = $tmp_dir . '_tmp_compressed_file';
		file_put_contents($tmp_file, $content);

		// Include the processor
		$jar = Kohana::find_file('vendor/yuicompressor', 'yuicompressor-2.4.8pre', 'jar');

		// Build our command.
		$cmd = 'java -jar '.
			escapeshellarg($jar).
			' --type="js" -o '.
			escapeshellarg($tmp_file).' '.
			escapeshellarg($tmp_file);

		// Execute, grab contents and cleanup.
		exec($cmd);
		$contents = file_get_contents($tmp_file);
		unlink($tmp_file);
		return $contents;
	}
}