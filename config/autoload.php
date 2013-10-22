<?php

/**
 *
 * Contao extension opengraph
 *
 * @copyright  Martin Kozianka 2012-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */


ClassLoader::addClasses(array(
	'OpenGraphNewsReader'    => 'system/modules/opengraph/classes/OpenGraphNewsReader.php',
	'OpenGraphHooks'         => 'system/modules/opengraph/classes/OpenGraphHooks.php',
	'OpenGraph'              => 'system/modules/opengraph/classes/OpenGraph.php',
));


TemplateLoader::addFiles(array(

));
