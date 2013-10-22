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

$GLOBALS['FE_MOD']['news']['newsreader']            = 'OpenGraphNewsReader';

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][]    = array('OpenGraphHooks', 'addOpenGraphDefinition');
$GLOBALS['TL_HOOKS']['generatePage'][]              = array('OpenGraphHooks', 'addOpenGraphTags');
