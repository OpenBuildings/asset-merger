<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Precompile the assets for the current environment. This is useful in deploy/build setups where you need to generate the assets before you make the code live.
 * 
 * @param string view the name of the view to run in order to generate the assets
 * @package    Despark/asset-merger
 * @author     Ivan Kerin
 * @copyright  (c) 2011-2012 Despark Ltd.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Task_Asset_Generate extends Minion_Task {

	protected $_options = array(
		'view' => FALSE, 
	);

	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('view', 'not_empty')
			->rule('view', 'Kohana::find_file', array('views', ':value'));
	}

	protected function _execute(array $options)
	{
		$view = View::factory($options['view']);
		$view->render();

		Minion_CLI::write('View rendered, assets should be generated');
	}
}