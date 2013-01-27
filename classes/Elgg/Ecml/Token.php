<?php

/**
 * Token output from ECML Tokenizer
 *
 * @access private
 */
class Elgg_Ecml_Token {
	/**
	 * @var bool if true, token is plain text, not an ECML tag
	 */
	public $isText = true;

	/**
	 * @var string
	 */
	public $content = "";

	/**
	 * @var string
	 */
	public $keyword = "";

	/**
	 * @var array
	 */
	public $attrs = array();

	/**
	 * @static
	 * @param string $content
	 * @param string $keyword
	 * @param array $attrs
	 * @return Elgg_Ecml_Token
	 */
	static function factory($content, $keyword = '', array $attrs = array()) {
		$token = new self();
		$token->content = $content;
		if ($keyword !== '') {
			$token->isText = false;
			$token->keyword = trim($keyword);
			$token->attrs = $attrs;
		}
		return $token;
	}
}
