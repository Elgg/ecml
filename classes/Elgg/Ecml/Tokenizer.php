<?php

/**
 * Generate a list of ECML tokens
 *
 * @access private
 */
class Elgg_Ecml_Tokenizer {

	const TAG_REGEX = '~\\[([a-z0-9\\./]+)([^\\]]+)?\\]~';
	const ATTR_SEPARATOR = ' ';
	const ATTR_OPERATOR = '=';
	const DELIMITER = 'NWMwYjc0ZjhiYTBjYmE2NzgwMmFkZTQzNmYyZDcxMWY3NGFjMDI1ZA';

	/**
	 * @var Elgg_Ecml_Token[]
	 */
	protected $replacedTokens;

	/**
	 * @param string $text
	 * @return Elgg_Ecml_Token[] array of ECML tokens
	 */
	public function getTokens($text) {
		$this->replacedTokens = array();

		$text = preg_replace_callback(Elgg_Ecml_Tokenizer::TAG_REGEX, array($this, 'replaceMatch'), $text);
		$pieces = explode(Elgg_Ecml_Tokenizer::DELIMITER, $text);

		$tokens = array();
		$last = count($pieces) - 1;
		foreach ($pieces as $i => $piece) {
			if ($piece !== '') {
				$tokens[] = Elgg_Ecml_Token::factory($piece);
			}
			if ($i !== $last) {
				$tokens[] = $this->replacedTokens[$i];
			}
		}
		$this->replacedTokens = array();
		return $tokens;
	}

	/**
	 * Render an ECML tag
	 *
	 * @param array $matches Array of string matches for a particular tag
	 * @return string
	 */
	protected function replaceMatch($matches) {
		// matches = [full tag, keyword, attributes?]
		$attributes = array();
		if (isset($matches[2])) {
			$success = true;
			$attributes = $this->tokenizeAttributes($matches[2], $success);
			if (!$success) {
				// failed to parse attributes, make a plain text token
				$this->replacedTokens[] = Elgg_Ecml_Token::factory($matches[0]);
				return Elgg_Ecml_Tokenizer::DELIMITER;
			}
		}
		$token = Elgg_Ecml_Token::factory($matches[0], $matches[1], $attributes);
		$this->replacedTokens[] = $token;
		return Elgg_Ecml_Tokenizer::DELIMITER;
	}

	/**
	 * Tokenize the ECML tag attributes
	 *
	 * @param string $string Attribute string
	 * @param bool $success
	 * @return array
	 */
	protected function tokenizeAttributes($string, &$success = null) {
		$success = true;
		$string = trim($string);
		if (empty($string)) {
			return array();
		}

		$attributes = array();
		$pos = 0;
		$char = elgg_substr($string, $pos, 1);

		// working var for assembling name and values
		$operand = $name = '';

		while ($char !== false && $char !== '') {
			switch ($char) {
				// handle quoted names/values
				case '"':
				case "'":
					$quote = $char;

					$next_char = elgg_substr($string, ++$pos, 1);
					while ($next_char != $quote) {
						// note: mb_substr returns "" instead of false...
						if ($next_char === false || $next_char === '') {
							// no matching quote. bail.
							$success = false;
							return array();

						} elseif ($next_char === '\\') {
							// allow escaping quotes
							$after_escape = elgg_substr($string, $pos + 1, 1);
							if ($after_escape === $quote) {
								$operand .= $quote;
								$pos += 2; // skip escape and quote
								$next_char = elgg_substr($string, $pos, 1);
								continue;
							}
						}
						$operand .= $next_char;
						$next_char = elgg_substr($string, ++$pos, 1);
					}
					break;

				case self::ATTR_SEPARATOR:
					$this->setAttribute($operand, $name, $attributes);
					break;

				case self::ATTR_OPERATOR:
					// save name, switch to value
					$name = $operand;
					$operand = '';
					break;

				default:
					$operand .= $char;
					break;
			}

			$char = elgg_substr($string, ++$pos, 1);
		}

		// need to get the last attr
		$this->setAttribute($operand, $name, $attributes);

		return $attributes;
	}

	protected function setAttribute(&$operand, &$name, &$attributes) {
		// normalize true and false
		if ($operand == 'true') {
			$operand = true;
		} elseif ($operand == 'false') {
			$operand = false;
		}
		if ($name !== '') {
			$attributes[$name] = $operand;
			$operand = $name = '';
		} elseif ($operand !== '') {
			// boolean attribute (no value)
			$attributes[$operand] = true;
			$operand = '';
		}
	}
}
