<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2015 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2012-2015 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_module']['fields']['opengraph_enable'] = array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['opengraph_enable'],
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50 m12'),
		'sql'                     => "char(1) NOT NULL default ''",
		
);

$GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader']  = 
	str_replace('news_archives', 'news_archives, opengraph_enable', $GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader']);