<?php
/**
 * Language definitions for ECML
 *
 */

$english = array(
	'ecml:view:output_longtext' => 'Longtext Output',
	'ecml:Exception:InvalidToken' => 'Type of items returned from [prepare:tokens, ecml] hook must string or Elgg_Ecml_Token',
	'ecml:Exception:InvalidTokenList' => '[prepare:tokens, ecml] hook must return an array',
);

add_translation('en', $english);
