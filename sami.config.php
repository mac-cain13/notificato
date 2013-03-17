<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;

$versions = GitVersionCollection::create(dirname(__FILE__) . '/src')
	->add('master', 'master branch')
	->addFromTags('0.*')
;

return new Sami(dirname(__FILE__) . '/src', array(	'title' => 'Notificare API',
													'build_dir' => dirname(__FILE__) . '/../notificare-apidoc/%version%',
													'cache_dir' => dirname(__FILE__) . '/../notificare-apidoc/cache/%version%',
													'versions' => $versions));
