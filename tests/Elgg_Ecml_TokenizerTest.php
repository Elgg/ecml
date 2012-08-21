<?php

class Elgg_Ecml_TokenizerTest extends UnitTestCase {

	/**
	 * @var Elgg_Ecml_Tokenizer
	 */
	protected $tk;

	function setUp() {
		$this->tk = new Elgg_Ecml_Tokenizer();
		parent::setUp();
	}

	function testValidTokens() {
		$valids = array(
			'[bar/baz foo="234" bool bool2=true f-.2 pow=\'pow\']' => array(
				'keyword' => 'bar/baz',
				'attrs' => array(
					'foo' => '234',
					'bool' => true,
					'bool2' => true,
					'f-.2' => true,
					'pow' => 'pow',
				),
			),
			'[bar.123  cat="fig\\"ht"]' => array(
				'keyword' => 'bar.123',
				'attrs' => array(
					'cat' => 'fig"ht',
				),
			),
		);
		foreach ($valids as $text => $test) {
			$tokens = $this->tk->getTokens($text);
			$this->assertTrue(isset($tokens[0]));
			$token = $tokens[0];
			$this->assertIsA($token, 'Elgg_Ecml_Token');
			/* @var Elgg_Ecml_Token $token */
			$this->assertFalse($token->isText);
			$this->assertEqual($token->keyword, $test['keyword']);
			$this->assertIdentical($token->attrs, $test['attrs']);
		}
	}

	function testMultibyteToken() {
		$tokens = $this->tk->getTokens('[foo 日本語="日本\\"語"]');
		$this->assertTrue(
			isset($tokens[0])
			&& $tokens[0]->keyword == 'foo'
			&& $tokens[0]->attrs == array('日本語' => '日本"語'));
	}

	function testInvalidTokens() {
		$invalids = array(
			'[foo a="b]',
			'[foo a="b\\"]',
		);
		foreach ($invalids as $text) {
			$tokens = $this->tk->getTokens($text);
			$this->assertTrue(isset($tokens[0]));
			$token = $tokens[0];
			$this->assertIsA($token, 'Elgg_Ecml_Token');
			/* @var Elgg_Ecml_Token $token */
			$this->assertTrue($token->isText);
		}
	}

	function testMultipleTokens() {
		$tokens = $this->tk->getTokens('Hello [foo bar="bar"], this [cat do="g] is [/foo].');
		$expected = array(
			'Hello ',
			array('foo', array('bar' => 'bar')),
			', this ',
			'[cat do="g]',
			' is ',
			array('/foo', array()),
			'.'
		);
		if ($this->assertEqual(count($tokens), count($expected))) {
			foreach ($expected as $i => $data) {
				if (is_array($data)) {
					$this->assertFalse($tokens[$i]->isText);
					$this->assertEqual($tokens[$i]->keyword, $data[0]);
					$this->assertEqual($tokens[$i]->attrs, $data[1]);
				}
			}
		}
	}
}
