<?php
/**
 * Provides the ECML service.
 *
 * @package ECML
 */

// be sure to run after other plugins
elgg_register_event_handler('init', 'system', 'ecml_init', 9999);

function ecml_init() {

	// get list of views to process for ECML
	// entries should be of the form 'view/name' => 'View description'
	$default_views = array(
		'output/longtext' => elgg_echo('ecml:view:output_longtext'),
	);
	$views = elgg_trigger_plugin_hook('get_views', 'ecml', null, $default_views);

	foreach ($views as $view => $desc) {
		elgg_register_plugin_hook_handler('view', $view, 'ecml_process_view');
	}

	elgg_register_plugin_hook_handler('unit_test', 'system', 'ecml_unit_test');

	if (!class_exists('Elgg_Ecml_Token')) {
		spl_autoload_register('_ecml_load_class');
	}
}

/**
 * Processes a view output for ECML tags
 *
 * @param string $hook   The name of the hook
 * @param string $name   The name of the view
 * @param string $value  The value of the view
 * @param array  $params The parameters for the view
 * @return string
 */
function ecml_process_view($hook, $name, $value, $params) {
	return _ecml_get_processor()->process($value, array(
		'view' => $name,
		'view_params' => $params,
	));
}

function ecml_unit_test($hook, $type, $value, $params) {
	// dumb strict errors caused by simpletest!
	error_reporting(E_ALL);

	$path = dirname(__FILE__) . '/tests';

	//error_reporting(E_ALL);
	$value[] = "$path/Elgg_Ecml_TokenizerTest.php";
	$value[] = "$path/Elgg_Ecml_ProcessorTest.php";

	return $value;
}

/**
 * @return Elgg_Ecml_Processor
 */
function _ecml_get_processor() {
	static $proc;
	if (null === $proc) {
		$proc = new Elgg_Ecml_Processor(new Elgg_Ecml_Tokenizer());
	}
	return $proc;
}

/**
 * @param string $class
 */
function _ecml_load_class($class) {
	if (0 === strpos($class, 'Elgg_Ecml_')) {
		$file = dirname(__FILE__) . '/classes/' . strtr($class, '_\\', '//') . '.php';
		is_file($file) && (require $file);
	}
}
