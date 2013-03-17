<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;

$versions = GitVersionCollection::create(dirname(__FILE__) . '/src')
	->addFromTags('0.*')
//	->add('master', 'master branch')
;

return new Sami(dirname(__FILE__) . '/src', array(	'title' => 'Notificare API',
													'build_dir' => dirname(__FILE__) . '/../notificare-api-docs/%version%',
													'cache_dir' => sys_get_temp_dir() . '/sami-notificare-cache/%version%',
													'versions' => $versions));
