<?php

class Elgg_Ecml_ProcessorTest extends UnitTestCase {

	/**
	 * @var Elgg_Ecml_Processor
	 */
	protected $proc;

	function setUp() {
		// @todo mock the tokenizer
		$this->proc = new Elgg_Ecml_Processor(new Elgg_Ecml_Tokenizer());
		elgg_register_plugin_hook_handler('view', 'output/text', 'ecml_process_view');
		parent::setUp();
	}

	function testProcess() {
		$text = '[footnote]I\'m a footnote.[/footnote]Hello [tag bar="bar"], this [tag do="g].';

		elgg_register_plugin_hook_handler('prepare:tokens', 'ecml', array($this, 'ecmlFootnote'));
		elgg_register_plugin_hook_handler('render:tag', 'ecml', array($this, 'ecmlTag1'));

		$output = $this->proc->process($text, array('foo' => 'bar'));
		$expected = 'Hello a:3:{s:3:"foo";s:3:"bar";s:7:"keyword";s:3:"tag";s:10:"attributes";a:1:{s:3:"bar";s:3:"bar";}}, this [tag do="g].I\'m a footnote.';
		$this->assertEqual($output, $expected);
	}

	function testViewSystem() {
		$text = '[footnote]I\'m a footnote.[/footnote]Hello [br clear=all], this [br inv="alid].';

		elgg_register_plugin_hook_handler('prepare:tokens', 'ecml', array($this, 'ecmlFootnote'));
		elgg_register_plugin_hook_handler('render:br', 'ecml', array($this, 'ecmlBr'));

		// @todo figure out what to do about views that mangle the ECML :/
		$output = elgg_view('output/text', array('value' => $text));
		$expected = 'Hello <br clear="all">, this <br inv="&quot;alid">.I&#039;m a footnote.';
		$this->assertEqual($output, $expected);
	}

	/**
	 * @param string $hook
	 * @param string $type
	 * @param string $value
	 * @param array $params
	 * @return string
	 */
	function ecmlTag1($hook, $type, $value, $params) {
		return serialize($params);
	}

	/**
	 * @param string $hook
	 * @param string $type
	 * @param string $value
	 * @param array $params
	 * @return string
	 */
	function ecmlBr($hook, $type, $value, $params) {
		return '<br ' . elgg_format_attributes($params['attributes']) . '>';
	}

	/**
	 * Example ECML plugin that works on full token set
	 *
	 * @param string $hook
	 * @param string $type
	 * @param Elgg_Ecml_Token[] $value
	 * @param array $params
	 * @return array
	 */
	function ecmlFootnote($hook, $type, $value, $params) {
		$output = array();
		$foot = array();

		$inElement = false;
		foreach ($value as $i => $token) {
			/* @var Elgg_Ecml_Token $token */
			if ($inElement) {
				if ($token->keyword === '/footnote') {
					$inElement = false;
				} else {
					array_push($foot, $token);
				}
			} else {
				if ($token->keyword === 'footnote') {
					$inElement = true;
				} else {
					array_push($output, $token);
				}
			}
		}
		array_splice($output, count($output), 0, $foot);
		return $output;
	}
}
