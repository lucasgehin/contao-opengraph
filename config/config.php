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

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][]    = array('OpenGraph\OpenGraphHooks', 'addOpenGraphDefinition');
$GLOBALS['TL_HOOKS']['generatePage'][]              = array('OpenGraph\OpenGraphHooks', 'addOpenGraphTags');
$GLOBALS['TL_HOOKS']['parseArticles'][]             = array('OpenGraph\OpenGraphHooks', 'parseArticlesHook');

