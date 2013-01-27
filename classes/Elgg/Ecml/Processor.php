<?php

/**
 * Turn ECML markup into text via plugin hooks
 *
 * @access private
 */
class Elgg_Ecml_Processor {

	/**
	 * @var Elgg_Ecml_Tokenizer
	 */
	protected $tokenizer;

	/**
	 * @param Elgg_Ecml_Tokenizer $tokenizer
	 */
	public function __construct(Elgg_Ecml_Tokenizer $tokenizer) {
		$this->tokenizer = $tokenizer;
	}

	/**
	 * @param string $text
	 * @param array $context info to pass to the plugin hooks
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	public function process($text, $context = array()) {
		$tokens = $this->tokenizer->getTokens($text);

		// allow processors that might need to see all the tokens at once
		$tokens = elgg_trigger_plugin_hook("prepare:tokens", "ecml", $context, $tokens);
		if (!is_array($tokens)) {
			throw new Exception(elgg_echo('ecml:Exception:InvalidTokenList'));
		}

		// process tokens in isolation
		$output = '';
		foreach ($tokens as $token) {
			if (is_string($token)) {
				$output .= $token;
			} elseif ($token instanceof Elgg_Ecml_Token) {
				/* @var Elgg_Ecml_Token $token */
				if ($token->isText) {
					$output .= $token->content;
				} else {
					$params = array_merge($context, array(
						'keyword' => $token->keyword,
						'attributes' => $token->attrs,
					));
					$output .= elgg_trigger_plugin_hook("render:{$token->keyword}", "ecml", $params, $token->content);
				}
			} else {
				throw new Exception(elgg_echo('ecml:Exception:InvalidToken'));
			}
		}
		return $output;
	}
}
