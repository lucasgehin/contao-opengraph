<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2012-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['opengraph_image'] = array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_content']['opengraph_image'],
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50 m12'),
		'sql'                     => "char(1) NOT NULL default ''",
		
);

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addImage'] .= ',opengraph_image';

$GLOBALS['TL_DCA']['tl_content']['palettes']['image']  = 
	str_replace(',caption', ',caption,opengraph_image', $GLOBALS['TL_DCA']['tl_content']['palettes']['image']);
