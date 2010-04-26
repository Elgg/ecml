<?php
/**
 * Language definitions for ECML
 *
 * @package ecml
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.org/
 */

$english = array(
	'ecml' => 'ECML',
	'ecml:help' => 'ECML Help',

	// views
	'ecml:views:annotation_generic_comment' => 'Comments',

	// keywords
	'ecml:keywords:googlemaps:desc' => 'Embed a Google Map.',
	'ecml:keywords:googlemaps:usage' => '[googlemaps src="URL"] Use the link code from Google Maps as the src.',

	'ecml:keywords:slideshare:desc' => 'Embed a Slideshare slide.',
	'ecml:keywords:slideshare:usage' => '[slideshare id="slideshare_id"] Use the Wordpress.com embed code.',

	'ecml:keywords:vimeo:desc' => 'Embed a Vimeo video.',
	'ecml:keywords:videmo:usage' => '[videmo src="URL"] Use a standard Vimeo URL as the source.',

	'ecml:keywords:youtube:desc' => 'Embed a YouTube video.',
	'ecml:keywords:youtube:usage' => '[youtube src="URL"] Use a standard YouTube URL as the source.',

	// keyword help
	'ecml:keywords_title' => 'Keywords',
	'ecml:keywords_instructions' =>
		'Keywords are replaced with content when viewed.  They must be surrounded by
		square brackets ([ and ]).  You can build your own or use the ones listed below.
		Hover over a keyword to read its description.',

	'ecml:keywords_instructions_more' =>
		'
		<p>You can build your own keywords for views and entities.</p>

		<p>[entity: type=type, subtype=subtype, owner=username, limit=number]<br />

		EX: To show 5 blog posts by admin:<br />
		[entity: type=object, subtype=blog, owner=admin, limit=5]</p>

		<p>You can also specify a valid Elgg view:<br />
		[view: elgg_view, name=value]</p>

		<p>Ex: To show a text input with a default value:<br />
		[view: input/text, value=This is a default value]</p>',

	// admin
	'ecml:admin' => 'ECML Permissions',
	'ecml:admin:instruction' =>

'ECML allows users you easily embed views, entities, and 3rd party applications into their content
on your site by using ECML keywords.  There are some ECML keywords that you may want to restrict
in certain areas of your site.  To disable a keyword for a section of your site, check the box in the
grid below.
',

	'ecml:admin:permissions_saved' => 'ECML permissions saved.',
	'ecml:admin:cannot_save_permissions' => 'Cannot save ECML permissions!',
	'ecml:admin:restricted' => 'Restricted',


);

add_translation('en', $english);