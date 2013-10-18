<?php
require_once('functions.php');

$autoload_directories = array(
	//'WPxCore', 'WPxCore/WPxActivity', 'WPxCore/WPxForms', 'WPxCore/WPxMembers', 'WPxCore/WPxPosts', 'WPxCore/WPxComments', 'WPxCore/WPxLog',
	//'WPxMods',
	//'WPxPages',
	'WPxBase', 'WPxQuery', 'WPxPosts', 'WPxLog', 'WPxTerms', 'WPxTaxonomies', 'WPxUsers', 'WPxComments', 'WPxTests'
);

foreach ($autoload_directories as $dir)
	foreach (glob(WPX_PATH . $dir . "/*.php") as $filename)
		require_once($filename);