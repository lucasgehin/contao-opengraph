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
 
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][]          = 'add_opengraph_image';
$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']                .= ';{opengraph_legend:hide},add_opengraph_image;'; 
$GLOBALS['TL_DCA']['tl_page']['palettes']['root']                   .= ';{opengraph_legend:hide},add_opengraph_image;';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['add_opengraph_image']  = 'opengraph_image';

// Fields
$GLOBALS['TL_DCA']['tl_page']['fields']['add_opengraph_image'] = array
(
		'label'						=> &$GLOBALS['TL_LANG']['tl_page']['add_opengraph_image'],
		'exclude'					=> true,
		'inputType'					=> 'checkbox',
		'eval'						=> array('submitOnChange' => true),
		'sql' 						=> "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_image'] = array
(
		'label'						=> &$GLOBALS['TL_LANG']['tl_page']['opengraph_image'],
		'exclude'					=> true,
		'inputType'					=> 'fileTree',
		'eval'						=> array('extensions' => 'png,gif,jpg,jpeg', 'files' => true, 'fieldType' => 'radio'),
		'sql'						=> "varchar(255) NOT NULL default ''"
);
