<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;

$versions = GitVersionCollection::create(dirname(__FILE__) . '/src')
	->add('master', 'master branch')
	->addFromTags('1.*')
;

return new Sami(dirname(__FILE__) . '/src', array(	'title' => 'Notificato API',
													'build_dir' => dirname(__FILE__) . '/../notificato-apidocs/%version%',
													'cache_dir' => dirname(__FILE__) . '/../notificato-apidocs/cache/%version%',
													'versions' => $versions));
