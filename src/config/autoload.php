<?php

/**
 *
 * Contao extension opengraph
 *
 * @copyright  Martin Kozianka 2012-2014 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */

ClassLoader::addClasses(array(
    'OpenGraph\OpenGraph'              => 'system/modules/opengraph/classes/OpenGraph.php',
    'OpenGraph\OpenGraphHooks'         => 'system/modules/opengraph/classes/OpenGraphHooks.php',
    'floIcon'                          => 'system/modules/opengraph/classes/floIcon.php',
));